<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\FeePayment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Fee::with(['student.user', 'student.class', 'feeType', 'academicYear', 'payments']);

        // Filters
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('class_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('fee_type_id')) {
            $query->where('fee_type_id', $request->fee_type_id);
        }

        if ($request->has('month')) {
            $query->where('month', $request->month);
        }

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Check for overdue fees
        if ($request->has('overdue') && $request->overdue) {
            $query->where('due_date', '<', now())
                  ->where('status', '!=', 'paid');
        }

        $perPage = $request->get('per_page', 15);
        $fees = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $fees
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_type_id' => 'required|exists:fee_types,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'month' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'remarks' => 'nullable|string'
        ]);

        $fee = Fee::create([
            'student_id' => $request->student_id,
            'fee_type_id' => $request->fee_type_id,
            'academic_year_id' => $request->academic_year_id,
            'month' => $request->month,
            'amount' => $request->amount,
            'paid_amount' => 0,
            'pending_amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'remarks' => $request->remarks
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fee created successfully',
            'data' => $fee->load(['student.user', 'feeType', 'academicYear'])
        ], 201);
    }

    public function show($id)
    {
        $fee = Fee::with([
            'student.user', 'student.class', 'feeType', 
            'academicYear', 'payments'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $fee
        ]);
    }

    public function update(Request $request, $id)
    {
        $fee = Fee::findOrFail($id);

        $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'due_date' => 'sometimes|date',
            'status' => 'sometimes|in:pending,partial,paid,overdue',
            'remarks' => 'nullable|string'
        ]);

        $updateData = $request->only(['amount', 'due_date', 'status', 'remarks']);

        // Recalculate pending amount if amount is updated
        if ($request->has('amount')) {
            $updateData['pending_amount'] = $request->amount - $fee->paid_amount;
        }

        $fee->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Fee updated successfully',
            'data' => $fee->fresh()->load(['student.user', 'feeType', 'academicYear'])
        ]);
    }

    public function makePayment(Request $request, $id)
    {
        $fee = Fee::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $fee->pending_amount,
            'payment_method' => 'required|in:cash,online,card,cheque,bank_transfer',
            'transaction_id' => 'nullable|string',
            'remarks' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Generate receipt number
            $receiptNumber = $this->generateReceiptNumber();

            // Create payment record
            $payment = FeePayment::create([
                'fee_id' => $fee->id,
                'amount' => $request->amount,
                'payment_date' => now(),
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'receipt_number' => $receiptNumber,
                'remarks' => $request->remarks
            ]);

            // Update fee record
            $newPaidAmount = $fee->paid_amount + $request->amount;
            $newPendingAmount = $fee->amount - $newPaidAmount;
            
            $status = $newPendingAmount <= 0 ? 'paid' : 
                     ($newPaidAmount > 0 ? 'partial' : 'pending');

            $fee->update([
                'paid_amount' => $newPaidAmount,
                'pending_amount' => $newPendingAmount,
                'status' => $status,
                'paid_date' => $status === 'paid' ? now() : $fee->paid_date,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => [
                    'payment' => $payment,
                    'fee' => $fee->fresh()->load(['student.user', 'feeType'])
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Payment failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentFees($studentId)
    {
        $fees = Fee::with(['feeType', 'academicYear', 'payments'])
                   ->where('student_id', $studentId)
                   ->orderBy('due_date', 'desc')
                   ->get();

        $summary = [
            'total_fees' => $fees->sum('amount'),
            'paid_fees' => $fees->sum('paid_amount'),
            'pending_fees' => $fees->sum('pending_amount'),
            'overdue_fees' => $fees->where('status', 'overdue')->sum('pending_amount')
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'fees' => $fees,
                'summary' => $summary
            ]
        ]);
    }

    public function getFeesSummary(Request $request)
    {
        $query = Fee::query();

        if ($request->has('class_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $summary = [
            'total_fees' => $query->sum('amount'),
            'collected_fees' => $query->sum('paid_amount'),
            'pending_fees' => $query->sum('pending_amount'),
            'overdue_fees' => $query->where('status', 'overdue')->sum('pending_amount'),
            'students_with_pending_fees' => $query->where('pending_amount', '>', 0)->distinct('student_id')->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    public function generateMonthlyFees(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'month' => 'required|string',
            'class_ids' => 'sometimes|array',
            'class_ids.*' => 'exists:classes,id'
        ]);

        $studentsQuery = Student::where('academic_year_id', $request->academic_year_id)
                               ->where('status', 'active');

        if ($request->has('class_ids')) {
            $studentsQuery->whereIn('class_id', $request->class_ids);
        }

        $students = $studentsQuery->with('class')->get();

        DB::beginTransaction();

        try {
            $feesCreated = 0;
            $monthlyFeeTypeId = FeeType::where('name', 'Monthly Fee')->first()->id ?? 1;

            foreach ($students as $student) {
                // Check if fee already exists for this month
                $existingFee = Fee::where('student_id', $student->id)
                                 ->where('fee_type_id', $monthlyFeeTypeId)
                                 ->where('month', $request->month)
                                 ->where('academic_year_id', $request->academic_year_id)
                                 ->first();

                if (!$existingFee) {
                    Fee::create([
                        'student_id' => $student->id,
                        'fee_type_id' => $monthlyFeeTypeId,
                        'academic_year_id' => $request->academic_year_id,
                        'month' => $request->month,
                        'amount' => $student->class->monthly_fee,
                        'paid_amount' => 0,
                        'pending_amount' => $student->class->monthly_fee,
                        'due_date' => now()->addDays(15), // 15 days from now
                        'status' => 'pending'
                    ]);

                    $feesCreated++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Monthly fees generated successfully for {$feesCreated} students"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate monthly fees',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getOverdueFees(Request $request)
    {
        $query = Fee::with(['student.user', 'student.class', 'feeType'])
                   ->where('due_date', '<', now())
                   ->where('status', '!=', 'paid');

        if ($request->has('class_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $overdueFees = $query->get();

        return response()->json([
            'success' => true,
            'data' => $overdueFees
        ]);
    }

    private function generateReceiptNumber()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastPayment = FeePayment::whereYear('created_at', $year)
                                ->whereMonth('created_at', $month)
                                ->orderBy('id', 'desc')
                                ->first();
        
        $sequence = $lastPayment ? (int)substr($lastPayment->receipt_number, -4) + 1 : 1;
        
        return 'RCP' . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}