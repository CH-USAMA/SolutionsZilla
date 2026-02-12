<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(\App\Services\AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('export')) {
            return $this->exportCsv($request);
        }

        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();

        if ($request->ajax()) {
            $query = Appointment::query()->with(['patient', 'doctor', 'clinic']);

            if (!$isSuperAdmin) {
                $query->forClinic($user->clinic_id);
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('clinic_name', function ($row) {
                    return $row->clinic ? $row->clinic->name : '<span class="text-gray-400">System</span>';
                })
                ->editColumn('appointment_time', function ($row) {
                    return '<div class="font-bold">' . date('h:i A', strtotime($row->appointment_time)) . '</div>' .
                        '<div class="text-xs text-gray-500">' . $row->appointment_date->format('M d, Y') . '</div>';
                })
                ->addColumn('patient_name', function ($row) {
                    $name = $row->patient ? $row->patient->name : '<span class="text-red-500">Deleted Patient</span>';
                    $phone = $row->patient ? $row->patient->phone : '';
                    return '<div class="text-sm font-medium text-gray-900">' . $name . '</div>' .
                        '<div class="text-sm text-gray-500">' . $phone . '</div>';
                })
                ->addColumn('doctor_name', function ($row) {
                    return $row->doctor ? $row->doctor->name : '<span class="text-red-500">Deleted Doctor</span>';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = match ($row->status) {
                        'confirmed' => 'bg-green-100 text-green-800',
                        'completed' => 'bg-gray-100 text-gray-800',
                        'cancelled', 'no_show' => 'bg-red-100 text-red-800',
                        default => 'bg-yellow-100 text-yellow-800',
                    };
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $statusClass . '">' .
                        ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $viewUrl = route('appointments.show', $row->id);
                    $btn = '<a href="' . $viewUrl . '" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>';

                    if ($row->status === 'booked') {
                        $btn .= '<form action="' . route('appointments.update-status', $row->id) . '" method="POST" class="inline">
                            ' . csrf_field() . method_field('PATCH') . '
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" class="text-green-600 hover:text-green-900 mr-2">Confirm</button>
                        </form>';
                    }

                    if (in_array($row->status, ['booked', 'confirmed'])) {
                        $btn .= '<form action="' . route('appointments.update-status', $row->id) . '" method="POST" class="inline">
                            ' . csrf_field() . method_field('PATCH') . '
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="text-blue-600 hover:text-blue-900">Complete</button>
                        </form>';
                    }
                    return $btn;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->filled('date')) {
                        $instance->whereDate('appointment_date', $request->date);
                    }
                    if ($request->filled('doctor_id')) {
                        $instance->where('doctor_id', $request->doctor_id);
                    }
                    if ($request->filled('status')) {
                        $instance->where('status', $request->status);
                    }
                    if (!empty($request->get('search')['value'])) { // Basic search if needed
                        $instance->whereHas('patient', function ($q) use ($request) {
                            $search = $request->get('search')['value'];
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['appointment_time', 'patient_name', 'status', 'action', 'clinic_name'])
                ->make(true);
        }

        $doctorQuery = Doctor::query()->where('is_available', true);
        if (!$isSuperAdmin) {
            $doctorQuery->forClinic($user->clinic_id);
        }
        $doctors = $doctorQuery->get();

        return view('appointments.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();

        $doctorQuery = Doctor::query()->where('is_available', true);
        if (!$isSuperAdmin) {
            $doctorQuery->forClinic($user->clinic_id);
        }
        $doctors = $doctorQuery->get();

        return view('appointments.create', compact('doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        try {
            $clinic = auth()->user()->clinic;

            $data = [
                'name' => $request->patient_name,
                'phone' => $request->patient_phone,
                'email' => $request->patient_email,
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'reason' => $request->notes, // notes used as reason
            ];

            $this->appointmentService->bookAppointment($data, $clinic);

            return redirect()->route('appointments.index', ['date' => $request->appointment_date])
                ->with('success', 'Appointment booked successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        if ($appointment->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Update the specified resource status.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        if ($appointment->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'status' => ['required', 'in:booked,confirmed,cancelled,completed,no_show'],
            'cancellation_reason' => ['nullable', 'required_if:status,cancelled', 'string'],
        ]);

        $appointment->update([
            'status' => $request->status,
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return back()->with('success', 'Appointment status updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        if ($appointment->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Export appointments to CSV
     */
    private function exportCsv(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();

        $query = Appointment::query()->with(['patient', 'doctor', 'clinic']);

        if (!$isSuperAdmin) {
            $query->forClinic($user->clinic_id);
        }

        // Apply filters
        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->latest('appointment_date');

        $appointments = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="appointments_export_' . date('Y-m-d_H-i') . '.csv"',
        ];

        $callback = function () use ($appointments, $isSuperAdmin) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            $columns = ['ID', 'Date', 'Time', 'Patient', 'Doctor', 'Clinic', 'Status', 'Created At'];
            if (!$isSuperAdmin) {
                // Remove Clinic column if not super admin, though logic above adds it. Let's keep it simple.
                // Actually, let's keep it consistent.
            }
            fputcsv($handle, $columns);

            foreach ($appointments as $appointment) {
                $row = [
                    $appointment->id,
                    $appointment->appointment_date->format('Y-m-d'),
                    $appointment->appointment_time,
                    $appointment->patient->name ?? 'Deleted Patient',
                    $appointment->doctor->name ?? 'Deleted Doctor',
                    $appointment->clinic->name ?? 'System',
                    ucfirst($appointment->status),
                    $appointment->created_at->format('Y-m-d H:i:s'),
                ];
                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
