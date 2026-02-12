<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Clinic;
use App\Models\BillingLog;
use App\Models\Plan;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use Carbon\Carbon;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        // Core metrics
        $metrics = [
            'total_revenue' => BillingLog::where('status', 'paid')->sum('amount'),
            'total_clinics' => Clinic::count(),
            'active_subscriptions' => Clinic::where('subscription_status', 'active')->count(),
            'total_appointments' => Appointment::withoutGlobalScopes()->count(),
            'total_patients' => Patient::withoutGlobalScopes()->count(),
            'total_doctors' => Doctor::withoutGlobalScopes()->count(),
            'total_users' => User::where('role', '!=', 'super_admin')->count(),
        ];

        $recent_billings = BillingLog::with(['clinic', 'plan'])
            ->latest()
            ->limit(10)
            ->get();

        $plan_distribution = Plan::withCount('clinics')->get();

        // Monthly user registration data (last 6 months)
        $userChartLabels = [];
        $userChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $userChartLabels[] = $date->format('M Y');
            $userChartData[] = User::where('role', '!=', 'super_admin')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Monthly revenue data (last 6 months)
        $revenueChartLabels = [];
        $revenueChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenueChartLabels[] = $date->format('M Y');
            $revenueChartData[] = (float) BillingLog::where('status', 'paid')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
        }

        // Monthly appointment data (last 6 months)
        $appointmentChartLabels = [];
        $appointmentChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $appointmentChartLabels[] = $date->format('M Y');
            $appointmentChartData[] = Appointment::withoutGlobalScopes()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Recent clinics
        $recent_clinics = Clinic::with('plan')
            ->withCount(['users', 'appointments'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'metrics',
            'recent_billings',
            'plan_distribution',
            'userChartLabels',
            'userChartData',
            'revenueChartLabels',
            'revenueChartData',
            'appointmentChartLabels',
            'appointmentChartData',
            'recent_clinics'
        ));
    }
}
