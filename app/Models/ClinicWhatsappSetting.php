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
        'waba_id',
        'display_phone_number',
        'access_token',
        'verify_token',
        'default_template',
        'message_type',
        'custom_message',
        'reminder_hours_before',
        'is_active',
        'provider',
        'js_api_url',
        'js_api_key',
        'js_session_id',
        'js_connection_status',
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
