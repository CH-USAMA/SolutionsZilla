<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClinicManagementController extends Controller
{
    /**
     * Display all clinics with their current plans for Super Admin management.
     */
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $clinics = Clinic::with('plan')
            ->withCount(['users', 'doctors', 'patients', 'appointments'])
            ->latest()
            ->get();

        $plans = Plan::where('is_active', true)->get();

        return view('super-admin.clinics', compact('clinics', 'plans'));
    }

    /**
     * Show the form for creating a new clinic.
     */
    public function create()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        return view('super-admin.clinics.create');
    }

    /**
     * Store a newly created clinic in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'admin_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            // Allow password to be nullable if we want to auto-generate, but user asked for "create username and password"
            'password' => 'required|string|min:8',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        // Create Clinic
        $clinic = Clinic::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'settings' => [],
        ]);

        // Create Admin User
        User::create([
            'name' => $request->admin_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_CLINIC_ADMIN,
            'clinic_id' => $clinic->id,
            'is_active' => true,
        ]);

        return redirect()->route('super-admin.clinics.index')->with('success', 'Clinic and Admin User created successfully.');
    }

    /**
     * Update a clinic's plan (tag/detag).
     */
    public function updatePlan(Request $request, Clinic $clinic)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'plan_id' => 'nullable|exists:plans,id',
        ]);

        $planId = $request->plan_id;
        $plan = $planId ? Plan::find($planId) : null;

        $clinic->update([
            'plan_id' => $planId,
            'subscription_status' => $planId ? 'active' : 'inactive',
        ]);

        $planName = $plan ? $plan->name : 'No Plan';

        return redirect()->back()->with('success', "Clinic \"{$clinic->name}\" has been assigned to the \"{$planName}\" plan.");
    }
    /**
     * Toggle clinic activation status.
     */
    public function toggleStatus(Clinic $clinic)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $clinic->update([
            'is_active' => !$clinic->is_active
        ]);

        $status = $clinic->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Clinic \"{$clinic->name}\" has been {$status}.");
    }
}
