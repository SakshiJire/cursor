<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'color', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subjects')
                    ->withPivot('periods_per_week')
                    ->withTimestamps();
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function learningMaterials()
    {
        return $this->hasMany(LearningMaterial::class);
    }
}