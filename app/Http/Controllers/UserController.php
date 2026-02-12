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
                ->addColumn('action', function ($row) {
                    $btn = '<button onclick="openResetPasswordModal(' . $row->id . ', \'' . addslashes($row->name) . '\')" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold border border-indigo-200 bg-indigo-50 px-2 py-1 rounded">Reset Password</button>';
                    return $btn;
                })
                ->rawColumns(['clinic_name', 'role_badge', 'action'])
                ->make(true);
        }

        return view('super-admin.users.index');
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
        ]);

        return redirect()->back()->with('success', "Password for user {$user->name} has been reset successfully.");
    }
}
