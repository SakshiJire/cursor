<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'code',
        'description',
        'capacity',
        'monthly_fee',
        'admission_fee',
        'subjects',
        'is_active',
    ];

    protected $casts = [
        'subjects' => 'array',
        'is_active' => 'boolean',
        'monthly_fee' => 'decimal:2',
        'admission_fee' => 'decimal:2',
    ];

    // Relationships
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects')
                    ->withPivot('periods_per_week')
                    ->withTimestamps();
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'class_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'class_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'class_id');
    }

    public function learningMaterials()
    {
        return $this->hasMany(LearningMaterial::class, 'class_id');
    }

    public function chatGroups()
    {
        return $this->hasMany(ChatGroup::class, 'class_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function getCurrentStudentsCount()
    {
        return $this->students()->active()->count();
    }

    public function getAvailableSeats()
    {
        return $this->capacity - $this->getCurrentStudentsCount();
    }

    public function isFull()
    {
        return $this->getCurrentStudentsCount() >= $this->capacity;
    }
}