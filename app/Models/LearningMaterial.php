<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'category_id', 'class_id', 'subject_id', 'created_by',
        'type', 'file_path', 'external_url', 'file_size', 'mime_type', 'tags',
        'downloads', 'views', 'is_public', 'is_active'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(LearningMaterialCategory::class, 'category_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function accessLogs()
    {
        return $this->hasMany(MaterialAccessLog::class, 'material_id');
    }
}