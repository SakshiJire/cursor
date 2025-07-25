<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningMaterialCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'icon', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function materials()
    {
        return $this->hasMany(LearningMaterial::class, 'category_id');
    }
}