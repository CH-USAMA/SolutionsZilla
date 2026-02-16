<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppConversation;
use App\Models\ClinicWhatsappSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle incoming WhatsApp webhook
     */
    public function handle(Request $request)
    {
        Log::info('WhatsApp Webhook Entry', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'payload' => $request->all()
        ]);

        // 1. Signature Verification (Production Security - Skip for third-party bridges for now unless secrets provided)
        if ($request->hasHeader('X-Hub-Signature-256') && !$this->validateSignature($request)) {
            Log::warning('WhatsApp Webhook: Invalid signature', [
                'ip' => $request->ip(),
                'header' => $request->header('X-Hub-Signature-256'),
            ]);
            return response('Unauthorized', 401);
        }

        // 2. Verification Challenge (GET)
        if ($request->isMethod('get') && $request->hub_mode === 'subscribe') {
            if ($request->hub_verify_token === config('services.whatsapp.verify_token')) {
                return response($request->hub_challenge, 200);
            }
            return response('Forbidden', 403);
        }

        // 3. Handle Events (POST)
        try {
            $payload = $request->all();

            // Determine structure and normalize
            if ($request->has('typeWebhook') && $request->has('idMessage')) {
                return $this->handleBridgePayload($request);
            }

            // Meta standard structure
            $changes = $request->input('entry.0.changes.0');

            // Fallback for simplified test payloads
            if (!$changes && $request->has('field') && $request->has('value')) {
                $changes = $payload;
            }

            if (!$changes || ($changes['field'] !== 'messages')) {
                Log::info('WhatsApp Webhook: Ignored non-message field', ['field' => $changes['field'] ?? 'unknown']);
                return response()->json(['status' => 'ignored', 'reason' => 'not_messages_field']);
            }

            $value = $changes['value'] ?? [];
            $metadata = $value['metadata'] ?? [];
            $phoneNumberId = $metadata['phone_number_id'] ?? null;

            if (!$phoneNumberId) {
                Log::warning('WhatsApp Webhook: Missing phone_number_id in payload');
                return response()->json(['status' => 'error', 'message' => 'no_phone_id']);
            }

            // Find Tenant
            $setting = ClinicWhatsappSetting::withoutGlobalScopes()
                ->where('phone_number_id', (string) $phoneNumberId)
                ->first();

            if (!$setting) {
                Log::warning("WhatsApp Webhook: Unknown phone_number_id: $phoneNumberId");
                return response()->json(['status' => 'error', 'message' => 'unknown_phone_id']);
            }

            $clinicId = $setting->clinic_id;

            // Handle Messages
            if (!empty($value['messages'])) {
                foreach ($value['messages'] as $message) {
                    $this->processMessage($clinicId, $message, $metadata);
                }
            }

            // Handle Status Updates
            if (!empty($value['statuses'])) {
                foreach ($value['statuses'] as $status) {
                    $this->processStatus($clinicId, $status);
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
     * Handle payloads from third-party bridges (Green-API, etc.)
     */
    private function handleBridgePayload(Request $request)
    {
        $type = $request->input('typeWebhook');
        $idInstance = $request->input('instanceData.idInstance');

        // Find setting by Instance ID or Phone ID
        $setting = ClinicWhatsappSetting::withoutGlobalScopes()
            ->where('phone_number_id', (string) $idInstance)
            ->first();

        if (!$setting) {
            Log::warning("WhatsApp Webhook (Bridge): Unknown instance ID: $idInstance");
            return response()->json(['status' => 'error', 'message' => 'unknown_instance']);
        }

        $clinicId = $setting->clinic_id;

        if ($type === 'incomingMessageReceived') {
            $sender = str_replace('@c.us', '', $request->input('senderData.sender'));
            $body = '';
            $msgType = $request->input('messageData.typeMessage');

            if ($msgType === 'textMessage') {
                $body = $request->input('messageData.textMessageData.textMessage');
            }

            $payload = [
                'id' => $request->input('idMessage'),
                'from' => $sender,
                'type' => $msgType === 'textMessage' ? 'text' : $msgType,
                'timestamp' => $request->input('timestamp'),
                'text' => ['body' => $body]
            ];

            $this->processMessage($clinicId, $payload, [
                'display_phone_number' => $request->input('instanceData.wid', '')
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Process incoming message
     */
    private function processMessage($clinicId, $payload, $metadata)
    {
        $messageId = $payload['id'];
        $from = $payload['from'];
        $type = $payload['type'];
        $timestamp = isset($payload['timestamp']) ? Carbon::createFromTimestamp($payload['timestamp']) : now();

        // 1. Manage Conversation (24h Window)
        $conversation = $this->getOrCreateConversation($clinicId, $from, $timestamp);

        // 2. Persist Message (Idempotency Check)
        $existingMessage = WhatsAppMessage::where('clinic_id', $clinicId)
            ->where('message_id', $messageId)
            ->first();

        if (!$existingMessage) {
            WhatsAppMessage::create([
                'clinic_id' => $clinicId,
                'message_id' => $messageId,
                'wamid' => $messageId,
                'from' => $from,
                'to' => $metadata['display_phone_number'] ?? '',
                'type' => $type,
                'direction' => 'incoming',
                'body' => $this->extractBody($payload),
                'status' => 'received',
                'metadata' => $payload,
                'conversation_id' => $conversation->conversation_id,
                'created_at' => $timestamp,
            ]);
        }

        // 3. Logic: Appointment Confirmation
        if ($type === 'text') {
            $this->handleConfirmation($clinicId, $from, $payload['text']['body']);
        }
    }

    /**
     * Process message status update
     */
    private function processStatus($clinicId, $payload)
    {
        $messageId = $payload['id'];
        $status = $payload['status']; // sent, delivered, read, failed

        $message = WhatsAppMessage::where('clinic_id', $clinicId)
            ->where(function ($q) use ($messageId) {
                $q->where('message_id', $messageId)
                    ->orWhere('wamid', $messageId);
            })
            ->first();

        if ($message) {
            $message->update([
                'status' => $status,
                'metadata' => array_merge($message->metadata ?? [], ['status_update' => $payload]),
            ]);

            // If we have pricing info in status (conversation billable), update conversation cost?
            // Meta usually sends pricing in 'pricing' object in status for 'sent' or 'delivered'
            if (isset($payload['pricing'])) {
                // Future implementation: Update conversation cost
            }
        }
    }

    /**
     * Get active conversation or create new
     */
    private function getOrCreateConversation($clinicId, $phoneNumber, $timestamp)
    {
        // Check for active conversation (started within last 24 hours)
        $conversation = WhatsAppConversation::where('clinic_id', $clinicId)
            ->where('phone_number', $phoneNumber)
            ->where('expires_at', '>', $timestamp)
            ->latest('started_at')
            ->first();

        if (!$conversation) {
            // Start new conversation
            $conversationId = 'conv_' . uniqid(); // In real implementation, derive from Meta if available
            $conversation = WhatsAppConversation::create([
                'clinic_id' => $clinicId,
                'conversation_id' => $conversationId,
                'phone_number' => $phoneNumber,
                'started_at' => $timestamp,
                'expires_at' => $timestamp->copy()->addHours(24),
                'last_message_at' => $timestamp,
                'type' => 'service', // Default to service/user-initiated
                'category' => 'user_initiated',
                'message_count' => 1,
            ]);
        } else {
            // Update existing
            $conversation->update([
                'last_message_at' => $timestamp,
                'message_count' => $conversation->message_count + 1,
            ]);
        }

        return $conversation;
    }

    /**
     * Extract clean body from message payload
     */
    private function extractBody($payload)
    {
        $type = $payload['type'];
        return match ($type) {
            'text' => $payload['text']['body'] ?? '',
            'button' => $payload['button']['text'] ?? '',
            'interactive' => $payload['interactive']['button_reply']['title'] ?? ($payload['interactive']['list_reply']['title'] ?? ''),
            default => "[$type]",
        };
    }

    /**
     * Validate incoming Meta Webhook signature
     */
    private function validateSignature(Request $request): bool
    {
        $signature = $request->header('X-Hub-Signature-256');
        $appSecret = config('services.whatsapp.app_secret');

        if (empty($appSecret)) {
            return true; // Dev mode
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
