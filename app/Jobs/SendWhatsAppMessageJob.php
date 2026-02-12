<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\ClinicWhatsappSetting;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public $backoff = [10, 60, 300, 600]; // Incremental backoff

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $clinicId,
        protected string $recipient,
        protected string $messageType, // 'template' or 'text'
        protected array $params = [],
        protected ?int $appointmentId = null
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $service): void
    {
        $settings = ClinicWhatsappSetting::withoutGlobalScopes()
            ->where('clinic_id', $this->clinicId)
            ->where('is_active', true)
            ->first();

        if (!$settings) {
            Log::warning("SendWhatsAppMessageJob: No active settings found for clinic {$this->clinicId}");
            return;
        }

        $success = false;

        if ($this->messageType === 'text') {
            $success = $service->sendSimpleMessage(
                $settings,
                $this->recipient,
                $this->params,
                $this->appointmentId
            );
        } else {
            $success = $service->sendTemplateMessage(
                $settings,
                $this->recipient,
                $this->params['template_name'] ?? $settings->default_template,
                $this->params,
                $this->appointmentId
            );
        }

        if (!$success) {
            // If it failed, we throw an exception to trigger the retry logic
            // providing the backoff defined above
            throw new \Exception("Failed to send WhatsApp message to {$this->recipient}. Retrying...");
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendWhatsAppMessageJob: Job permanently failed after {$this->tries} attempts", [
            'clinic_id' => $this->clinicId,
            'recipient' => $this->recipient,
            'error' => $exception->getMessage()
        ]);
    }
}
