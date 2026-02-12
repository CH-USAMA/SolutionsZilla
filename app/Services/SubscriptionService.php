<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\Appointment;
use App\Models\User;
use App\Models\WhatsappLog;
use App\Models\Plan;
use App\Exceptions\PlanLimitReachedException;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Check if a clinic can create a new appointment.
     *
     * @throws PlanLimitReachedException
     */
    public function canCreateAppointment(Clinic $clinic): bool
    {
        $plan = $clinic->plan;

        if (!$plan) {
            return false;
        }

        if ($plan->max_appointments === 0) {
            return true; // Unlimited
        }

        $count = Appointment::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return $count < $plan->max_appointments;
    }

    /**
     * Check if a clinic can send a new WhatsApp message.
     */
    public function canSendWhatsApp(Clinic $clinic): bool
    {
        $plan = $clinic->plan;

        if (!$plan) {
            return false;
        }

        if ($plan->max_whatsapp_messages === 0) {
            return true; // Unlimited
        }

        $count = WhatsappLog::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->where('direction', 'outgoing')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return $count < $plan->max_whatsapp_messages;
    }

    /**
     * Check if a clinic can send a new SMS message.
     */
    public function canSendSms(Clinic $clinic): bool
    {
        $plan = $clinic->plan;

        if (!$plan) {
            return false;
        }

        if ($plan->max_sms_messages === 0) {
            return true; // Unlimited
        }

        $count = \App\Models\SmsLog::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return $count < $plan->max_sms_messages;
    }

    /**
     * Check if a clinic can add another user.
     */
    public function canAddUser(Clinic $clinic): bool
    {
        $plan = $clinic->plan;

        if (!$plan) {
            return false;
        }

        if ($plan->max_users === 0) {
            return true; // Unlimited
        }

        $count = User::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->count();

        return $count < $plan->max_users;
    }

    /**
     * Get usage statistics for a clinic.
     */
    public function getUsageStats(Clinic $clinic): array
    {
        $plan = $clinic->plan;

        if (!$plan) {
            return [];
        }

        $appointmentsCount = Appointment::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $whatsappCount = WhatsappLog::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->where('direction', 'outgoing')
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $usersCount = User::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->count();

        return [
            'appointments' => [
                'used' => $appointmentsCount,
                'limit' => $plan->max_appointments,
                'percentage' => $plan->max_appointments > 0 ? min(100, ($appointmentsCount / $plan->max_appointments) * 100) : 0,
            ],
            'whatsapp' => [
                'used' => $whatsappCount,
                'limit' => $plan->max_whatsapp_messages,
                'percentage' => $plan->max_whatsapp_messages > 0 ? min(100, ($whatsappCount / $plan->max_whatsapp_messages) * 100) : 0,
            ],
            'users' => [
                'used' => $usersCount,
                'limit' => $plan->max_users,
                'percentage' => $plan->max_users > 0 ? min(100, ($usersCount / $plan->max_users) * 100) : 0,
            ],
        ];
    }
}
