<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToClinic;

class WhatsAppMessage extends Model
{
    use HasFactory, SoftDeletes, BelongsToClinic;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'clinic_id',
        'message_id',
        'wamid',
        'from',
        'to',
        'type',
        'direction',
        'body',
        'status',
        'metadata',
        'conversation_id',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function conversation()
    {
        return $this->belongsTo(WhatsAppConversation::class, 'conversation_id', 'conversation_id');
    }
}
