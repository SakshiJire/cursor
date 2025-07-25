<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'institute_id',
        'class_id',
        'admission_number',
        'admission_date',
        'roll_number',
        'father_name',
        'mother_name',
        'guardian_name',
        'father_phone',
        'mother_phone',
        'guardian_phone',
        'father_occupation',
        'mother_occupation',
        'blood_group',
        'medical_history',
        'previous_school',
        'previous_percentage',
        'documents',
        'special_notes',
        'transport_required',
        'hostel_required',
        'status'
    ];

    protected $casts = [
        'admission_date' => 'date',
        'previous_percentage' => 'decimal:2',
        'documents' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function transport()
    {
        return $this->hasOne(StudentTransport::class);
    }

    public function hostel()
    {
        return $this->hasOne(StudentHostel::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id', 'user_id');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->user->full_name;
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

    public function scopeByInstitute($query, $instituteId)
    {
        return $query->where('institute_id', $instituteId);
    }

    public function scopeRequiresTransport($query)
    {
        return $query->where('transport_required', 'yes');
    }

    public function scopeRequiresHostel($query)
    {
        return $query->where('hostel_required', 'yes');
    }
}