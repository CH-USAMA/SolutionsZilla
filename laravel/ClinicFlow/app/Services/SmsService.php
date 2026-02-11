<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $sid;
    protected $token;
    protected $from;

    public function __construct()
    {
        $this->sid = config('services.twilio.sid');
        $this->token = config('services.twilio.token');
        $this->from = config('services.twilio.from');
    }

    /**
     * Send an SMS to a patient.
     */
    public function sendSms(Patient $patient, string $message, Clinic $clinic)
    {
        // 1. Check Quota (via SubscriptionService - integrated separately)

        // 2. Prepare Payload
        $phone = $patient->phone;
        if (!str_starts_with($phone, '+')) {
            $phone = '+92' . ltrim($phone, '0'); // Default to Pakistan if no country code
        }

        try {
            $response = Http::withBasicAuth($this->sid, $this->token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json", [
                    'To' => $phone,
                    'From' => $this->from,
                    'Body' => $message,
                ]);

            $data = $response->json();

            // 3. Log Activity
            SmsLog::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'phone_number' => $phone,
                'message' => $message,
                'status' => $response->successful() ? 'sent' : 'failed',
                'provider_sid' => $data['sid'] ?? null,
                'provider_response' => $data,
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error("SMS Sending Failed: " . $e->getMessage());

            SmsLog::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'phone_number' => $phone,
                'message' => $message,
                'status' => 'error',
                'provider_response' => ['error' => $e->getMessage()],
            ]);

            return false;
        }
    }
}
