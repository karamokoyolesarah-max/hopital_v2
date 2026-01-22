<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'content',
        'attachment_path',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return match($this->sender_type) {
            'patient' => $this->belongsTo(Patient::class, 'sender_id'),
            'doctor' => $this->belongsTo(User::class, 'sender_id'),
            'external_doctor' => $this->belongsTo(ExternalDoctor::class, 'sender_id'),
            default => null
        };
    }
}