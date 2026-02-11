<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display reporting dashboard
     */
    public function index(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;

        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportCsv($request);
        }

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

    /**
     * Export report data to CSV
     */
    private function exportCsv(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $query = Appointment::forClinic($clinicId)
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->with(['patient', 'doctor']);

        $appointments = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="report_export_' . $startDate . '_to_' . $endDate . '.csv"',
        ];

        $callback = function () use ($appointments) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, ['ID', 'Date', 'Time', 'Patient', 'Doctor', 'Status', 'Created At']);

            foreach ($appointments as $appointment) {
                fputcsv($handle, [
                    $appointment->id,
                    $appointment->appointment_date->format('Y-m-d'),
                    $appointment->appointment_time,
                    $appointment->patient->name ?? 'Deleted Patient',
                    $appointment->doctor->name ?? 'Deleted Doctor',
                    ucfirst($appointment->status),
                    $appointment->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
