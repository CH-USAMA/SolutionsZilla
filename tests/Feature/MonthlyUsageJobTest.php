<?php

namespace Tests\Feature;

use App\Jobs\CalculateMonthlyUsageJob;
use App\Models\Clinic;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MonthlyUsageJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_usage_correctly()
    {
        // Allow mass assignment of created_at
        WhatsAppMessage::unguard();
        WhatsAppConversation::unguard();

        // 1. Setup Data
        $month = 1;
        $year = 2026;
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        $clinic = Clinic::factory()->create(['is_active' => true]);

        // Create 2 Conversations in Jan 2026
        WhatsAppConversation::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => 'conv_1',
            'phone_number' => '1234567890',
            'started_at' => $startDate->copy()->addDay(),
            'expires_at' => $startDate->copy()->addDay()->addHours(24),
            'type' => 'marketing',
            'category' => 'marketing',
            'cost' => 0.05,
        ]);

        WhatsAppConversation::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => 'conv_2',
            'phone_number' => '0987654321',
            'started_at' => $startDate->copy()->addDays(5),
            'expires_at' => $startDate->copy()->addDays(5)->addHours(24),
            'type' => 'service',
            'category' => 'service',
            'cost' => 0.00,
        ]);

        // Create 1 Conversation in Feb 2026 (Should be ignored)
        WhatsAppConversation::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => 'conv_3',
            'phone_number' => '1112223333',
            'started_at' => $startDate->copy()->addMonth(),
            'expires_at' => $startDate->copy()->addMonth()->addHours(24),
            'type' => 'marketing',
            'category' => 'marketing',
            'cost' => 0.05,
        ]);

        // Create Messages 
        // 3 sent within Jan
        WhatsAppMessage::create([
            'clinic_id' => $clinic->id,
            'message_id' => 'msg_1',
            'direction' => 'outgoing',
            'from' => '1234567890',
            'to' => '0987654321',
            'status' => 'delivered',
            'created_at' => $startDate->copy()->addDay(),
        ]);
        WhatsAppMessage::create([
            'clinic_id' => $clinic->id,
            'message_id' => 'msg_2',
            'direction' => 'outgoing',
            'from' => '1234567890',
            'to' => '0987654321',
            'status' => 'sent', // not delivered
            'created_at' => $startDate->copy()->addDay(),
        ]);
        WhatsAppMessage::create([
            'clinic_id' => $clinic->id,
            'message_id' => 'msg_3',
            'direction' => 'outgoing',
            'from' => '1234567890',
            'to' => '0987654321',
            'status' => 'delivered',
            'created_at' => $startDate->copy()->addDays(5),
        ]);

        // 1 incoming (ignored for sent count)
        WhatsAppMessage::create([
            'clinic_id' => $clinic->id,
            'message_id' => 'msg_4',
            'direction' => 'incoming',
            'from' => '0987654321',
            'to' => '1234567890',
            'created_at' => $startDate->copy()->addDay(),
        ]);

        // 2. Run Job
        $job = new CalculateMonthlyUsageJob($month, $year);
        $job->handle();

        // 3. Verify Usage Record
        $this->assertDatabaseHas('whatsapp_usage', [
            'clinic_id' => $clinic->id,
            'month' => $month,
            'year' => $year,
            'conversations_count' => 2, // conv_1, conv_2
            'messages_sent' => 3,       // msg_1, msg_2, msg_3
            'messages_delivered' => 2,  // msg_1, msg_3
            'estimated_cost' => 0.05,   // 0.05 + 0.00
        ]);

        // Verify Feb 2026 record does not exist
        $this->assertDatabaseMissing('whatsapp_usage', [
            'clinic_id' => $clinic->id,
            'month' => 2,
            'year' => 2026,
        ]);
    }
}
