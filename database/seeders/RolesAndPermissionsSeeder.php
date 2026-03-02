<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Clinic Admin Management (Mainly for Super Admin in the future)
            'manage_clinics',
            'view_all_clinics',

            // Patient Management
            'view_patients',
            'create_patients',
            'edit_patients',
            'delete_patients',

            // Appointment Management
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'delete_appointments',
            'confirm_appointments',

            // Staff/Receptionist Management
            'view_staff',
            'create_staff',
            'edit_staff',
            'delete_staff',

            // WhatsApp & Communication
            'manage_whatsapp',
            'view_whatsapp_logs',
            'send_test_messages',

            // Reports & Analytics
            'view_reports',
            'export_reports',

            // Billing & Plans
            'view_billing',
            'manage_subscriptions',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create Roles and Assign Permissions

        // Super Admin (has all permissions)
        $superAdminRole = Role::findOrCreate(User::ROLE_SUPER_ADMIN);
        $superAdminRole->givePermissionTo(Permission::all());

        // Clinic Admin
        $clinicAdminRole = Role::findOrCreate(User::ROLE_CLINIC_ADMIN);
        $clinicAdminRole->givePermissionTo([
            'view_patients',
            'create_patients',
            'edit_patients',
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'confirm_appointments',
            'view_staff',
            'create_staff',
            'edit_staff',
            'delete_staff',
            'manage_whatsapp',
            'view_whatsapp_logs',
            'send_test_messages',
            'view_reports',
            'export_reports',
            'view_billing',
            'manage_subscriptions'
        ]);

        // Receptionist
        $receptionistRole = Role::findOrCreate(User::ROLE_RECEPTIONIST);
        $receptionistRole->givePermissionTo([
            'view_patients',
            'create_patients',
            'edit_patients',
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'confirm_appointments',
            'view_whatsapp_logs'
        ]);

        // Doctor
        $doctorRole = Role::findOrCreate(User::ROLE_DOCTOR);
        $doctorRole->givePermissionTo([
            'view_patients',
            'view_appointments',
            'view_reports'
        ]);

        // Assign existing users to roles based on their 'role' column
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role && !$user->hasAnyRole(Role::all())) {
                $user->assignRole($user->role);
            }
        }
    }
}
