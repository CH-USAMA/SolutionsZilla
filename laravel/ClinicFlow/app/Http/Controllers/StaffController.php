<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class StaffController extends Controller
{
    /**
     * Display a listing of the receptionists.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $query = User::where('role', User::ROLE_RECEPTIONIST);

            if (!$user->isSuperAdmin()) {
                $query->where('clinic_id', $user->clinic_id);
            }

            $query->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('clinic_name', function ($row) {
                    return $row->clinic ? $row->clinic->name : '<span class="text-gray-400">System</span>';
                })
                ->addColumn('action', function ($row) {
                    // Start with simple Edit button (we reuse user editing or specific staff edit)
                    // For now, simple edit button or delete
                    $btn = '<a href="' . route('staff.edit', $row->id) . '" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>';
                    return $btn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->rawColumns(['clinic_name', 'action'])
                ->make(true);
        }

        return view('staff.index');
    }

    /**
     * Show the form for creating a new receptionist.
     */
    public function create()
    {
        return view('staff.create');
    }

    /**
     * Store a newly created receptionist in storage.
     */
    public function store(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_RECEPTIONIST,
            'clinic_id' => $clinicId,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Receptionist account created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $staff)
    {
        // Simple security check
        if ($staff->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        if (!$staff->isReceptionist()) {
            abort(404);
        }

        return view('staff.edit', compact('staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $staff)
    {
        if ($staff->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($staff->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        return redirect()->route('staff.index')
            ->with('success', 'Receptionist account updated successfully.');
    }
}
