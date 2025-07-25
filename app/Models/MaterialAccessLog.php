<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialAccessLog extends Model
{
    use HasFactory;

    protected $fillable = ['material_id', 'user_id', 'action', 'accessed_at', 'ip_address'];
    protected $casts = ['accessed_at' => 'datetime'];

    public function material()
    {
        return $this->belongsTo(LearningMaterial::class, 'material_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}