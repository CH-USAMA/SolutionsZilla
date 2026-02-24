<?php

namespace App\Services\WhatsApp;

use App\Interfaces\WhatsAppProviderInterface;
use App\Models\ClinicWhatsappSetting;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JsApiProvider implements WhatsAppProviderInterface
{
    protected function getBaseUrl(ClinicWhatsappSetting $settings): ?string
    {
        return $settings->js_api_url ?: config('services.whatsapp.js_api_url');
    }

    protected function getApiKey(ClinicWhatsappSetting $settings): ?string
    {
        return $settings->js_api_key ?: config('services.whatsapp.js_api_key');
    }

    public function sendSimpleMessage(ClinicWhatsappSetting $settings, string $phone, array $params = [], ?int $appointmentId = null): bool
    {
        $clinicId = $settings->clinic_id;

        $log = WhatsAppMessage::create([
            'clinic_id' => $clinicId,
            'message_id' => 'pending_' . uniqid(),
            'direction' => 'outgoing',
            'to' => $phone,
            'from' => $settings->js_session_id ?? 'default',
            'type' => 'text',
            'body' => $params['message'],
            'metadata' => [
                'provider' => 'js_api',
                'appointment_id' => $appointmentId
            ],
            'status' => 'pending',
        ]);

        try {
            $baseUrl = $this->getBaseUrl($settings);
            $apiKey = $this->getApiKey($settings);

            if (!$baseUrl) {
                Log::error('JS API Error: No URL configured.', ['clinic_id' => $clinicId]);
                return false;
            }

            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->post("{$baseUrl}/messages/send", [
                        'session' => $settings->js_session_id,
                        'to' => $phone,
                        'text' => $params['message'],
                    ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $log->update([
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
        // For JS API, we usually send raw text as it doesn't have "official" templates
        // We can simulate template sending by just sending the built body
        return $this->sendSimpleMessage($settings, $phone, $params, $appointmentId);
    }

    public function getStatus(ClinicWhatsappSetting $settings): array
    {
        $baseUrl = $this->getBaseUrl($settings);
        if (!$baseUrl || !$settings->js_session_id) {
            return ['status' => 'not_configured', 'details' => 'Missing URL or Session ID'];
        }

        try {
            $apiKey = $this->getApiKey($settings);
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->get("{$baseUrl}/sessions/status/{$settings->js_session_id}");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'details' => $e->getMessage()];
        }

        return ['status' => 'disconnected', 'details' => 'Unable to reach JS API'];
    }

    public function getQrCode(ClinicWhatsappSetting $settings): ?string
    {
        $baseUrl = $this->getBaseUrl($settings);
        $session = $settings->js_session_id;
        $apiKey = $this->getApiKey($settings);

        if (!$baseUrl || !$session) {
            return null;
        }

        try {
            Log::info("JsApiProvider: Attempting to fetch QR from {$baseUrl}/sessions/qr/{$session}");
            $response = Http::withHeaders([
                'x-api-key' => $apiKey
            ])->timeout(10)->get($baseUrl . "/sessions/qr/{$session}");

            if ($response->successful()) {
                $qr = $response->json('qr');
                if ($qr)
                    return $qr;

                // If successful but no QR, it might be initializing
                $status = $response->json('status', 'unknown');
                throw new \Exception("Session status: {$status}. Please wait a moment and refresh.");
            }

            if ($response->status() === 403) {
                throw new \Exception("Unauthorized: API Keys do not match.");
            }

            throw new \Exception("Gateway returned error {$response->status()}: " . ($response->json('error') ?: $response->body()));
        } catch (\Exception $e) {
            Log::error("WhatsApp JS API QR Error: " . $e->getMessage());
            throw $e;
        }
    }
}
