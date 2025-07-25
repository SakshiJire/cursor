<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admission_number',
        'roll_number',
        'class_id',
        'academic_year_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'address',
        'city',
        'state',
        'pincode',
        'photo',
        'medical_info',
        'emergency_contacts',
        'admission_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'medical_info' => 'array',
        'emergency_contacts' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function parents()
    {
        return $this->belongsToMany(ParentModel::class, 'parent_student')
                    ->withPivot('relationship', 'is_primary')
                    ->withTimestamps();
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function activityParticipations()
    {
        return $this->hasMany(ActivityParticipant::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByAcademicYear($query, $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    // Helper methods
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth->age;
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('images/default-student.png');
    }

    public function getPrimaryParent()
    {
        return $this->parents()->wherePivot('is_primary', true)->first();
    }

    public function getTotalFeesAttribute()
    {
        return $this->fees()->sum('amount');
    }

    public function getPaidFeesAttribute()
    {
        return $this->fees()->sum('paid_amount');
    }

    public function getPendingFeesAttribute()
    {
        return $this->fees()->sum('pending_amount');
    }
}