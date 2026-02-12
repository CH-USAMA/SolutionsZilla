<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs for the current clinic.
     */
    public function index(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;

        $query = ActivityLog::where('clinic_id', $clinicId)
            ->with(['user', 'loggable'])
            ->latest();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(20);

        return view('admin.logs.index', compact('logs'));
    }

    /**
     * Export logs to PDF.
     */
    public function exportPdf()
    {
        $clinicId = auth()->user()->clinic_id;
        $logs = ActivityLog::where('clinic_id', $clinicId)
            ->with(['user', 'loggable'])
            ->latest()
            ->limit(100)
            ->get();

        $pdf = Pdf::loadView('admin.logs.pdf', compact('logs'));
        return $pdf->download('activity-logs-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Super Admin view for global logs.
     */
    public function globalIndex()
    {
        // Simple check for super admin role
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $logs = ActivityLog::with(['clinic', 'user', 'loggable'])
            ->latest()
            ->paginate(50);

        return view('super-admin.logs.index', compact('logs'));
    }
}
