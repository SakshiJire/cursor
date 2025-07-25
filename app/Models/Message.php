<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id', 'receiver_id', 'group_id', 'content', 'type', 'file_path',
        'file_name', 'file_size', 'is_read', 'read_at', 'is_deleted', 'replied_to'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    public function repliedToMessage()
    {
        return $this->belongsTo(Message::class, 'replied_to');
    }

    public function reactions()
    {
        return $this->hasMany(MessageReaction::class);
    }
}