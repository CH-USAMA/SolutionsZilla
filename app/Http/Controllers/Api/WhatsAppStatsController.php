<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WhatsAppStatsController extends Controller
{
    /**
     * Get usage stats for authenticated tenant
     */
    public function stats(Request $request)
    {
        $clinicId = $request->user()->clinic_id;

        if (!$clinicId) {
            return response()->json(['error' => 'Clinic not found'], 404);
        }

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Get stored monthly usage record
        $usage = WhatsAppUsage::where('clinic_id', $clinicId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        // If current month, calculate real-time
        if (!$usage && (int) $month === now()->month && (int) $year === now()->year) {
            $startDate = now()->startOfMonth();
            $endDate = now();

            $conversationsCount = WhatsAppConversation::where('clinic_id', $clinicId)
                ->whereBetween('started_at', [$startDate, $endDate])
                ->count();

            $messagesSent = WhatsAppMessage::where('clinic_id', $clinicId)
                ->where('direction', 'outgoing')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $messagesDelivered = WhatsAppMessage::where('clinic_id', $clinicId)
                ->where('direction', 'outgoing')
                ->where('status', 'delivered')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $stats = [
                'month' => (int) $month,
                'year' => (int) $year,
                'conversations_count' => $conversationsCount,
                'messages_sent' => $messagesSent,
                'messages_delivered' => $messagesDelivered,
                'estimated_cost' => 0.00, // Placeholder
                'currency' => 'USD',
                'is_estimated' => true
            ];
        } else {
            $stats = $usage ? $usage->toArray() : null;
        }

        return response()->json([
            'data' => $stats
        ]);
    }

    /**
     * Get paginated message logs
     */
    public function messages(Request $request)
    {
        $clinicId = $request->user()->clinic_id;

        if (!$clinicId) {
            return response()->json(['error' => 'Clinic not found'], 404);
        }

        $query = WhatsAppMessage::where('clinic_id', $clinicId)
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('direction')) {
            $query->where('direction', $request->input('direction'));
        }

        if ($request->has('conversation_id')) {
            $query->where('conversation_id', $request->input('conversation_id'));
        }

        $messages = $query->paginate($request->input('per_page', 20));

        return response()->json($messages);
    }
}
