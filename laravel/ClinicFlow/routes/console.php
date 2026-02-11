<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule WhatsApp reminders (24 hours before appointment)
Schedule::command('reminders:whatsapp')->everySecond();

// Schedule SMS reminders (2 hours before appointment)
Schedule::command('reminders:sms')->everyFifteenMinutes();
