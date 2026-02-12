<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Clinic;
use App\Models\Plan;

class BillingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'amount',
        'payment_gateway',
        'status',
        'transaction_id',
        'plan_id',
        'invoice_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
