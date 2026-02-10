<?php

namespace App\Http\Controllers;

use App\Models\ClinicWhatsappSetting;
use App\Models\WhatsappLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsAppSettingsController extends Controller
{
    /**
     * Show WhatsApp settings form
     */
    public function index()
    {
        $clinic = Auth::user()->clinic;
        $settings = $clinic->whatsappSettings ?? new ClinicWhatsappSetting();

        return view('whatsapp.settings', compact('settings'));
    }

    /**
     * Update WhatsApp settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'phone_number_id' => 'required|string',
            'access_token' => 'required|string',
            'default_template' => 'required_if:message_type,template|string|nullable',
            'message_type' => 'required|in:template,text',
            'custom_message' => 'required_if:message_type,text|string|nullable',
            'reminder_hours_before' => 'required|integer|min:1|max:168',
            'is_active' => 'boolean',
        ]);

        $clinic = Auth::user()->clinic;

        ClinicWhatsappSetting::updateOrCreate(
            ['clinic_id' => $clinic->id],
            [
                'phone_number_id' => $request->phone_number_id,
                'access_token' => $request->access_token,
                'default_template' => $request->default_template,
                'message_type' => $request->message_type,
                'custom_message' => $request->custom_message,
                'reminder_hours_before' => $request->reminder_hours_before,
                'is_active' => $request->has('is_active'),
            ]
        );

        return redirect()->route('whatsapp.settings')->with('success', 'WhatsApp settings updated successfully.');
    }

    /**
     * Show WhatsApp logs
     */
    public function logs()
    {
        $clinic = Auth::user()->clinic;

        $logs = WhatsappLog::where('clinic_id', $clinic->id)
            ->with(['appointment.patient'])
            ->latest()
            ->paginate(20);

        return view('whatsapp.logs', compact('logs'));
    }

    /**
     * Send a test WhatsApp message
     */
    public function test()
    {
        $clinic = Auth::user()->clinic;
        $settings = $clinic->whatsappSettings;

        if (!$settings) {
            return back()->with('error', 'Please save your WhatsApp settings first.');
        }

        // Just use the clinic's own phone for testing if possible, 
        // or a dummy number. User provided '923038004684'.
        $testPhone = '923038004684';

        $service = app(\App\Services\WhatsAppService::class);
        $testMessage = 'Test message from ClinicFlow! This verifies your ' . ($settings->message_type === 'text' ? 'Simple Text' : 'Template') . ' settings.';

        if ($settings->message_type === 'text') {
            $result = $service->sendSimpleMessage(
                $settings,
                $testPhone,
                ['message' => $testMessage]
            );
        } else {
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
    public function createTestAppointment()
    {
        $clinic = Auth::user()->clinic;
        $settings = $clinic->whatsappSettings;
        $hours = $settings->reminder_hours_before ?? 24;

        // Use first patient and doctor for simplicity
        $patient = $clinic->patients()->first();
        $doctor = $clinic->doctors()->first();

        if (!$patient || !$doctor) {
            return back()->with('error', 'Need at least one patient and one doctor to create a test appointment.');
        }

        $appointment = \App\Models\Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addHours($hours),
            'appointment_time' => now()->addHours($hours)->format('H:i:s'),
            'status' => 'booked',
            'notes' => 'Generated for WhatsApp testing',
        ]);

        return back()->with('success', "Test appointment #{$appointment->id} created for tomorrow at " . $appointment->appointment_time . ". You can now run 'php artisan reminders:whatsapp' to test.");
    }
}
