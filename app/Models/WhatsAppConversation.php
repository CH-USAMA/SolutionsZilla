<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class WhatsAppConversation extends Model
{
    use HasFactory, BelongsToClinic;

    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'clinic_id',
        'conversation_id',
        'phone_number',
        'started_at',
        'expires_at',
        'last_message_at',
        'type',
        'category',
        'message_count',
        'is_billable',
        'cost',
        'currency',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_message_at' => 'datetime',
        'is_billable' => 'boolean',
        'cost' => 'decimal:4',
    ];

    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id', 'conversation_id');
    }
}
