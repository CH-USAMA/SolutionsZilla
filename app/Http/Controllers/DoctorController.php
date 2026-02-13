<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DoctorController extends Controller
{
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
            $query = Doctor::query()->with('clinic');

            if ($user->isSuperAdmin()) {
                // Super Admin sees all doctors
                $query->latest();
            } elseif ($user->isDoctor()) {
                // Doctor sees only their own profile
                $query->where('user_id', $user->id);
            } else {
                // Clinic Admin / Receptionist sees doctors for their clinic
                $query->forClinic($user->clinic_id)->latest();
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('clinic_name', function ($row) {
                    return $row->clinic ? $row->clinic->name : '<span class="text-gray-400">System</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('doctors.edit', $row->id);
                    $btn = '<a href="' . $editUrl . '" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>';
                    return $btn;
                })
                ->editColumn('is_available', function ($row) {
                    if ($row->is_available) {
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>';
                    }
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Unavailable</span>';
                })
                ->rawColumns(['clinic_name', 'action', 'is_available'])
                ->make(true);
        }

        return view('doctors.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clinics = auth()->user()->isSuperAdmin() ? \App\Models\Clinic::all() : [];
        return view('doctors.create', compact('clinics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        $user = auth()->user();
        $clinicId = $user->isSuperAdmin() ? $request->clinic_id : $user->clinic_id;

        if ($user->isSuperAdmin() && !$clinicId) {
            return back()->withInput()->withErrors(['clinic_id' => 'Clinic is required for Super Admin.']);
        }

        $validated = $request->validated();
        $validated['is_available'] = $request->has('is_available');

        // Require email and password for login
        if (empty($validated['email'])) {
            return back()->withInput()->withErrors(['email' => 'Email is required for doctor login account.']);
        }

        $password = $request->input('password', 'password123'); // Default or input

        // Create User Account
        $doctorUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => User::ROLE_DOCTOR,
            'clinic_id' => $clinicId,
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        // Create Doctor Profile
        Doctor::create(array_merge(
            $validated,
            [
                'clinic_id' => $clinicId,
                'user_id' => $doctorUser->id,
            ]
        ));

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor and User Account created successfully. Default password: ' . $password);
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        $this->authorize('view', $doctor);

        $doctor->load([
            'appointments' => function ($query) {
                $query->latest()->limit(5);
            }
        ]);

        return view('doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        $this->authorize('update', $doctor);
        return view('doctors.edit', compact('doctor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $this->authorize('update', $doctor);

        $validated = $request->validated();
        $validated['is_available'] = $request->has('is_available');

        // Update User Password if provided
        if ($request->filled('password') && $doctor->user) {
            $doctor->user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        // Remove password from doctor model update
        unset($validated['password']);
        unset($validated['password_confirmation']);

        $doctor->update($validated);

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        $this->authorize('delete', $doctor);

        $doctor->delete();

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor deleted successfully.');
    }

    /**
     * Export doctors to CSV
     */
    private function exportCsv(Request $request)
    {
        $user = auth()->user();
        $query = Doctor::query()->with('clinic');

        if ($user->isSuperAdmin()) {
            $query->latest();
        } elseif ($user->isDoctor()) {
            $query->where('user_id', $user->id);
        } else {
            $query->forClinic($user->clinic_id)->latest();
        }

        if ($request->status !== null) {
            // Note: status comes as string "1" or "0" from the filter
            if ($request->status === '1')
                $query->where('is_available', true);
            if ($request->status === '0')
                $query->where('is_available', false);
        }

        $doctors = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="doctors_export_' . date('Y-m-d_H-i') . '.csv"',
        ];

        $callback = function () use ($doctors, $user) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            $columns = ['ID', 'Name', 'Specialization', 'Phone', 'Email', 'Status', 'Created At'];
            if ($user->isSuperAdmin()) {
                array_splice($columns, 1, 0, 'Clinic');
            }
            fputcsv($handle, $columns);

            foreach ($doctors as $doctor) {
                $row = [
                    $doctor->id,
                    $doctor->name,
                    $doctor->specialization,
                    $doctor->phone,
                    $doctor->email,
                    $doctor->is_available ? 'Available' : 'Unavailable',
                    $doctor->created_at->format('Y-m-d H:i:s'),
                ];

                if ($user->isSuperAdmin()) {
                    array_splice($row, 1, 0, $doctor->clinic->name ?? 'System');
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
