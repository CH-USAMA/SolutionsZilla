<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display reporting dashboard
     */
    public function index(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;

        // Date range
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Base query
        $query = Appointment::forClinic($clinicId)
            ->whereBetween('appointment_date', [$startDate, $endDate]);

        // Stats
        $totalAppointments = (clone $query)->count();
        $completedAppointments = (clone $query)->where('status', 'completed')->count();
        $noShows = (clone $query)->where('status', 'no_show')->count();
        $cancelled = (clone $query)->where('status', 'cancelled')->count();

        // Revenue Loss estimation (based on average consultation fee)
        // This is a simple calculation as requested
        $averageFee = 1500; // Default average fee if not calculable

        // Try to calculate actual potential revenue from doctors' fees
        $doctors = \App\Models\Doctor::forClinic($clinicId)->get();
        if ($doctors->count() > 0) {
            $averageFee = $doctors->avg('consultation_fee');
        }

        $estimatedLoss = $noShows * $averageFee;
        $estimatedRevenue = $completedAppointments * $averageFee;

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'totalAppointments',
            'completedAppointments',
            'noShows',
            'cancelled',
            'estimatedLoss',
            'estimatedRevenue'
        ));
    }
}
