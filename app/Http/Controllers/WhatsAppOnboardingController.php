<?php

namespace App\Http\Controllers;

use App\Models\ClinicWhatsappSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppOnboardingController extends Controller
{
    /**
     * Handle Meta Embedded Signup Callback
     * Exchanges auth code for long-lived access token
     */
    public function callback(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'phone_number_id' => 'required|string',
            'waba_id' => 'required|string',
        ]);

        $code = $request->code;
        $clinicId = $request->user()->clinic_id;

        try {
            // Exchange code for access token
            $response = Http::get('https://graph.facebook.com/v20.0/oauth/access_token', [
                'client_id' => config('services.whatsapp.client_id'),
                'client_secret' => config('services.whatsapp.client_secret'),
                'code' => $code,
            ]);

            if ($response->failed()) {
                Log::error('WhatsApp Onboarding Failed: Token Exchange', ['error' => $response->body()]);
                return response()->json(['error' => 'Failed to connect to Meta'], 400);
            }

            $accessToken = $response->json()['access_token'];

            // Store credentials
            ClinicWhatsappSetting::updateOrCreate(
                ['clinic_id' => $clinicId],
                [
                    'phone_number_id' => $request->phone_number_id,
                    'waba_id' => $request->waba_id,
                    'access_token' => $accessToken,
                    'is_active' => true,
                    // 'review_status' => 'pending' // managed via webhook usually
                ]
            );

            // Optional: Subscribe to webhooks automatically using the token
            // Http::withToken($accessToken)->post(...)

            return response()->json(['status' => 'connected']);

        } catch (\Exception $e) {
            Log::error('WhatsApp Onboarding Exception', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
