<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PerformanceTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $clinicsCount = 10; // Reduced from 50 for dev speed, can be increased
        $patientsPerClinic = 500;
        $appointmentsPerClinic = 2000;

        $this->command->info("Seeding $clinicsCount clinics with $patientsPerClinic patients and $appointmentsPerClinic appointments each...");

        for ($i = 0; $i < $clinicsCount; $i++) {
            DB::transaction(function () use ($faker, $patientsPerClinic, $appointmentsPerClinic) {
                // Create Clinic
                $clinic = Clinic::create([
                    'name' => $faker->company . ' Clinic',
                    'domain' => $faker->slug,
                    'is_active' => true,
                    'plan_id' => 1,
                ]);

                // Create Doctors (5 per clinic)
                $doctorIds = [];
                for ($d = 0; $d < 5; $d++) {
                    $user = User::create([
                        'name' => $faker->name,
                        'email' => $faker->unique()->safeEmail,
                        'password' => bcrypt('password'),
                        'role' => 'doctor',
                        'clinic_id' => $clinic->id,
                    ]);

                    $doctor = Doctor::create([
                        'user_id' => $user->id,
                        'clinic_id' => $clinic->id,
                        'specialization' => $faker->jobTitle,
                        'license_number' => $faker->bothify('LIC-####'),
                        'phone' => $faker->phoneNumber,
                    ]);
                    $doctorIds[] = $doctor->id;
                }

                // Create Patients (Batch Insert)
                $patientsData = [];
                for ($p = 0; $p < $patientsPerClinic; $p++) {
                    $patientsData[] = [
                        'clinic_id' => $clinic->id,
                        'name' => $faker->name,
                        'phone' => $faker->phoneNumber,
                        'email' => $faker->safeEmail,
                        'gender' => $faker->randomElement(['male', 'female']),
                        'date_of_birth' => $faker->date(),
                        'address' => $faker->address,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Chunk insert patients
                foreach (array_chunk($patientsData, 100) as $chunk) {
                    Patient::insert($chunk);
                }

                // Get Patient IDs
                $patientIds = Patient::where('clinic_id', $clinic->id)->pluck('id')->toArray();

                // Create Appointments (Batch Insert optimization needed but relationships complex)
                // We'll use insert for speed, simpler data
                $appointmentsData = [];
                for ($a = 0; $a < $appointmentsPerClinic; $a++) {
                    $appointmentsData[] = [
                        'clinic_id' => $clinic->id,
                        'patient_id' => $faker->randomElement($patientIds),
                        'doctor_id' => $faker->randomElement($doctorIds),
                        'appointment_date' => $faker->dateTimeBetween('-1 year', '+1 year')->format('Y-m-d'),
                        'appointment_time' => $faker->time('H:i:s'),
                        'status' => $faker->randomElement(['booked', 'confirmed', 'completed', 'cancelled']),
                        'type' => 'checkup',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                foreach (array_chunk($appointmentsData, 100) as $chunk) {
                    Appointment::insert($chunk);
                }
            });

            $this->command->info("Clinic created.");
        }
    }
}
