<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppUsage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WhatsAppDashboardController extends Controller
{
    /**
     * Display the WhatsApp dashboard.
     */
    public function index(Request $request)
    {
        $clinicId = $request->user()->clinic_id; // Assuming user has clinic_id

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Stats
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

        // Approximate cost (placeholder logic)
        // In reality, you'd sum up 'cost' column from conversations
        $estimatedCost = WhatsAppConversation::where('clinic_id', $clinicId)
            ->whereBetween('started_at', [$startDate, $endDate])
            ->sum('cost');

        // Recent Messages
        $recentMessages = WhatsAppMessage::where('clinic_id', $clinicId)
            ->with('conversation')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('clinic.whatsapp.dashboard', compact(
            'month',
            'year',
            'conversationsCount',
            'messagesSent',
            'messagesDelivered',
            'estimatedCost',
            'recentMessages'
        ));
    }
}
