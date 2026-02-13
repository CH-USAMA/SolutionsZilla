<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $clinic;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Plan
        $plan = Plan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'price' => 0,
            'max_appointments' => 0, // Unlimited
            'features' => [],
            'is_active' => true,
        ]);

        // Setup Clinic
        $this->clinic = Clinic::create([
            'name' => 'Test Clinic',
            'phone' => '1234567890',
            'is_active' => true,
            'plan_id' => $plan->id,
            'subscription_status' => 'active',
        ]);

        // Setup User
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_CLINIC_ADMIN,
            'clinic_id' => $this->clinic->id,
        ]);
    }

    public function test_can_create_doctor()
    {
        $response = $this->actingAs($this->user)
            ->post(route('doctors.store'), [
                'name' => 'Dr. Test',
                'specialization' => 'General',
                'phone' => '1234567890',
                'email' => 'drtest@example.com',
                'consultation_fee' => 1000,
                'is_available' => 1,
                'password' => 'password',
            ]);

        $response->assertRedirect(route('doctors.index'));
        $this->assertDatabaseHas('doctors', ['email' => 'drtest@example.com']);
    }

    public function test_can_update_doctor()
    {
        $doctor = Doctor::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Dr. Old',
            'specialization' => 'Old Spec',
            'phone' => '1111111111',
            'email' => 'old@example.com',
            'consultation_fee' => 500,
            'is_available' => true,
            'user_id' => User::create(['name' => 'Dr', 'email' => 'old@example.com', 'password' => 'p', 'clinic_id' => $this->clinic->id])->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('doctors.update', $doctor), [
                'name' => 'Dr. New',
                'specialization' => 'New Spec',
                'phone' => '2222222222',
                'email' => 'old@example.com', // Keeping email same
                'consultation_fee' => 1500,
                'is_available' => 1,
            ]);

        $response->assertRedirect(route('doctors.index'));
        $this->assertDatabaseHas('doctors', ['name' => 'Dr. New']);
    }

    public function test_can_create_patient()
    {
        $response = $this->actingAs($this->user)
            ->post(route('patients.store'), [
                'name' => 'Test Patient',
                'phone' => '03001234567',
                'gender' => 'male',
                'date_of_birth' => '1990-01-01',
                'email' => 'patient@test.com',
                'address' => 'Test Address',
            ]);

        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseHas('patients', ['phone' => '03001234567']);
    }

    public function test_can_update_patient()
    {
        $patient = Patient::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Old Patient',
            'phone' => '03000000000',
            'gender' => 'male',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('patients.update', $patient), [
                'name' => 'New Patient',
                'phone' => '03000000000',
                'gender' => 'female',
            ]);

        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseHas('patients', ['name' => 'New Patient', 'gender' => 'female']);
    }

    public function test_can_book_appointment()
    {
        $doctor = Doctor::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Dr. Appointment',
            'consultation_fee' => 1000,
            'is_available' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('appointments.store'), [
                'doctor_id' => $doctor->id,
                'patient_name' => 'New Patient for Appt',
                'patient_phone' => '03009998887',
                'appointment_date' => now()->addDay()->format('Y-m-d'),
                'appointment_time' => '10:00',
                'notes' => 'Test Notes',
            ]);

        $response->assertRedirect();
        // Check session for success
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('appointments', [
            'clinic_id' => $this->clinic->id,
            'doctor_id' => $doctor->id,
        ]);

        // Also verify patient was created
        $this->assertDatabaseHas('patients', ['phone' => '03009998887']);
    }

    public function test_can_create_staff()
    {
        $response = $this->actingAs($this->user)
            ->post(route('staff.store'), [
                'name' => 'Receptionist User',
                'email' => 'staff@test.com',
                'phone' => '03001112223',
                'password' => 'password',
                'password_confirmation' => 'password',
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('staff.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'staff@test.com',
            'role' => User::ROLE_RECEPTIONIST,
            'clinic_id' => $this->clinic->id,
        ]);
    }
}
