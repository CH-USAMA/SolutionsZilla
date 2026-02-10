<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicWhatsappSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'phone_number_id',
        'access_token',
        'default_template',
        'reminder_hours_before',
        'is_active',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'reminder_hours_before' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the clinic that owns the WhatsApp settings
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
