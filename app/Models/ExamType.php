<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'weightage', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}