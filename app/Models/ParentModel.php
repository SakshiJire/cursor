<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'father_name',
        'father_phone',
        'father_email',
        'father_occupation',
        'mother_name',
        'mother_phone',
        'mother_email',
        'mother_occupation',
        'address',
        'city',
        'state',
        'pincode',
        'guardian_name',
        'guardian_phone',
        'guardian_relationship',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'parent_student')
                    ->withPivot('relationship', 'is_primary')
                    ->withTimestamps();
    }

    // Helper methods
    public function getPrimaryContact()
    {
        return $this->father_phone ?: $this->mother_phone ?: $this->guardian_phone;
    }

    public function getPrimaryEmail()
    {
        return $this->father_email ?: $this->mother_email ?: $this->user->email;
    }
}