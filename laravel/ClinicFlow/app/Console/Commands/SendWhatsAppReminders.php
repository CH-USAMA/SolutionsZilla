<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppReminder;
use App\Models\Appointment;
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
    public function handle()
    {
        $this->info('Checking for appointments needing WhatsApp reminders...');

        $appointments = Appointment::with(['patient', 'doctor', 'clinic'])
            ->needingWhatsAppReminder()
            ->get();

        $count = $appointments->count();

        if ($count === 0) {
            $this->info('No appointments need WhatsApp reminders at this time.');
            return 0;
        }

        $this->info("Found {$count} appointment(s) needing WhatsApp reminders.");

        foreach ($appointments as $appointment) {
            SendWhatsAppReminder::dispatch($appointment);
            $this->line("Queued WhatsApp reminder for appointment #{$appointment->id}");
        }

        $this->info('All WhatsApp reminders have been queued successfully!');

        return 0;
    }
}
