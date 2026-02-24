<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\WhatsAppMessage;
use App\Models\ClinicWhatsappSetting;
use App\Services\WhatsApp\MetaProvider;
use App\Services\WhatsApp\JsApiProvider;
use App\Interfaces\WhatsAppProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Get the appropriate provider for a clinic
     */
    public function getProvider(ClinicWhatsappSetting $settings): WhatsAppProviderInterface
    {
        if ($settings->provider === 'js_api') {
            return app(JsApiProvider::class);
        }

        return app(MetaProvider::class);
    }

    /**
     * Send WhatsApp reminder for appointment
     */
    public function sendAppointmentReminder(Appointment $appointment): bool
    {
        try {
            $settings = $appointment->clinic->whatsappSettings;

            if (!$settings || !$settings->is_active) {
                Log::warning('WhatsApp settings not configured or inactive', [
                    'clinic_id' => $appointment->clinic_id,
                ]);
                return false;
            }

            $phone = $this->formatPhoneNumber($appointment->patient->phone);
            $message = $this->buildReminderMessage($appointment);

            // Dispatch background job
            \App\Jobs\SendWhatsAppMessageJob::dispatch(
                $appointment->clinic_id,
                $phone,
                $settings->message_type,
                ['message' => $message],
                $appointment->id
            );

            return true;
        } catch (\Exception $e) {
            Log::error('WhatsApp dispatch failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Unified send message method called by background jobs
     */
    public function sendSimpleMessage($settings, string $phone, array $params = [], ?int $appointmentId = null): bool
    {
        return $this->getProvider($settings)->sendSimpleMessage($settings, $phone, $params, $appointmentId);
    }

    /**
     * Unified send template message method called by background jobs
     */
    public function sendTemplateMessage(
        $settings,
        string $phone,
        string $templateName,
        array $params = [],
        ?int
        $appointmentId = null
    ): bool {
        return $this->getProvider($settings)->sendTemplateMessage($settings, $phone, $templateName, $params, $appointmentId);
    }

    /**
     * Build reminder message
     */
    private function buildReminderMessage(Appointment $appointment): string
    {
        $settings = $appointment->clinic->whatsappSettings;
        $template = $settings->custom_message;

        if (!$template) {
            $template = "السلام علیکم {patient_name}!\n\n" .
                "Reminder: You have an appointment tomorrow at {clinic_name}\n\n" .
                "📅 Date: {date}\n" .
                "⏰ Time: {time}\n" .
                "👨‍⚕️ Doctor: {doctor_name}\n\n" .
                "Please arrive 10 minutes early.\n" .
                "To cancel or reschedule, please call us.\n\n" .
                "JazakAllah Khair!";
        }

        $replace = [
            '{clinic_name}' => $appointment->clinic->name,
            '{patient_name}' => $appointment->patient->name,
            '{doctor_name}' => $appointment->doctor->name,
            '{date}' => \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y'),
            '{time}' => \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A'),
        ];

        return strtr($template, $replace);
    }

    /**
     * Format phone number for WhatsApp (Pakistan format)
     */
    public function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

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