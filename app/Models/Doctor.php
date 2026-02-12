<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToClinic;

class Doctor extends Model
{
    use HasFactory, SoftDeletes, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'user_id',
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

    // Relationship inherited from BelongsToClinic trait

    /**
     * Get the user account associated with the doctor
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
