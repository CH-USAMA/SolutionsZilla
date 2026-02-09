<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'type',
        'amount',
        'billing_date',
        'due_date',
        'paid_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    /**
     * Get the clinic that owns the billing record
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Scope a query to only include records for the current clinic
     */
    public function scopeForClinic($query, $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    /**
     * Scope a query to filter by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if payment is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'unpaid' && $this->due_date->isPast();
    }
}
