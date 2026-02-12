<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_clinic_a_cannot_see_clinic_b_patients()
    {
        // 1. Setup Clinic A and B
        $clinicA = Clinic::factory()->create(['name' => 'Clinic A']);
        $userA = User::factory()->create(['clinic_id' => $clinicA->id]);
        $patientA = Patient::factory()->create(['clinic_id' => $clinicA->id, 'name' => 'Patient A']);

        $clinicB = Clinic::factory()->create(['name' => 'Clinic B']);
        $patientB = Patient::factory()->create(['clinic_id' => $clinicB->id, 'name' => 'Patient B']);

        // 2. Act as User A
        $this->actingAs($userA);

        // 3. Assert Patient B is not visible in index
        $response = $this->get(route('patients.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Patient B');

        // 4. Assert direct access to Patient B returns 403
        $response = $this->get(route('patients.show', $patientB->id));
        $response->assertStatus(403);
    }

    public function test_clinic_a_cannot_update_clinic_b_appointments()
    {
        $clinicA = Clinic::factory()->create();
        $userA = User::factory()->create(['clinic_id' => $clinicA->id]);

        $clinicB = Clinic::factory()->create();
        $patientB = Patient::factory()->create(['clinic_id' => $clinicB->id]);
        $appointmentB = Appointment::factory()->create([
            'clinic_id' => $clinicB->id,
            'patient_id' => $patientB->id,
            'status' => 'booked'
        ]);

        $this->actingAs($userA);

        // Try to update Patient B's appointment status
        $response = $this->patch(route('appointments.update-status', $appointmentB->id), [
            'status' => 'confirmed'
        ]);

        $response->assertStatus(403);
        $this->assertEquals('booked', $appointmentB->fresh()->status);
    }
}
