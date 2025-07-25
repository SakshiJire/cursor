<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'class_id', 'academic_year_id', 'activity_date',
        'start_time', 'end_time', 'venue', 'staff_id', 'type', 'requirements', 'status'
    ];

    protected $casts = [
        'activity_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function participants()
    {
        return $this->hasMany(ActivityParticipant::class);
    }
}