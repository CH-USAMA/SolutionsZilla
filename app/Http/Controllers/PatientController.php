<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Traits\ApiResponse;

class PatientController extends Controller
{
    use ApiResponse;

    protected $patientService;

    public function __construct(\App\Services\PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('export')) {
            return $this->exportCsv($request);
        }

        if ($request->ajax()) {
            $user = auth()->user();
            $query = Patient::query()->with('clinic');

            if (!$user->isSuperAdmin()) {
                $query->forClinic($user->clinic_id);
            }

            $query->latest();

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('clinic_name', function ($row) {
                    return $row->clinic ? $row->clinic->name : '<span class="text-gray-400">System</span>';
                })
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
                ->rawColumns(['clinic_name', 'action', 'medical_history_btn'])
                ->make(true);
        }

        return view('patients.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clinics = auth()->user()->isSuperAdmin() ? \App\Models\Clinic::all() : [];
        return view('patients.create', compact('clinics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
            'clinic_id' => [Rule::requiredIf($user->isSuperAdmin()), 'exists:clinics,id'],
        ]);

        $clinicId = $user->isSuperAdmin() ? $validated['clinic_id'] : $user->clinic_id;
        $clinic = \App\Models\Clinic::findOrFail($clinicId);

        $this->patientService->getOrCreateByPhone($validated['phone'], $validated['name'], $clinic, $validated);

        return redirect()->route('patients.index')
            ->with('success', 'Patient registered successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        // Simple check instead of Policy for now to keep it simple as requested
        if ($patient->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $patient->load([
            'appointments' => function ($query) {
                $query->latest();
            },
            'documents' => function ($query) {
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
        if ($patient->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        if ($patient->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
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
        if ($patient->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    /**
     * Upload a document for a patient
     */
    public function uploadDocument(Request $request, Patient $patient)
    {
        if ($patient->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'], // 10MB max
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $this->patientService->uploadDocument($patient, $request->file('document'));

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Delete a patient document
     */
    public function deleteDocument(Patient $patient, $documentId)
    {
        if ($patient->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $this->patientService->deleteDocument($patient, $documentId);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Document deleted successfully.');
    }

    /**
     * Export patients to CSV
     */
    private function exportCsv(Request $request)
    {
        $user = auth()->user();
        $query = Patient::query()->with('clinic');

        if (!$user->isSuperAdmin()) {
            $query->forClinic($user->clinic_id);
        }

        $query->latest();

        // Filters matching DataTables logic
        if ($request->get('gender') == 'male' || $request->get('gender') == 'female' || $request->get('gender') == 'other') {
            $query->where('gender', $request->get('gender'));
        }

        $patients = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="patients_export_' . date('Y-m-d_H-i') . '.csv"',
        ];

        $callback = function () use ($patients, $user) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            $columns = ['ID', 'Name', 'Phone', 'Email', 'Gender', 'Age', 'Address', 'Medical History', 'Created At'];
            if ($user->isSuperAdmin()) {
                array_splice($columns, 1, 0, 'Clinic');
            }
            fputcsv($handle, $columns);

            foreach ($patients as $patient) {
                $row = [
                    $patient->id,
                    $patient->name,
                    $patient->phone,
                    $patient->email,
                    ucfirst($patient->gender),
                    $patient->age,
                    $patient->address,
                    $patient->medical_history,
                    $patient->created_at->format('Y-m-d H:i:s'),
                ];

                if ($user->isSuperAdmin()) {
                    array_splice($row, 1, 0, $patient->clinic->name ?? 'System');
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
