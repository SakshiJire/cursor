<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'type', 'created_by', 'class_id', 'avatar', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function members()
    {
        return $this->hasMany(ChatGroupMember::class, 'group_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'group_id');
    }
}