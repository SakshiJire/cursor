<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name',
        'created_by',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'chat_group_members', 'group_id', 'user_id');
    }

    public function groupMembers()
    {
        return $this->hasMany(ChatGroupMember::class, 'group_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'group_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'group_id')->latest();
    }

    // Check if user is member of this group
    public function hasMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    // Add member to group
    public function addMember($userId)
    {
        if (!$this->hasMember($userId)) {
            ChatGroupMember::create([
                'group_id' => $this->id,
                'user_id' => $userId,
            ]);
        }
    }

    // Remove member from group
    public function removeMember($userId)
    {
        ChatGroupMember::where('group_id', $this->id)
            ->where('user_id', $userId)
            ->delete();
    }
}
