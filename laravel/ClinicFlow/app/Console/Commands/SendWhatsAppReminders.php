<?php

namespace App\Console\Commands;


use App\Models\Appointment;
use App\Models\Clinic;
use App\Services\WhatsAppService;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class SendWhatsAppReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:whatsapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders for appointments 24 hours in advance';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsAppService, \App\Services\SubscriptionService $subscriptionService)
    {
        $this->info('Checking for appointments needing WhatsApp reminders...');

        // Get all clinics with active WhatsApp settings
        $clinics = Clinic::whereHas('whatsappSettings', function ($q) {
            $q->where('is_active', true);
        })->with(['whatsappSettings', 'plan'])->get();

        foreach ($clinics as $clinic) {
            /** @var \App\Models\Clinic $clinic */
            // Check plan quota
            if (!$subscriptionService->canSendWhatsApp($clinic)) {
                $this->warn("Clinic: {$clinic->name} has reached its WhatsApp quota. Skipping reminders.");
                continue;
            }

            $hours = $clinic->whatsappSettings->reminder_hours_before;
            $this->info("Processing Clinic: {$clinic->name} (Reminders: {$hours}h before)");

            $targetTime = \Carbon\Carbon::now()->addHours($hours);
            // dd($targetTime->toDateString(), $targetTime->copy()->subMinutes(30)->toTimeString(), $targetTime->copy()->addMinutes(30)->toTimeString());
            // Scope logic directly here for dynamic hours
            $appointments = Appointment::where('clinic_id', $clinic->id)
                ->where('whatsapp_reminder_sent', false)
                ->where('status', 'booked')
                ->whereDate('appointment_date', $targetTime->toDateString())
                ->whereTime('appointment_time', '>=', $targetTime->copy()->subMinutes(30)->toTimeString())
                ->whereTime('appointment_time', '<=', $targetTime->copy()->addMinutes(30)->toTimeString())
                ->with(['patient', 'doctor'])
                ->get();

            if ($appointments->isEmpty()) {
                continue;
            }

            $this->info("Found " . $appointments->count() . " appointment(s) for {$clinic->name}");

            foreach ($appointments as $appointment) {
                try {
                    $result = app(\App\Services\WhatsAppService::class)->sendAppointmentReminder($appointment);

                    if ($result) {
                        $appointment->update([
                            'whatsapp_reminder_sent' => true,
                            'whatsapp_reminder_sent_at' => now(),
                        ]);
                        $this->info("Sent WhatsApp reminder for appointment #{$appointment->id}");
                    } else {
                        $this->error("Failed to send WhatsApp reminder for appointment #{$appointment->id}");
                    }
                } catch (\Exception $e) {
                    $this->error("Error sending reminder for appointment #{$appointment->id}: " . $e->getMessage());
                }
            }
        }

        $this->info('WhatsApp reminder processing complete!');

        return 0;
    }
}
