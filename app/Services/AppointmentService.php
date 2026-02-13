<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Clinic;
use App\Exceptions\PlanLimitReachedException;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Book a new appointment with business logic checks.
     */
    public function bookAppointment(array $data, Clinic $clinic)
    {
        // 1. Subscription Check
        if (!$this->subscriptionService->canCreateAppointment($clinic)) {
            throw new \Exception("Upgrade your plan to book more appointments.");
        }

        return DB::transaction(function () use ($data, $clinic) {
            // 2. Find or Create Patient
            $patient = Patient::firstOrCreate(
                ['phone' => $data['phone'], 'clinic_id' => $clinic->id],
                [
                    'name' => $data['name'],
                    'email' => $data['email'] ?? null,
                    'gender' => $data['gender'] ?? null,
                ]
            );

            // 3. Double-booking Check
            $exists = Appointment::where('doctor_id', $data['doctor_id'])
                ->where('appointment_date', $data['appointment_date'])
                ->where('appointment_time', $data['appointment_time'])
                ->whereIn('status', ['booked', 'confirmed'])
                ->exists();

            if ($exists) {
                throw new \Exception("This time slot is already booked for this doctor.");
            }

            // 4. Create Appointment
            return Appointment::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'doctor_id' => $data['doctor_id'],
                'appointment_date' => $data['appointment_date'],
                'appointment_time' => $data['appointment_time'],
                'notes' => $data['reason'] ?? null,
                'status' => 'booked',
            ]);
        });
    }

    /**
     * Update appointment status.
     */
    public function updateStatus(Appointment $appointment, string $status)
    {
        return $appointment->update(['status' => $status]);
    }
}
