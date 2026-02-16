<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Services\SmsService;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendSmsReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS reminders for appointments as configured per clinic';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService, SubscriptionService $subscriptionService)
    {
        $this->info('Checking for appointments needing SMS reminders...');

        // Get all clinics with active SMS reminders
        $clinics = Clinic::where('is_active', true)
            ->whereHas('whatsappSettings', function ($q) {
                $q->where('is_active', true);
            })->with(['whatsappSettings'])->get();

        foreach ($clinics as $clinic) {
            /** @var Clinic $clinic */

            // Check Plan Quota
            if (!$subscriptionService->canSendSms($clinic)) {
                $this->warn("Clinic: {$clinic->name} has reached its SMS quota. Skipping.");
                continue;
            }

            $hours = $clinic->whatsappSettings->reminder_hours_before; // Use same window for now
            $this->info("Processing SMS for Clinic: {$clinic->name} ({$hours}h before)");

            $targetTime = Carbon::now()->addHours($hours);

            $appointments = Appointment::where('clinic_id', $clinic->id)
                ->where('sms_reminder_sent', false)
                ->where('status', 'booked')
                ->whereDate('appointment_date', $targetTime->toDateString())
                ->whereTime('appointment_time', '>=', $targetTime->copy()->subMinutes(30)->toTimeString())
                ->whereTime('appointment_time', '<=', $targetTime->copy()->addMinutes(30)->toTimeString())
                ->with(['patient', 'doctor'])
                ->get();

            foreach ($appointments as $appointment) {
                $timeStr = $appointment->appointment_time instanceof Carbon ? $appointment->appointment_time->format('H:i') : Carbon::parse($appointment->appointment_time)->format('H:i');
                $dateStr = $appointment->appointment_date instanceof Carbon ? $appointment->appointment_date->format('M d, Y') : Carbon::parse($appointment->appointment_date)->format('M d, Y');

                $message = "Reminder: Your appointment with Dr. {$appointment->doctor->name} is scheduled for {$timeStr} on {$dateStr}. Support: {$clinic->phone}";

                if ($smsService->sendSms($appointment->patient, $message, $clinic)) {
                    $appointment->update([
                        'sms_reminder_sent' => true,
                        'sms_reminder_sent_at' => now(),
                    ]);
                    $this->info("SMS sent to appointment #{$appointment->id}");
                } else {
                    $this->error("Failed to send SMS to appointment #{$appointment->id}");
                }
            }
        }

        $this->info('SMS reminder processing complete!');
        return 0;
    }
}
