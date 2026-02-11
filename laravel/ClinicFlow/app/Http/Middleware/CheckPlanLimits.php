<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $clinic = auth()->user()->clinic;

        if (!$clinic || (auth()->user() && auth()->user()->isSuperAdmin())) {
            return $next($request);
        }

        $subscriptionService = app(\App\Services\SubscriptionService::class);

        $canProceed = true;
        $message = "You have reached the limit for this feature on your current plan.";

        switch ($feature) {
            case 'appointments':
                if (!$subscriptionService->canCreateAppointment($clinic)) {
                    throw new \App\Exceptions\PlanLimitReachedException('appointments', "Monthly appointment limit reached. Please upgrade your plan.");
                }
                break;
            case 'users':
                if (!$subscriptionService->canAddUser($clinic)) {
                    throw new \App\Exceptions\PlanLimitReachedException('users', "User limit reached. Please upgrade your plan.");
                }
                break;
            case 'whatsapp':
                if (!$subscriptionService->canSendWhatsApp($clinic)) {
                    throw new \App\Exceptions\PlanLimitReachedException('whatsapp', "WhatsApp reminder quota exceeded for this month.");
                }
                break;
        }

        return $next($request);
    }
}
