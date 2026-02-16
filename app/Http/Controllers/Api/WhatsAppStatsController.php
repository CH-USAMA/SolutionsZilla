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
        $user = $request->user();
        $clinicId = $user->isSuperAdmin() ? $request->input('clinic_id') : $user->clinic_id;

        if (!$clinicId && !$user->isSuperAdmin()) {
            return response()->json(['error' => 'Clinic not found'], 404);
        }

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Collective stats for Super Admin if no clinic_id
        if ($user->isSuperAdmin() && !$clinicId) {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $conversationsCount = WhatsAppConversation::whereBetween('started_at', [$startDate, $endDate])->count();
            $messagesSent = WhatsAppMessage::where('direction', 'outgoing')->whereBetween('created_at', [$startDate, $endDate])->count();
            $messagesDelivered = WhatsAppMessage::where('direction', 'outgoing')->where('status', 'delivered')->whereBetween('created_at', [$startDate, $endDate])->count();

            return response()->json([
                'data' => [
                    'month' => (int) $month,
                    'year' => (int) $year,
                    'conversations_count' => $conversationsCount,
                    'messages_sent' => $messagesSent,
                    'messages_delivered' => $messagesDelivered,
                    'estimated_cost' => (float) WhatsAppConversation::whereBetween('started_at', [$startDate, $endDate])->sum('cost'),
                    'is_collective' => true
                ]
            ]);
        }

        // Get stored monthly usage record
        $usage = WhatsAppUsage::where('clinic_id', $clinicId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        // If current month or no usage record, calculate real-time
        if (!$usage || ((int) $month === now()->month && (int) $year === now()->year)) {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = ($month == now()->month && $year == now()->year) ? now() : $startDate->copy()->endOfMonth();

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
                'estimated_cost' => (float) WhatsAppConversation::where('clinic_id', $clinicId)->whereBetween('started_at', [$startDate, $endDate])->sum('cost'),
                'currency' => 'USD',
                'is_estimated' => true
            ];
        } else {
            $stats = $usage->toArray();
        }

        return response()->json(['data' => $stats]);
    }

    /**
     * Get paginated message logs
     */
    public function messages(Request $request)
    {
        $user = $request->user();
        $clinicId = $user->isSuperAdmin() ? $request->input('clinic_id') : $user->clinic_id;

        if (!$clinicId && !$user->isSuperAdmin()) {
            return response()->json(['error' => 'Clinic not found'], 404);
        }

        $query = WhatsAppMessage::with('clinic')->orderBy('created_at', 'desc');

        if ($clinicId) {
            $query->where('clinic_id', $clinicId);
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('direction')) {
            $query->where('direction', $request->input('direction'));
        }

        $messages = $query->paginate($request->input('per_page', 20));

        return response()->json($messages);
    }
}
