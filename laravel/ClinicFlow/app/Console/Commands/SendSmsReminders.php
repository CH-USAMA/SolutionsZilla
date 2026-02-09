<?php

namespace App\Console\Commands;

use App\Jobs\SendSmsReminder;
use App\Models\Appointment;
use Illuminate\Console\Command;

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
    protected $description = 'Send SMS reminders for appointments 2 hours in advance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for appointments needing SMS reminders...');

        $appointments = Appointment::with(['patient', 'doctor', 'clinic'])
            ->needingSmsReminder()
            ->get();

        $count = $appointments->count();

        if ($count === 0) {
            $this->info('No appointments need SMS reminders at this time.');
            return 0;
        }

        $this->info("Found {$count} appointment(s) needing SMS reminders.");

        foreach ($appointments as $appointment) {
            SendSmsReminder::dispatch($appointment);
            $this->line("Queued SMS reminder for appointment #{$appointment->id}");
        }

        $this->info('All SMS reminders have been queued successfully!');

        return 0;
    }
}
