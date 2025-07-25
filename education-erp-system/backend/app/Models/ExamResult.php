<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'student_id', 'subject_id', 'marks_obtained', 'max_marks', 'grade', 'status', 'remarks'
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'max_marks' => 'decimal:2'
    ];

    public function exam() { return $this->belongsTo(Exam::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
}