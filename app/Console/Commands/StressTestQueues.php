<?php

namespace App\Console\Commands;

use App\Models\Clinic;
use App\Models\ClinicWhatsappSetting;
use App\Models\WhatsAppMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Support\Str;

class StressTestQueues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stress-queues {count=1000 : Number of jobs to dispatch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch a large number of dummy WhatsApp jobs to test queue performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $this->info("Dispatching $count dummy jobs...");

        // Ensure we have a clinic to attach to
        $clinic = Clinic::first();
        if (!$clinic) {
            $clinic = Clinic::factory()->create();
            $this->info("Created temporary clinic ID: {$clinic->id}");
        }

        // Ensure settings exist
        if (!$clinic->whatsappSettings) {
            ClinicWhatsappSetting::create([
                'clinic_id' => $clinic->id,
                'phone_number_id' => '1234567890',
                'waba_id' => 'waba_stress_test',
                'display_phone_number' => '+1234567890',
                'access_token' => 'stress_test_token',
                'is_active' => true,
            ]);
        }

        $startTime = microtime(true);

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            \App\Jobs\SendWhatsAppMessageJob::dispatch(
                $clinic->id,
                '1234567890',
                'text',
                ['message' => "Stress Test Message $i"],
                null
            );

            $bar->advance();
        }

        $bar->finish();
        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        $this->newLine();
        $this->info("Dispatched $count jobs in " . number_format($duration, 2) . " seconds.");
        $this->info("Rate: " . number_format($count / $duration, 2) . " jobs/sec");

        $this->info("Run 'php artisan queue:work' to process them and monitor valid processing.");
    }
}
