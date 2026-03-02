<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of all users for Super Admin.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        if ($request->ajax()) {
            $query = User::with('clinic')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('clinic_name', function ($row) {
                    return $row->clinic ? $row->clinic->name : '<span class="text-gray-400">System (Super Admin)</span>';
                })
                ->addColumn('role_badge', function ($row) {
                    $colors = [
                        'super_admin' => 'bg-purple-100 text-purple-800',
                        'clinic_admin' => 'bg-blue-100 text-blue-800',
                        'doctor' => 'bg-green-100 text-green-800',
                        'receptionist' => 'bg-yellow-100 text-yellow-800',
                    ];
                    $color = $colors[$row->role] ?? 'bg-gray-100 text-gray-800';
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $color . '">' . ucfirst(str_replace('_', ' ', $row->role)) . '</span>';
                })
                ->addColumn('plain_password_display', function ($row) {
                    if (!$row->plain_password)
                        return '<span class="text-gray-400 italic">No password</span>';
                    return '<div class="flex items-center gap-2">
                                <span id="pwd-' . $row->id . '" class="password-text text-gray-300 font-mono tracking-tighter" data-password="' . $row->plain_password . '">••••••••</span>
                                <button onclick="togglePassword(' . $row->id . ')" class="text-gray-400 hover:text-indigo-600 transition p-1">
                                    <svg id="eye-' . $row->id . '" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('super-admin.users.edit', $row->id);
                    $btn = '<a href="' . $editUrl . '" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold mr-3">Edit</a>';
                    $btn .= '<button onclick="openResetPasswordModal(' . $row->id . ', \'' . addslashes($row->name) . '\')" class="text-gray-600 hover:text-gray-900 text-xs font-bold border border-gray-200 bg-gray-50 px-2 py-1 rounded">Reset</button>';
                    return $btn;
                })
                ->rawColumns(['clinic_name', 'role_badge', 'plain_password_display', 'action'])
                ->make(true);
        }

        return view('super-admin.users.index');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        if (!auth()->user()->isSuperAdmin())
            abort(403);

        $clinics = \App\Models\Clinic::all();
        $roles = \Spatie\Permission\Models\Role::all();
        return view('super-admin.users.create', compact('clinics', 'roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin())
            abort(403);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
            'clinic_id' => ['nullable', 'exists:clinics,id'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
            'role' => $request->role,
            'clinic_id' => $request->clinic_id,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('super-admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user)
    {
        if (!auth()->user()->isSuperAdmin())
            abort(403);

        $clinics = \App\Models\Clinic::all();
        $roles = \Spatie\Permission\Models\Role::all();
        return view('super-admin.users.edit', compact('user', 'clinics', 'roles'));
    }

    /**
     * Update the user.
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin())
            abort(403);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'exists:roles,name'],
            'clinic_id' => ['nullable', 'exists:clinics,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'clinic_id' => $request->clinic_id,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['plain_password'] = $request->password;
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        return redirect()->route('super-admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Reset password for a user (Super Admin only).
     */
    public function resetPassword(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
        ]);

        return redirect()->back()->with('success', "Password for user {$user->name} has been reset successfully.");
    }
}
