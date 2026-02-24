<?php

namespace App\Http\Controllers;

use App\Models\ClinicWhatsappSetting;
use App\Models\WhatsappLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WhatsAppSettingsController extends Controller
{
    /**
     * Show WhatsApp settings form
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedClinicId = null;
        $clinic = null;

        // Super Admin: allow clinic selection
        if ($user->isSuperAdmin()) {
            $clinics = \App\Models\Clinic::orderBy('name')->get();
            $selectedClinicId = $request->get('clinic_id');

            if ($selectedClinicId) {
                $clinic = \App\Models\Clinic::find($selectedClinicId);
            }
        } else {
            $clinic = $user->clinic;
        }

        if ($clinic) {
            $settings = $clinic->whatsappSettings;
            $allowedProviders = $clinic->allowed_whatsapp_providers ?? ['meta'];

            // Zero-Config Auto-Initialization
            if (!$settings || (in_array('js_api', $allowedProviders) && empty($settings->js_session_id))) {
                $settings = ClinicWhatsappSetting::updateOrCreate(
                    ['clinic_id' => $clinic->id],
                    [
                        'provider' => in_array('js_api', $allowedProviders) ? 'js_api' : 'meta',
                        'js_session_id' => $settings->js_session_id ?? ('clinic_' . $clinic->id . '_' . bin2hex(random_bytes(4))),
                        'is_active' => $settings->is_active ?? true,
                    ]
                );
            }
        } else {
            $settings = new ClinicWhatsappSetting();
        }

        if ($user->isSuperAdmin()) {
            return view('whatsapp.settings', compact('settings', 'clinics', 'selectedClinicId'));
        }

        return view('whatsapp.settings', compact('settings', 'selectedClinicId'));
    }

    /**
     * Update WhatsApp settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $clinicId = $user->isSuperAdmin() ? $request->clinic_id : $user->clinic_id;

        if (!$clinicId) {
            return back()->with('error', 'No clinic selected.');
        }

        $clinic = \App\Models\Clinic::find($clinicId);
        $allowedProviders = $clinic->allowed_whatsapp_providers ?? ['meta'];
        $provider = $request->input('provider', 'meta');

        if (!in_array($provider, $allowedProviders) && !$user->isSuperAdmin()) {
            return back()->with('error', 'The selected provider is not enabled for your clinic.');
        }

        $settings = ClinicWhatsappSetting::where('clinic_id', $clinicId)->first();

        $rules = [
            'provider' => 'required|in:meta,js_api',
            'message_type' => 'required|in:template,text',
            'reminder_hours_before' => 'required|integer|min:1|max:168',
            'is_active' => 'boolean',
            'custom_message' => 'required_if:message_type,text|string|nullable',
        ];

        // Technical fields are ONLY required if Super Admin is editing OR if they don't exist yet (including global)
        $isSuperAdmin = $user->isSuperAdmin();
        $globalJsUrl = config('services.whatsapp.js_api_url');

        if ($provider === 'meta') {
            $rules['phone_number_id'] = ($isSuperAdmin || !$settings?->phone_number_id) ? 'required|string' : 'nullable|string';
            $rules['access_token'] = ($isSuperAdmin || !$settings?->access_token) ? 'required|string' : 'nullable|string';
            $rules['default_template'] = 'required_if:message_type,template|string|nullable';
        } else {
            $rules['js_api_url'] = ($isSuperAdmin || (!$settings?->js_api_url && !$globalJsUrl)) ? 'required|url' : 'nullable|url';
            $rules['js_session_id'] = ($isSuperAdmin || !$settings?->js_session_id) ? 'required|string' : 'nullable|string';
        }

        $request->validate($rules);

        $updateData = [
            'provider' => $provider,
            'message_type' => $request->message_type,
            'custom_message' => $request->custom_message,
            'reminder_hours_before' => $request->reminder_hours_before,
            'is_active' => $request->has('is_active'),
            'default_template' => $request->default_template ?? ($settings?->default_template ?? 'appointment_reminder'),
        ];

        // Only update technical fields if provided or if user is super admin
        if ($request->has('phone_number_id'))
            $updateData['phone_number_id'] = $request->phone_number_id;
        if ($request->has('access_token'))
            $updateData['access_token'] = $request->access_token;
        if ($request->has('js_api_url'))
            $updateData['js_api_url'] = $request->js_api_url;
        if ($request->has('js_api_key'))
            $updateData['js_api_key'] = $request->js_api_key;
        if ($request->has('js_session_id'))
            $updateData['js_session_id'] = $request->js_session_id;

        ClinicWhatsappSetting::updateOrCreate(
            ['clinic_id' => $clinicId],
            $updateData
        );

        return redirect()->route('whatsapp.settings', ['clinic_id' => $clinicId])->with('success', 'WhatsApp settings updated successfully.');
    }

    /**
     * Show WhatsApp logs
     */
    public function logs(Request $request)
    {
        $user = Auth::user();

        // Super Admin: allow clinic selection or view all
        if ($user->isSuperAdmin()) {
            $clinics = \App\Models\Clinic::orderBy('name')->get();
            $selectedClinicId = $request->get('clinic_id');

            $query = WhatsappLog::with(['appointment.patient', 'clinic']);

            if ($selectedClinicId) {
                $query->where('clinic_id', $selectedClinicId);
            }

            $logs = $query->latest()->paginate(20);

            return view('whatsapp.logs', compact('logs', 'clinics', 'selectedClinicId'));
        }

        // Regular clinic user
        $clinic = $user->clinic;
        $selectedClinicId = null;

        $logs = WhatsappLog::where('clinic_id', $clinic->id)
            ->with(['appointment.patient'])
            ->latest()
            ->paginate(20);

        return view('whatsapp.logs', compact('logs', 'selectedClinicId'));
    }

    /**
     * Send a test WhatsApp message
     */
    public function test(Request $request)
    {
        $user = Auth::user();
        $clinicId = $user->isSuperAdmin() ? $request->clinic_id : $user->clinic_id;

        if (!$clinicId) {
            return back()->with('error', 'No clinic selected.');
        }

        $request->validate([
            'test_phone' => 'required|string|min:10',
        ]);

        $clinic = \App\Models\Clinic::find($clinicId);
        $settings = $clinic->whatsappSettings;

        if (!$settings) {
            return back()->with('error', 'Please save WhatsApp settings for this clinic first.');
        }

        $testPhone = $request->input('test_phone');

        $service = app(WhatsAppService::class);

        // Build a more personal test message using clinic details
        $testMessage = "Hello from *{$clinic->name}*! \n\n" .
            "This is a test message to verify our WhatsApp integration via " .
            ($settings->provider === 'js_api' ? "WhatsApp JS API" : "Meta Cloud API") . ". \n\n" .
            "If you received this, your system is ready to send automated appointment reminders. \n" .
            "Generated on: " . now()->format('d M Y, h:i A');

        if ($settings->message_type === 'text' || $settings->provider === 'js_api') {
            $result = $service->sendSimpleMessage(
                $settings,
                $testPhone,
                ['message' => $testMessage]
            );
        } else {
            // For Meta, still use template if configured
            $result = $service->sendTemplateMessage(
                $settings,
                $testPhone,
                $settings->default_template,
                ['message' => $testMessage]
            );
        }

        if ($result) {
            return back()->with('success', 'Test message sent successfully to ' . $testPhone);
        }

        return back()->with('error', 'Failed to send test message. Check logs for details.');
    }

    /**
     * Create a test appointment for testing the scheduler
     */
    public function createTestAppointment(Request $request)
    {
        $user = Auth::user();
        $clinicId = $user->isSuperAdmin() ? $request->clinic_id : $user->clinic_id;

        if (!$clinicId) {
            return back()->with('error', 'No clinic selected.');
        }

        $clinic = \App\Models\Clinic::find($clinicId);
        $settings = $clinic->whatsappSettings;
        $hours = $settings->reminder_hours_before ?? 24;

        // Use first patient and doctor for simplicity
        $patient = $clinic->patients()->first();
        $doctor = $clinic->doctors()->first();

        if (!$patient || !$doctor) {
            return back()->with('error', 'Need at least one patient and one doctor in ' . $clinic->name . ' to create a test appointment.');
        }

        $appointment = \App\Models\Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addHours($hours + 1)->format('Y-m-d'),
            'appointment_time' => now()->addHours($hours + 1)->format('H:i:s'),
            'status' => 'booked',
            'notes' => 'Generated for WhatsApp testing by Super Admin',
        ]);

        return back()->with('success', "Test appointment #{$appointment->id} created for clinic '{$clinic->name}' at " . $appointment->appointment_time . ".");
    }

    /**
     * Get QR Code for JS API connection (AJAX)
     */
    public function getQrCode(Request $request)
    {
        $user = Auth::user();
        $clinicId = ($user && $user->isSuperAdmin()) ? ($request->clinic_id ?: $user->clinic_id) : ($user ? $user->clinic_id : $request->clinic_id);

        if (!$clinicId) {
            return response()->json(['error' => 'Clinic not specified'], 400);
        }

        $settings = ClinicWhatsappSetting::where('clinic_id', $clinicId)->first();
        if (!$settings) {
            return response()->json(['error' => 'Settings not found'], 404);
        }

        if ($settings->provider !== 'js_api') {
            return response()->json(['error' => 'JS API not configured'], 404);
        }

        try {
            $providerRecord = (new WhatsAppService())->getProvider($settings);

            // Get raw response check for status
            $baseUrl = $settings->js_api_url ?: config('services.whatsapp.js_api_url');
            $session = $settings->js_session_id;
            $apiKey = $settings->js_api_key ?: config('services.whatsapp.js_api_key');

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key' => $apiKey
            ])->timeout(10)->get($baseUrl . "/sessions/qr/{$session}");

            if ($response->successful()) {
                $data = $response->json();

                // If status is connected, update local DB status
                if (($data['status'] ?? '') === 'connected') {
                    $settings->update(['js_connection_status' => 'connected']);
                }

                return response()->json($data);
            }

            return response()->json(['error' => 'Failed to reach gateway', 'status' => 'error'], 500);
        } catch (\Exception $e) {
            Log::error("WhatsAppSettingsController: QR failed- " . $e->getMessage());
            return response()->json(['error' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }
}
