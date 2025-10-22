<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'group_id',
        'message',
        'message_type',
        'is_seen',
    ];

    protected $casts = [
        'is_seen' => 'boolean',
    ];

    // Relationships
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

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'message_id');
    }

    // Scopes
    public function scopePrivateMessages($query, $userId1, $userId2)
    {
        return $query->where(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId1)->where('receiver_id', $userId2);
        })->orWhere(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId2)->where('receiver_id', $userId1);
        })->whereNull('group_id');
    }

    public function scopeGroupMessages($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    public function scopeUnseenFor($query, $userId)
    {
        return $query->where('receiver_id', $userId)->where('is_seen', false);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where('message', 'like', '%' . $keyword . '%');
    }

    // Mark message as seen
    public function markAsSeen()
    {
        $this->update(['is_seen' => true]);
    }

    // Check if message is private
    public function isPrivate()
    {
        return $this->group_id === null;
    }

    // Check if message is group message
    public function isGroupMessage()
    {
        return $this->group_id !== null;
    }
}
