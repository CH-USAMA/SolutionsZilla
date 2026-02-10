<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'status',
        'notes',
        'cancellation_reason',
        'whatsapp_reminder_sent',
        'sms_reminder_sent',
        'whatsapp_reminder_sent_at',
        'sms_reminder_sent_at',
        'confirmed_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'whatsapp_reminder_sent' => 'boolean',
        'sms_reminder_sent' => 'boolean',
        'whatsapp_reminder_sent_at' => 'datetime',
        'sms_reminder_sent_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    /**
     * Get the clinic that owns the appointment
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the patient for this appointment
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor for this appointment
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Scope a query to only include appointments for the current clinic
     */
    public function scopeForClinic($query, $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    /**
     * Scope a query to only include today's appointments
     */
    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', Carbon::today());
    }

    /**
     * Scope a query to filter by date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('appointment_date', $date);
    }

    /**
     * Scope a query to filter by doctor
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope a query to filter by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get appointments needing WhatsApp reminder (24 hours before)
     */
    public function scopeNeedingWhatsAppReminder($query)
    {
        $targetDate = Carbon::now()->addHours(24);

        return $query->where('whatsapp_reminder_sent', false)
            ->where('status', 'booked')
            ->whereDate('appointment_date', $targetDate->toDateString())
            ->whereTime('appointment_time', '>=', $targetDate->subMinutes(30)->toTimeString())
            ->whereTime('appointment_time', '<=', $targetDate->addMinutes(60)->toTimeString());
    }

    /**
     * Scope to get appointments needing SMS reminder (2 hours before)
     */
    public function scopeNeedingSmsReminder($query)
    {
        $targetDate = Carbon::now()->addHours(2);

        return $query->where('sms_reminder_sent', false)
            ->where('status', 'booked')
            ->whereDate('appointment_date', $targetDate->toDateString())
            ->whereTime('appointment_time', '>=', $targetDate->subMinutes(15)->toTimeString())
            ->whereTime('appointment_time', '<=', $targetDate->addMinutes(30)->toTimeString());
    }

    /**
     * Get full appointment datetime
     */
    public function getAppointmentDateTimeAttribute()
    {
        return Carbon::parse($this->appointment_date->format('Y-m-d') . ' ' . $this->appointment_time);
    }

    /**
     * Check if appointment is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->appointment_date_time->isFuture() && in_array($this->status, ['booked', 'confirmed']);
    }

    /**
     * Check if appointment is past
     */
    public function isPast(): bool
    {
        return $this->appointment_date_time->isPast();
    }
}
