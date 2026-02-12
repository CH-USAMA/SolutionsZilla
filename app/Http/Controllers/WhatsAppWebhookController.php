<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\WhatsappLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle incoming WhatsApp webhook
     */
    public function handle(Request $request)
    {
        Log::info('WhatsApp Webhook received', ['payload' => $request->all()]);

        // 1. Signature Verification (Production Security)
        if (!$this->validateSignature($request)) {
            Log::warning('WhatsApp Webhook: Invalid signature', [
                'header' => $request->header('X-Hub-Signature-256'),
                'body' => $request->getContent(),
            ]);
            return response('Unauthorized', 401);
        }

        try {
            // Meta sends verification challenge on webhook setup
            if ($request->has('hub_mode') && $request->hub_mode === 'subscribe') {
                if ($request->hub_verify_token === config('services.whatsapp.verify_token')) {
                    return response($request->hub_challenge, 200);
                }
                return response('Forbidden', 403);
            }

            // Parse incoming message safely
            $entry = $request->input('entry.0', []);
            $changes = $entry['changes'][0] ?? null;
            if (!$changes) {
                return response()->json(['status' => 'no_changes']);
            }

            $value = $changes['value'] ?? null;
            if (!$value) {
                return response()->json(['status' => 'no_value']);
            }

            // Get the Phone Number ID that received this message for multi-tenancy
            $metadata = $value['metadata'] ?? [];
            $phoneNumberId = $metadata['phone_number_id'] ?? null;

            if (!$phoneNumberId) {
                Log::warning('WhatsApp Webhook: No phone_number_id found in metadata');
                return response()->json(['status' => 'no_phone_id']);
            }

            // Find the clinic associated with this Phone Number ID (Global Scope bypassed for discovery)
            $setting = \App\Models\ClinicWhatsappSetting::withoutGlobalScopes()
                ->where('phone_number_id', $phoneNumberId)
                ->first();

            $clinicId = $setting ? $setting->clinic_id : null;

            $messages = $value['messages'] ?? [];
            if (empty($messages)) {
                // Could be a status update (delivered/read), which we don't handle yet
                return response()->json(['status' => 'no_messages']);
            }

            foreach ($messages as $message) {
                $phone = $message['from'] ?? null;
                $messageType = $message['type'] ?? null;

                // Only handle text messages for confirmation
                if ($messageType !== 'text') {
                    continue;
                }

                $messageText = $message['text']['body'] ?? null;

                if (!$phone || !$messageText) {
                    continue;
                }

                // Log incoming message (Global Scope will handle clinic_id if auth, but here we set it manually)
                try {
                    WhatsappLog::create([
                        'clinic_id' => $clinicId,
                        'direction' => 'incoming',
                        'phone' => $phone,
                        'payload' => $message,
                        'status' => 'received',
                    ]);
                } catch (\Exception $logError) {
                    Log::error('Failed to log WhatsApp message', ['error' => $logError->getMessage()]);
                }

                // Check for confirmation keywords
                if ($clinicId) {
                    $this->handleConfirmation($clinicId, $phone, $messageText);
                }
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('WhatsApp webhook fatal error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Validate incoming Meta Webhook signature
     */
    private function validateSignature(Request $request): bool
    {
        $signature = $request->header('X-Hub-Signature-256');

        // If app_secret is not set, we skip validation for now (local dev convenience)
        // But in production, this should be mandatory
        $appSecret = config('services.whatsapp.app_secret');
        if (empty($appSecret)) {
            return true;
        }

        if (!$signature) {
            return false;
        }

        $signature = str_replace('sha256=', '', $signature);
        $expectedSignature = hash_hmac('sha256', $request->getContent(), $appSecret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle appointment confirmation via WhatsApp
     */
    private function handleConfirmation(int $clinicId, string $phone, string $messageText)
    {
        // Expand confirmation keywords (English & Urdu)
        $confirmationKeywords = [
            'yes',
            'ok',
            'confirm',
            'done',
            'booked',
            'ji',
            'han',
            'theek',
            'jee',
            'okey',
            'confirmado',
            'جی',
            'ہاں',
            'ٹھیک',
            'ہوگیا',
            'تصدیق',
            'جی ہاں'
        ];

        $normalizedText = mb_strtolower(trim($messageText));

        $isConfirmation = false;
        foreach ($confirmationKeywords as $keyword) {
            if (mb_strpos($normalizedText, $keyword) !== false) {
                $isConfirmation = true;
                break;
            }
        }

        if (!$isConfirmation) {
            return;
        }

        // Find latest upcoming appointment for this phone number
        // Bypassing global scope as we are in background process with a specific clinic context
        $appointment = Appointment::withoutGlobalScopes()
            ->where('clinic_id', $clinicId)
            ->whereHas('patient', function ($query) use ($phone) {
                $query->where('phone', 'like', '%' . substr($phone, -10) . '%');
            })
            ->where('status', 'booked')
            ->whereNull('confirmed_at')
            ->where('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->first();

        if ($appointment) {
            // Confirm appointment
            $appointment->update([
                'confirmed_at' => now(),
                'status' => 'confirmed',
            ]);

            Log::info('Appointment confirmed via WhatsApp', [
                'appointment_id' => $appointment->id,
                'clinic_id' => $clinicId,
                'phone' => $phone,
            ]);
        }
    }
}
