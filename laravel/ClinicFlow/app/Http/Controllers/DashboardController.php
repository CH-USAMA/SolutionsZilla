<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        $clinicId = auth()->user()->clinic_id;

        // Today's appointments
        $todayAppointments = Appointment::forClinic($clinicId)
            ->today()
            ->with(['patient', 'doctor'])
            ->orderBy('appointment_time')
            ->get();

        // KPIs
        $todayTotal = $todayAppointments->count();
        $todayConfirmed = $todayAppointments->where('status', 'confirmed')->count();
        $todayCompleted = $todayAppointments->where('status', 'completed')->count();

        // No-shows this month
        $monthlyNoShows = Appointment::forClinic($clinicId)
            ->whereMonth('appointment_date', Carbon::now()->month)
            ->whereYear('appointment_date', Carbon::now()->year)
            ->where('status', 'no_show')
            ->count();

        // Upcoming appointments (next 7 days)
        $upcomingAppointments = Appointment::forClinic($clinicId)
            ->whereBetween('appointment_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->whereIn('status', ['booked', 'confirmed'])
            ->with(['patient', 'doctor'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'todayAppointments',
            'todayTotal',
            'todayConfirmed',
            'todayCompleted',
            'monthlyNoShows',
            'upcomingAppointments'
        ));
    }
}
