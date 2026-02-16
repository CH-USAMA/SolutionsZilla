<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class WhatsAppUsage extends Model
{
    use HasFactory, BelongsToClinic;

    protected $table = 'whatsapp_usage';

    protected $fillable = [
        'clinic_id',
        'month',
        'year',
        'conversations_count',
        'messages_sent',
        'messages_delivered',
        'estimated_cost',
        'currency',
        'breakdown',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'breakdown' => 'array',
    ];
}
