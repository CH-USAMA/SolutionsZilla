<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToClinic;

use App\Traits\LogsActivity;

class Patient extends Model
{
    use HasFactory, SoftDeletes, BelongsToClinic, LogsActivity;

    protected $fillable = [
        'clinic_id',
        'name',
        'phone',
        'email',
        'gender',
        'date_of_birth',
        'address',
        'medical_history',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // Relationship inherited from BelongsToClinic trait

    /**
     * Get all appointments for this patient
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Scope a query to only include patients for the current clinic
     */
    public function scopeForClinic($query, $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    /**
     * Get patient's documents
     */
    public function documents()
    {
        return $this->hasMany(PatientDocument::class);
    }

    /**
     * Get patient's age
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }
}
