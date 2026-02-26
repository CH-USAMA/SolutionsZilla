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
                ->addColumn('status', function ($row) {
                    $checked = $row->is_active ? 'checked' : '';
                    $route = route('staff.toggle-status', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('PATCH');

                    return '
                    <form action="' . $route . '" method="POST" class="inline" id="toggle-form-' . $row->id . '">
                        ' . $csrf . '
                        ' . $method . '
                        <label class="inline-flex items-center cursor-pointer m-0">
                            <input type="checkbox" class="sr-only peer" ' . $checked . ' onchange="document.getElementById(\'toggle-form-' . $row->id . '\').submit()">
                            <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 shadow-inner">
                            </div>
                        </label>
                    </form>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('staff.edit', $row->id);
                    $deleteUrl = route('staff.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '
                    <div class="flex items-center gap-3">
                        <a href="' . $editUrl . '" class="text-indigo-600 hover:text-indigo-900 transition flex items-center gap-1 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </a>
                        <form action="' . $deleteUrl . '" method="POST" class="inline m-0" onsubmit="return confirm(\'Are you sure you want to delete this receptionist? This action cannot be undone.\');">
                            ' . $csrf . '
                            ' . $method . '
                            <button type="submit" class="text-red-500 hover:text-red-700 transition flex items-center gap-1 text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>';
                })
                ->editColumn('created_at', function ($row) {
                    return '<span class="text-gray-500 text-sm">' . $row->created_at->format('M d, Y') . '</span>';
                })
                ->rawColumns(['clinic_name', 'status', 'action', 'created_at'])
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $staff)
    {
        if ($staff->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        if (!$staff->isReceptionist()) {
            abort(404);
        }

        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Receptionist account deleted successfully.');
    }

    /**
     * Toggle the active status of the receptionist.
     */
    public function toggleStatus(User $staff)
    {
        if ($staff->clinic_id !== auth()->user()->clinic_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        if (!$staff->isReceptionist()) {
            abort(404);
        }

        $staff->update(['is_active' => !$staff->is_active]);

        return redirect()->back()
            ->with('success', 'Receptionist status updated successfully.');
    }
}
