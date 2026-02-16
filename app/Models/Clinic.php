<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Cashier\Billable;

use App\Traits\LogsActivity;

class Clinic extends Model
{
    use HasFactory, SoftDeletes, Billable, LogsActivity;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'logo',
        'opening_time',
        'closing_time',
        'setup_fee',
        'monthly_fee',
        'billing_status',
        'next_billing_date',
        'plan_id',
        'subscription_status',
        'trial_ends_at',
        'subscription_ends_at',
        'is_active',
    ];

    protected $casts = [
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
        'next_billing_date' => 'date',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_active' => 'boolean',
        'setup_fee' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
    ];

    /**
     * Get all users belonging to this clinic
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all doctors belonging to this clinic
     */
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    /**
     * Get all patients belonging to this clinic
     */
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Get all appointments for this clinic
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get all billing records for this clinic
     */
    public function billingRecords()
    {
        return $this->hasMany(BillingRecord::class);
    }

    /**
     * Get the WhatsApp settings for this clinic
     */
    /**
     * Get all SMS logs for this clinic
     */
    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class);
    }

    /**
     * Get the plan for this clinic.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get all WhatsApp messages for this clinic
     */
    public function whatsappMessages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    /**
     * Get all WhatsApp conversations for this clinic
     */
    public function whatsappConversations()
    {
        return $this->hasMany(WhatsAppConversation::class);
    }

    /**
     * Get all WhatsApp usage records for this clinic
     */
    public function whatsappUsage()
    {
        return $this->hasMany(WhatsAppUsage::class);
    }
}
