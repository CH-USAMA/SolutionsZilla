<?php

namespace App\Jobs;

use App\Models\Clinic;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppUsage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalculateMonthlyUsageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $month;
    protected $year;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($month = null, $year = null)
    {
        $this->month = $month ?? now()->month;
        $this->year = $year ?? now()->year;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $month = $this->month;
        $year = $this->year;

        // Process all active clinics
        Clinic::where('is_active', true)->chunk(100, function ($clinics) use ($month, $year) {
            /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Clinic[] $clinics */
            foreach ($clinics as $clinic) {
                $this->calculateForClinic($clinic, $month, $year);
            }
        });
    }

    /**
     * Calculate usage for a single clinic
     */
    protected function calculateForClinic(Clinic $clinic, $month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // 1. Count Conversations
        $conversations = WhatsAppConversation::where('clinic_id', $clinic->id)
            ->whereBetween('started_at', [$startDate, $endDate])
            ->get();

        $conversationsCount = $conversations->count();

        // Breakdown by category usually
        $breakdown = $conversations->groupBy('category')->map->count();

        // 2. Count Messages
        $messagesSent = WhatsAppMessage::where('clinic_id', $clinic->id)
            ->where('direction', 'outgoing')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $messagesDelivered = WhatsAppMessage::where('clinic_id', $clinic->id)
            ->where('direction', 'outgoing')
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // 3. Estimate Cost (Simplified Example - Real cost depends on country/category)
        // Marketing: ~$0.01, Utility: ~$0.005, Service: Free (first 1000)
        // This is a placeholder estimation logic
        $estimatedCost = $conversations->sum('cost');

        // 4. Update or Create Usage Record
        WhatsAppUsage::updateOrCreate(
            [
                'clinic_id' => $clinic->id,
                'month' => $month,
                'year' => $year,
            ],
            [
                'conversations_count' => $conversationsCount,
                'messages_sent' => $messagesSent,
                'messages_delivered' => $messagesDelivered,
                'estimated_cost' => $estimatedCost,
                'breakdown' => $breakdown,
                'currency' => 'USD'
            ]
        );
    }
}
