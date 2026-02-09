<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;

        if ($request->ajax()) {
            $query = Appointment::forClinic($clinicId)->with(['patient', 'doctor']);

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('appointment_time', function ($row) {
                    return '<div class="font-bold">' . date('h:i A', strtotime($row->appointment_time)) . '</div>' .
                        '<div class="text-xs text-gray-500">' . $row->appointment_date->format('M d, Y') . '</div>';
                })
                ->addColumn('patient_name', function ($row) {
                    return '<div class="text-sm font-medium text-gray-900">' . $row->patient->name . '</div>' .
                        '<div class="text-sm text-gray-500">' . $row->patient->phone . '</div>';
                })
                ->addColumn('doctor_name', function ($row) {
                    return $row->doctor->name;
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
                ->rawColumns(['appointment_time', 'patient_name', 'status', 'action'])
                ->make(true);
        }

        $doctors = Doctor::forClinic($clinicId)->where('is_available', true)->get();

        return view('appointments.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clinicId = auth()->user()->clinic_id;
        $doctors = Doctor::forClinic($clinicId)->where('is_available', true)->get();

        return view('appointments.create', compact('doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $clinicId = auth()->user()->clinic_id;

        // Find or create patient
        $patient = Patient::firstOrCreate(
            [
                'clinic_id' => $clinicId,
                'phone' => $request->patient_phone
            ],
            [
                'name' => $request->patient_name,
                'email' => $request->patient_email,
            ]
        );

        // Check for double booking (simple check)
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['booked', 'confirmed'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['appointment_time' => 'This time slot is already booked for this doctor.']);
        }

        Appointment::create([
            'clinic_id' => $clinicId,
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'booked',
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointments.index', ['date' => $request->appointment_date])
            ->with('success', 'Appointment booked successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        if ($appointment->clinic_id !== auth()->user()->clinic_id) {
            abort(403);
        }
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Update the specified resource status.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        if ($appointment->clinic_id !== auth()->user()->clinic_id) {
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
        if ($appointment->clinic_id !== auth()->user()->clinic_id) {
            abort(403);
        }

        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }
}
