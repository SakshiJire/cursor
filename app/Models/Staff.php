<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'emergency_contact',
        'address',
        'city',
        'state',
        'pincode',
        'designation',
        'qualification',
        'experience',
        'joining_date',
        'salary',
        'photo',
        'documents',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2',
        'documents' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}