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
            $messages = $value['messages'] ?? [];

            foreach ($messages as $message) {
                $phone = $message['from'] ?? null;
                $messageText = $message['text']['body'] ?? null;

                if (!$phone || !$messageText) {
                    continue;
                }

                // Log incoming message
                WhatsappLog::create([
                    'clinic_id' => 1, // TODO: Determine clinic from phone number mapping
                    'direction' => 'incoming',
                    'phone' => $phone,
                    'payload' => $message,
                    'status' => 'received',
                ]);

                // Check for confirmation keywords
                $this->handleConfirmation($phone, $messageText);
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
        // Check if message is a confirmation keyword
        $confirmationKeywords = ['yes', 'ok', 'confirm'];
        $normalizedText = strtolower(trim($messageText));

        if (!in_array($normalizedText, $confirmationKeywords)) {
            return;
        }

        // Find latest upcoming appointment for this phone number
        $appointment = Appointment::whereHas('patient', function ($query) use ($phone) {
            $query->where('phone', 'like', '%' . substr($phone, -10) . '%');
        })
            ->where('status', 'booked')
            ->whereNull('confirmed_at')
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->first();

        if ($appointment) {
            // Confirm appointment (idempotent)
            $appointment->update([
                'confirmed_at' => now(),
                'status' => 'confirmed',
            ]);

            Log::info('Appointment confirmed via WhatsApp', [
                'appointment_id' => $appointment->id,
                'phone' => $phone,
            ]);
        }
    }
}
