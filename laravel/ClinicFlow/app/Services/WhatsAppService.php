<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send WhatsApp reminder for appointment
     * 
     * @param Appointment $appointment
     * @return bool
     */
    public function sendAppointmentReminder(Appointment $appointment): bool
    {
        try {
            $message = $this->buildReminderMessage($appointment);
            $phone = $this->formatPhoneNumber($appointment->patient->phone);

            // TODO: Integrate with actual WhatsApp API (e.g., Twilio, WhatsApp Business API)
            // For now, we'll just log the message
            Log::info('WhatsApp Reminder', [
                'phone' => $phone,
                'message' => $message,
                'appointment_id' => $appointment->id,
            ]);

            // Simulate successful sending
            return true;

        } catch (\Exception $e) {
            Log::error('WhatsApp sending failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Build reminder message
     */
    private function buildReminderMessage(Appointment $appointment): string
    {
        $clinicName = $appointment->clinic->name;
        $patientName = $appointment->patient->name;
        $doctorName = $appointment->doctor->name;
        $date = $appointment->appointment_date->format('d M Y');
        $time = date('h:i A', strtotime($appointment->appointment_time));

        return "السلام علیکم {$patientName}!\n\n" .
            "Reminder: You have an appointment tomorrow at {$clinicName}\n\n" .
            "📅 Date: {$date}\n" .
            "⏰ Time: {$time}\n" .
            "👨‍⚕️ Doctor: {$doctorName}\n\n" .
            "Please arrive 10 minutes early.\n" .
            "To cancel or reschedule, please call us.\n\n" .
            "JazakAllah Khair!";
    }

    /**
     * Format phone number for WhatsApp (Pakistan format)
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
