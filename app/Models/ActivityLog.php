<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Clinic;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'user_id',
        'action',
        'loggable_type',
        'loggable_id',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function loggable()
    {
        return $this->morphTo();
    }
}
