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
        try {
            // Meta sends verification challenge on webhook setup
            if ($request->has('hub_mode') && $request->hub_mode === 'subscribe') {
                if ($request->hub_verify_token === config('services.whatsapp.verify_token')) {
                    return response($request->hub_challenge, 200);
                }
                return response('Forbidden', 403);
            }

            // Parse incoming message
            $entry = $request->input('entry.0');
            $changes = $entry['changes'][0] ?? null;
            $value = $changes['value'] ?? null;

            // Get the Phone Number ID that received this message for multi-tenancy
            $metadata = $value['metadata'] ?? null;
            $phoneNumberId = $metadata['phone_number_id'] ?? null;

            if (!$phoneNumberId) {
                return response()->json(['status' => 'no_phone_id']);
            }

            // Find the clinic associated with this Phone Number ID
            $setting = \App\Models\ClinicWhatsappSetting::where('phone_number_id', $phoneNumberId)->first();
            $clinicId = $setting ? $setting->clinic_id : null;

            $messages = $value['messages'] ?? [];

            foreach ($messages as $message) {
                $phone = $message['from'] ?? null;
                $messageText = $message['text']['body'] ?? null;

                if (!$phone || !$messageText) {
                    continue;
                }

                // Log incoming message
                WhatsappLog::create([
                    'clinic_id' => $clinicId,
                    'direction' => 'incoming',
                    'phone' => $phone,
                    'payload' => $message,
                    'status' => 'received',
                ]);

                // Check for confirmation keywords
                if ($clinicId) {
                    $this->handleConfirmation($phone, $messageText);
                }
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('WhatsApp webhook error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Handle appointment confirmation via WhatsApp
     */
    private function handleConfirmation(string $phone, string $messageText)
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
            'جی',
            'ہاں',
            'ٹھیک',
            'ہوگیا',
            'تصدیق'
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
        // Match last 10 digits to be safe with country codes
        $appointment = Appointment::whereHas('patient', function ($query) use ($phone) {
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
                'phone' => $phone,
                'text' => $messageText,
            ]);
        }
    }
}
