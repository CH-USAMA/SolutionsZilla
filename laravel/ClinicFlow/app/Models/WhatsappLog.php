<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class WhatsappLog extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'appointment_id',
        'direction',
        'phone',
        'template_name',
        'payload',
        'response',
        'status',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
    ];

    // Relationship inherited from BelongsToClinic trait

    /**
     * Get the appointment associated with the log
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
