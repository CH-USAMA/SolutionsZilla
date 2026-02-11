<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
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
            // Load clinic WhatsApp settings
            $settings = $appointment->clinic->whatsappSettings;

            if (!$settings || !$settings->is_active) {
                Log::warning('WhatsApp settings not configured or inactive', [
                    'clinic_id' => $appointment->clinic_id,
                ]);
                return false;
            }

            $phone = $this->formatPhoneNumber($appointment->patient->phone);
            $message = $this->buildReminderMessage($appointment);

            // Send via Meta Cloud API based on message type
            if ($settings->message_type === 'text') {
                return $this->sendSimpleMessage(
                    $settings,
                    $phone,
                    ['message' => $message],
                    $appointment->id
                );
            }

            return $this->sendTemplateMessage(
                $settings,
                $phone,
                $settings->default_template,
                ['message' => $message],
                $appointment->id
            );

        } catch (\Exception $e) {
            Log::error('WhatsApp sending failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send simple text message via Meta WhatsApp Cloud API
     */
    public function sendSimpleMessage($settings, string $phone, array $params = [], ?int $appointmentId = null): bool
    {
        $clinicId = $settings->clinic_id;

        // Prepare payload
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phone,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $params['message'],
            ],
        ];

        // Create log entry (pending)
        $log = WhatsappLog::create([
            'clinic_id' => $clinicId,
            'appointment_id' => $appointmentId,
            'direction' => 'outgoing',
            'phone' => $phone,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        try {
            // Call Meta WhatsApp Cloud API
            $response = Http::withToken($settings->access_token)
                ->withHeaders(['ngrok-skip-browser-warning' => 'true'])
                ->withUserAgent('ClinicFlow-WhatsApp/1.0')
                ->post("https://graph.facebook.com/v20.0/{$settings->phone_number_id}/messages", $payload);

            if ($response->successful()) {
                // Update log to sent
                $log->update([
                    'response' => $response->json(),
                    'status' => 'sent',
                ]);

                return true;
            } else {
                // Update log to failed
                $log->update([
                    'response' => $response->json(),
                    'status' => 'failed',
                    'error_message' => $response->body(),
                ]);

                return false;
            }

        } catch (\Exception $e) {
            // Update log to failed
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send template message via Meta WhatsApp Cloud API
     */
    public function sendTemplateMessage($settings, string $phone, string $templateName, array $params = [], ?int $appointmentId = null): bool
    {
        $clinicId = $settings->clinic_id;

        // Prepare payload
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => 'en'],
            ],
        ];

        // Create log entry (pending)
        $log = WhatsappLog::create([
            'clinic_id' => $clinicId,
            'appointment_id' => $appointmentId,
            'direction' => 'outgoing',
            'phone' => $phone,
            'template_name' => $templateName,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        try {
            // Call Meta WhatsApp Cloud API
            $response = Http::withToken($settings->access_token)
                ->post("https://graph.facebook.com/v20.0/{$settings->phone_number_id}/messages", $payload);

            if ($response->successful()) {
                // Update log to sent
                $log->update([
                    'response' => $response->json(),
                    'status' => 'sent',
                ]);

                return true;
            } else {
                // Update log to failed
                $log->update([
                    'response' => $response->json(),
                    'status' => 'failed',
                    'error_message' => $response->body(),
                ]);

                return false;
            }

        } catch (\Exception $e) {
            // Update log to failed
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
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

        $clinicName = $appointment->clinic->name;
        $patientName = $appointment->patient->name;
        $doctorName = $appointment->doctor->name;
        $date = \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y');
        $time = \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A');

        $replace = [
            '{clinic_name}' => $clinicName,
            '{patient_name}' => $patientName,
            '{doctor_name}' => $doctorName,
            '{date}' => $date,
            '{time}' => $time,
        ];

        return strtr($template, $replace);
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
