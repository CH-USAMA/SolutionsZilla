<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::withCount('permissions')->get();
        return view('super-admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy(function ($item) {
            // Basic grouping logic for UI
            if (str_contains($item->name, 'patient'))
                return 'Patients';
            if (str_contains($item->name, 'appointment'))
                return 'Appointments';
            if (str_contains($item->name, 'staff'))
                return 'Staff';
            if (str_contains($item->name, 'clinic'))
                return 'Clinics';
            if (str_contains($item->name, 'whatsapp'))
                return 'Communications';
            if (str_contains($item->name, 'report'))
                return 'Reports';
            if (str_contains($item->name, 'billing'))
                return 'Billing';
            return 'Others';
        });

        return view('super-admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create(['name' => $request->name]);
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
        });

        return redirect()->route('super-admin.roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function ($item) {
            if (str_contains($item->name, 'patient'))
                return 'Patients';
            if (str_contains($item->name, 'appointment'))
                return 'Appointments';
            if (str_contains($item->name, 'staff'))
                return 'Staff';
            if (str_contains($item->name, 'clinic'))
                return 'Clinics';
            if (str_contains($item->name, 'whatsapp'))
                return 'Communications';
            if (str_contains($item->name, 'report'))
                return 'Reports';
            if (str_contains($item->name, 'billing'))
                return 'Billing';
            return 'Others';
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('super-admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions ?? []);
        });

        return redirect()->route('super-admin.roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (in_array($role->name, ['super_admin', 'clinic_admin'])) {
            return back()->with('error', 'Core roles cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('super-admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
