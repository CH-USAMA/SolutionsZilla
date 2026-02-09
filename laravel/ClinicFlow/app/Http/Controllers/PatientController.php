<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $patients = Patient::forClinic(auth()->user()->clinic_id)->latest();

            return \Yajra\DataTables\Facades\DataTables::of($patients)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $viewUrl = route('patients.show', $row->id);
                    $editUrl = route('patients.edit', $row->id);
                    $btn = '<a href="' . $viewUrl . '" class="text-indigo-600 hover:text-indigo-900 mr-2">View</a>';
                    $btn .= '<a href="' . $editUrl . '" class="text-gray-600 hover:text-gray-900">Edit</a>';
                    return $btn;
                })
                ->editColumn('age_gender', function ($row) {
                    $age = $row->age ? $row->age . ' yrs' : '-';
                    $gender = ucfirst($row->gender ?? '-');
                    return $age . ' / ' . $gender;
                })
                ->addColumn('medical_history_btn', function ($row) {
                    if ($row->medical_history) {
                        return '<button onclick="showHistory(\'' . addslashes($row->medical_history) . '\', \'' . addslashes($row->name) . '\')" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View</button>';
                    }
                    return '<span class="text-gray-400 text-sm">-</span>';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('gender') == 'male' || $request->get('gender') == 'female' || $request->get('gender') == 'other') {
                        $instance->where('gender', $request->get('gender'));
                    }
                    if (!empty($request->get('search')['value'])) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search')['value'];
                            $w->orWhere('name', 'LIKE', "%$search%")
                                ->orWhere('phone', 'LIKE', "%$search%")
                                ->orWhere('email', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['action', 'medical_history_btn'])
                ->make(true);
        }

        return view('patients.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
        ]);

        Patient::create(array_merge(
            $validated,
            ['clinic_id' => $clinicId]
        ));

        return redirect()->route('patients.index')
            ->with('success', 'Patient registered successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        // Simple check instead of Policy for now to keep it simple as requested
        if ($patient->clinic_id !== auth()->user()->clinic_id) {
            abort(403);
        }

        $patient->load([
            'appointments' => function ($query) {
                $query->latest();
            }
        ]);

        return view('patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        if ($patient->clinic_id !== auth()->user()->clinic_id) {
            abort(403);
        }
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        if ($patient->clinic_id !== auth()->user()->clinic_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
        ]);

        $patient->update($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        if ($patient->clinic_id !== auth()->user()->clinic_id) {
            abort(403);
        }

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }
}
