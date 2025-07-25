<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'receipt_number',
        'amount_due',
        'amount_paid',
        'late_fee',
        'discount',
        'payment_method',
        'transaction_id',
        'payment_date',
        'due_date',
        'status',
        'remarks',
        'collected_by'
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'payment_date' => 'date',
        'due_date' => 'date'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    // Accessors
    public function getTotalAmountAttribute()
    {
        return $this->amount_paid + $this->late_fee - $this->discount;
    }

    public function getBalanceAmountAttribute()
    {
        return $this->amount_due - $this->amount_paid;
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }
}