<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display all plans for Super Admin management.
     */
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $plans = Plan::withCount('clinics')->get();

        return view('super-admin.plans', compact('plans'));
    }

    /**
     * Toggle the is_active status of a plan.
     */
    public function toggle(Plan $plan)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $plan->update(['is_active' => !$plan->is_active]);

        return redirect()->back()->with('success', "Plan \"{$plan->name}\" has been " . ($plan->is_active ? 'enabled' : 'disabled') . '.');
    }
}
