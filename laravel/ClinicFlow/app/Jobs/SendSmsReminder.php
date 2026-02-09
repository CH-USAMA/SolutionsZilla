<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $appointment;

    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        // Send SMS reminder
        $sent = $smsService->sendAppointmentReminder($this->appointment);

        if ($sent) {
            // Mark as sent
            $this->appointment->update([
                'sms_reminder_sent' => true,
                'sms_reminder_sent_at' => now(),
            ]);

            Log::info('SMS reminder sent successfully', [
                'appointment_id' => $this->appointment->id,
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SMS reminder job failed', [
            'appointment_id' => $this->appointment->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
