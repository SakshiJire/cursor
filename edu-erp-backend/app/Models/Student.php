<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'institution_id',
        'academic_year_id',
        'class_id',
        'admission_number',
        'admission_date',
        'roll_number',
        'parent_id',
        'father_name',
        'mother_name',
        'guardian_name',
        'guardian_relation',
        'blood_group',
        'medical_conditions',
        'previous_school',
        'transport_required',
        'hostel_required',
        'documents',
        'remarks',
        'status',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'documents' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id', 'user_id');
    }

    public function transportAssignment()
    {
        return $this->hasOne(TransportAssignment::class);
    }

    public function hostelAssignment()
    {
        return $this->hasOne(HostelAssignment::class);
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

    public function scopeByInstitution($query, $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }
}
