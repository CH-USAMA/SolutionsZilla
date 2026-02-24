<?php

namespace App\Services\WhatsApp;

use App\Interfaces\WhatsAppProviderInterface;
use App\Models\ClinicWhatsappSetting;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaProvider implements WhatsAppProviderInterface
{
    public function sendSimpleMessage(ClinicWhatsappSetting $settings, string $phone, array $params = [], ?int $appointmentId = null): bool
    {
        $clinicId = $settings->clinic_id;

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

        $log = WhatsAppMessage::create([
            'clinic_id' => $clinicId,
            'message_id' => 'pending_' . uniqid(),
            'direction' => 'outgoing',
            'to' => $phone,
            'from' => $settings->phone_number_id,
            'type' => 'text',
            'body' => $params['message'],
            'metadata' => [
                'provider' => 'meta',
                'payload' => $payload,
                'appointment_id' => $appointmentId
            ],
            'status' => 'pending',
        ]);

        try {
            $response = Http::withToken($settings->access_token)
                ->withHeaders(['ngrok-skip-browser-warning' => 'true'])
                ->post("https://graph.facebook.com/v20.0/{$settings->phone_number_id}/messages", $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                $messageId = $responseData['messages'][0]['id'] ?? null;

                $log->update([
                    'message_id' => $messageId ?? $log->message_id,
                    'status' => 'sent',
                    'metadata' => array_merge($log->metadata ?? [], ['response' => $responseData]),
                ]);

                return true;
            } else {
                $log->update([
                    'status' => 'failed',
                    'metadata' => array_merge($log->metadata ?? [], [
                        'response' => $response->json(),
                        'error_message' => $response->body()
                    ]),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'metadata' => array_merge($log->metadata ?? [], ['error_message' => $e->getMessage()]),
            ]);
            return false;
        }
    }

    public function sendTemplateMessage(ClinicWhatsappSetting $settings, string $phone, string $templateName, array $params = [], ?int $appointmentId = null): bool
    {
        $clinicId = $settings->clinic_id;

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => 'en'],
            ],
        ];

        $log = WhatsAppMessage::create([
            'clinic_id' => $clinicId,
            'message_id' => 'pending_' . uniqid(),
            'direction' => 'outgoing',
            'to' => $phone,
            'from' => $settings->phone_number_id,
            'type' => 'template',
            'body' => "Template: $templateName",
            'metadata' => [
                'provider' => 'meta',
                'payload' => $payload,
                'template_name' => $templateName,
                'appointment_id' => $appointmentId
            ],
            'status' => 'pending',
        ]);

        try {
            $response = Http::withToken($settings->access_token)
                ->post("https://graph.facebook.com/v20.0/{$settings->phone_number_id}/messages", $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                $messageId = $responseData['messages'][0]['id'] ?? null;

                $log->update([
                    'message_id' => $messageId ?? $log->message_id,
                    'status' => 'sent',
                    'metadata' => array_merge($log->metadata ?? [], ['response' => $responseData]),
                ]);

                return true;
            } else {
                $log->update([
                    'status' => 'failed',
                    'metadata' => array_merge($log->metadata ?? [], [
                        'response' => $response->json(),
                        'error_message' => $response->body()
                    ]),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'metadata' => array_merge($log->metadata ?? [], ['error_message' => $e->getMessage()]),
            ]);
            return false;
        }
    }

    public function getStatus(ClinicWhatsappSetting $settings): array
    {
        // For Meta, we can check if the access token works by calling symbolic endpoint
        return [
            'status' => $settings->is_active ? 'connected' : 'disconnected',
            'details' => 'Meta API status managed via Facebook Dashboard'
        ];
    }

    public function getQrCode(ClinicWhatsappSetting $settings): ?string
    {
        return null; // Not applicable for Meta
    }
}
