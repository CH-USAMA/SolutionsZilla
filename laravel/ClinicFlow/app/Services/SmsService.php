<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS reminder for appointment
     * 
     * @param Appointment $appointment
     * @return bool
     */
    public function sendAppointmentReminder(Appointment $appointment): bool
    {
        try {
            $message = $this->buildReminderMessage($appointment);
            $phone = $this->formatPhoneNumber($appointment->patient->phone);

            // TODO: Integrate with actual SMS API (e.g., Twilio, Eocean, etc.)
            // For now, we'll just log the message
            Log::info('SMS Reminder', [
                'phone' => $phone,
                'message' => $message,
                'appointment_id' => $appointment->id,
            ]);

            // Simulate successful sending
            return true;

        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Build reminder message (shorter for SMS)
     */
    private function buildReminderMessage(Appointment $appointment): string
    {
        $clinicName = $appointment->clinic->name;
        $doctorName = $appointment->doctor->name;
        $time = date('h:i A', strtotime($appointment->appointment_time));

        return "Reminder: Your appointment with Dr. {$doctorName} at {$clinicName} is in 2 hours at {$time}. Please arrive on time.";
    }

    /**
     * Format phone number for SMS (Pakistan format)
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add Pakistan country code if not present
        if (!str_starts_with($phone, '92')) {
            if (str_starts_with($phone, '0')) {
                $phone = '92' . substr($phone, 1);
            } else {
                $phone = '92' . $phone;
            }
        }

        return $phone;
    }
}
