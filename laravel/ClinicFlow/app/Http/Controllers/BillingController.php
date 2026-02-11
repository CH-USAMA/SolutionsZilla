<?php

namespace App\Http\Controllers;

use App\Models\BillingRecord;
use App\Models\Clinic;
use App\Models\Plan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;

class BillingController extends Controller
{
    /**
     * Display billing history.
     */
    public function index(SubscriptionService $subscriptionService)
    {
        if (!auth()->user()->isClinicAdmin()) {
            abort(403);
        }

        $clinic = auth()->user()->clinic->load('plan');

        $records = BillingRecord::forClinic($clinic->id)
            ->latest('billing_date')
            ->get();

        $usage = $subscriptionService->getUsageStats($clinic);

        return view('billing.index', compact('records', 'clinic', 'usage'));
    }

    /**
     * Show available plans for upgrading.
     */
    public function plans()
    {
        $plans = Plan::where('is_active', true)->get();
        $currentPlan = auth()->user()->clinic->plan;

        return view('billing.plans', compact('plans', 'currentPlan'));
    }

    /**
     * Start a Stripe Checkout session or directly assign for free plans.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $clinic = auth()->user()->clinic;

        // For free plans (like Testing), assign directly without Stripe
        if (!$plan->stripe_price_id || $plan->price == 0) {
            $clinic->update([
                'plan_id' => $plan->id,
                'subscription_status' => 'active',
            ]);

            return redirect()->route('billing.index')->with('success', "Switched to the \"{$plan->name}\" plan successfully!");
        }

        try {
            return $clinic->newSubscription('default', $plan->stripe_price_id)
                ->checkout([
                    'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}&plan_id=' . $plan->id,
                    'cancel_url' => route('billing.plans'),
                ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Stripe error: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment.
     */
    public function success(Request $request)
    {
        $clinic = auth()->user()->clinic;
        $planId = $request->plan_id;

        if ($planId) {
            $clinic->update([
                'plan_id' => $planId,
                'subscription_status' => 'active',
            ]);
        }

        return redirect()->route('billing.index')->with('success', 'Subscription updated successfully!');
    }
}
