<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'name',
        'specialization',
        'phone',
        'email',
        'qualifications',
        'consultation_fee',
        'is_available',
        'availability_schedule',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'consultation_fee' => 'decimal:2',
        'availability_schedule' => 'array',
    ];

    /**
     * Get the clinic that owns the doctor
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get all appointments for this doctor
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Scope a query to only include available doctors
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to only include doctors for the current clinic
     */
    public function scopeForClinic($query, $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }
}
