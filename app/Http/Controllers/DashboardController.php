<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;

        // Get date range from request or default to today
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::today();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::today();

        // Today's appointments (filtered by date range)
        $todayAppointments = Appointment::forClinic($clinicId)
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->with(['patient', 'doctor'])
            ->orderBy('appointment_time')
            ->get();

        // KPIs (filtered by date range)
        $todayTotal = $todayAppointments->count();
        $todayConfirmed = $todayAppointments->where('status', 'confirmed')->count();
        $todayCompleted = $todayAppointments->where('status', 'completed')->count();

        // No-shows this month
        $monthlyNoShows = Appointment::forClinic($clinicId)
            ->whereMonth('appointment_date', Carbon::now()->month)
            ->whereYear('appointment_date', Carbon::now()->year)
            ->where('status', 'no_show')
            ->count();

        // Upcoming appointments (next 7 days from end date)
        $upcomingAppointments = Appointment::forClinic($clinicId)
            ->whereBetween('appointment_date', [$endDate->copy()->addDay(), $endDate->copy()->addDays(7)])
            ->whereIn('status', ['booked', 'confirmed'])
            ->with(['patient', 'doctor'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(50)
            ->get();

        // Additional stats
        $totalPatients = Patient::where('clinic_id', $clinicId)->count();
        $activeReceptionists = User::where('clinic_id', $clinicId)
            ->where('role', User::ROLE_RECEPTIONIST)
            ->where('is_active', true)
            ->count();

        return view('dashboard', compact(
            'todayAppointments',
            'todayTotal',
            'todayConfirmed',
            'todayCompleted',
            'monthlyNoShows',
            'upcomingAppointments',
            'startDate',
            'endDate',
            'totalPatients',
            'activeReceptionists'
        ));
    }
}
