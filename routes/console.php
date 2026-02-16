<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule WhatsApp reminders (checks for appointments in the next 24h/2h as configured)
Schedule::command('reminders:whatsapp')->everyFiveMinutes();

// Schedule SMS reminders (2 hours before appointment)
Schedule::command('reminders:sms')->everyFifteenMinutes();

// Schedule Daily Database Backups at 2 AM
Schedule::command('db:backup')->dailyAt('02:00');

// Schedule Monthly Usage Calculation (1st of every month at 1 AM for previous month)
Schedule::job(new \App\Jobs\CalculateMonthlyUsageJob(now()->subMonth()->month, now()->subMonth()->year))
    ->monthlyOn(1, '01:00');
