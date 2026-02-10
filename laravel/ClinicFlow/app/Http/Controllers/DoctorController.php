<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $query = Doctor::query();

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

            $doctors = $query; // DataTables accepts query builder

            return \Yajra\DataTables\Facades\DataTables::of($doctors)
                ->addIndexColumn()
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
                ->rawColumns(['action', 'is_available'])
                ->make(true);
        }

        return view('doctors.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('doctors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        $clinicId = auth()->user()->clinic_id;

        $validated = $request->validated();
        $validated['is_available'] = $request->has('is_available');

        Doctor::create(array_merge(
            $validated,
            ['clinic_id' => $clinicId]
        ));

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor created successfully.');
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
}
