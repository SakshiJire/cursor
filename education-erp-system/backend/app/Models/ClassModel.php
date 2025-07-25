<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'institute_id',
        'name',
        'section',
        'capacity',
        'class_teacher_id',
        'annual_fee',
        'description',
        'status'
    ];

    protected $casts = [
        'annual_fee' => 'decimal:2'
    ];

    // Relationships
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function classTeacher()
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class, 'class_id');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'class_id');
    }

    public function learningMaterials()
    {
        return $this->hasMany(LearningMaterial::class, 'class_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->name . ($this->section ? ' - ' . $this->section : '');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByInstitute($query, $instituteId)
    {
        return $query->where('institute_id', $instituteId);
    }
}