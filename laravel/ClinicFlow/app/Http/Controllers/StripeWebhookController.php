<?php

namespace App\Http\Controllers;

use App\Models\BillingLog;
use App\Models\Clinic;
use App\Models\Plan;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends CashierController
{
    /**
     * Handle invoice payment succeeded.
     */
    public function handleInvoicePaymentSucceeded($payload): Response
    {
        $session = $payload['data']['object'];
        $stripeCustomerId = $session['customer'];

        $clinic = Clinic::where('stripe_id', $stripeCustomerId)->first();

        if ($clinic) {
            // Find the plan by price ID
            $priceId = $session['lines']['data'][0]['price']['id'] ?? null;
            $plan = Plan::where('stripe_price_id', $priceId)->first();

            // Create Billing Log
            $billingLog = BillingLog::create([
                'clinic_id' => $clinic->id,
                'amount' => $session['amount_paid'] / 100,
                'payment_gateway' => 'stripe',
                'status' => 'paid',
                'transaction_id' => $session['payment_intent'] ?? $session['id'],
                'plan_id' => $plan ? $plan->id : $clinic->plan_id,
            ]);

            // Activate/Update Clinic Plan if needed
            if ($plan) {
                $clinic->update([
                    'plan_id' => $plan->id,
                    'subscription_status' => 'active',
                ]);
            }

            // Generate Invoice PDF
            app(InvoiceService::class)->generateInvoice($billingLog);

            Log::info("Billing log and invoice created for Clinic ID: {$clinic->id}");
        }

        return $this->successMethod();
    }

    /**
     * Handle customer subscription deleted.
     */
    public function handleCustomerSubscriptionDeleted($payload): Response
    {
        $session = $payload['data']['object'];
        $stripeCustomerId = $session['customer'];

        $clinic = Clinic::where('stripe_id', $stripeCustomerId)->first();

        if ($clinic) {
            $clinic->update([
                'subscription_status' => 'canceled',
            ]);
            Log::info("Subscription canceled for Clinic ID: {$clinic->id}");
        }

        return $this->successMethod();
    }
}
