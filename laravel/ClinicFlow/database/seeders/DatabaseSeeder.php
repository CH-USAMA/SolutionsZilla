<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        // 1. Create Demo Clinic
        $clinic = Clinic::create([
            'name' => 'Shifa International Clinic',
            'phone' => '051-1234567',
            'address' => 'Blue Area, Islamabad',
            'opening_time' => '09:00:00',
            'closing_time' => '20:00:00',
            'is_active' => true,
        ]);

        // 1.1 Create WhatsApp Settings for Clinic
        \App\Models\ClinicWhatsappSetting::create([
            'clinic_id' => $clinic->id,
            'phone_number_id' => '1234567890', // Demo ID
            'access_token' => 'demo_access_token', // Will be encrypted
            'default_template' => 'appointment_reminder',
            'is_active' => true,
        ]);

        // 2. Create Clinic Admin
        User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Admin',
            'email' => 'admin@shifa.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CLINIC_ADMIN,
            'phone' => '03001234567',
        ]);

        // 3. Create Receptionist
        User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Reception Desk',
            'email' => 'reception@shifa.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_RECEPTIONIST,
            'phone' => '03007654321',
        ]);

        // 4. Create Doctors
        // Doctor 1
        $doc1User = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Sarah Ahmed',
            'email' => 'sarah@shifa.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_DOCTOR,
            'phone' => '03211234567',
        ]);

        $doc1 = Doctor::create([
            'user_id' => $doc1User->id,
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Sarah Ahmed',
            'specialization' => 'Cardiologist',
            'phone' => '03211234567',
            'email' => 'sarah@shifa.com',
            'consultation_fee' => 2500,
            'is_available' => true,
        ]);

        // Doctor 2
        $doc2User = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ali Khan',
            'email' => 'ali@shifa.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_DOCTOR,
            'phone' => '03331234567',
        ]);

        $doc2 = Doctor::create([
            'user_id' => $doc2User->id,
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ali Khan',
            'specialization' => 'General Physician',
            'phone' => '03331234567',
            'email' => 'ali@shifa.com',
            'consultation_fee' => 1500,
            'is_available' => true,
        ]);

        // 5. Create Patients
        $patients = [];
        for ($i = 1; $i <= 10; $i++) {
            $patients[] = Patient::create([
                'clinic_id' => $clinic->id,
                'name' => 'Patient ' . $i,
                'phone' => '0300' . rand(1000000, 9999999),
                'email' => 'patient' . $i . '@example.com',
                'gender' => $i % 2 == 0 ? 'female' : 'male',
                'date_of_birth' => Carbon::now()->subYears(rand(20, 60)),
            ]);
        }

        // 6. Create Appointments (Today)
        foreach (range(9, 17) as $hour) {
            // Skip lunch
            if ($hour == 13)
                continue;

            Appointment::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patients[array_rand($patients)]->id,
                'doctor_id' => ($hour % 2 == 0) ? $doc1->id : $doc2->id,
                'appointment_date' => Carbon::today(),
                'appointment_time' => sprintf('%02d:00:00', $hour),
                'status' => $hour < 12 ? 'completed' : 'booked',
                'notes' => 'Regular checkup',
            ]);
        }

        // 7. Create Future Appointments
        Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patients[0]->id,
            'doctor_id' => $doc1->id,
            'appointment_date' => Carbon::tomorrow(),
            'appointment_time' => '10:00:00',
            'status' => 'booked',
        ]);
    }
}
