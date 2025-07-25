<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\FeePayment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Fee Structure Management
    public function getFeeStructures(Request $request)
    {
        $query = FeeStructure::with(['class', 'institute'])
                            ->byInstitute($request->user()->institute_id);

        if ($request->has('class_id')) {
            $query->byClass($request->class_id);
        }

        $feeStructures = $query->active()->get();

        return response()->json([
            'success' => true,
            'data' => $feeStructures
        ]);
    }

    public function storeFeeStructure(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'fee_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,half_yearly,yearly,one_time',
            'due_date' => 'required|date',
            'late_fee' => 'nullable|numeric|min:0',
            'grace_period_days' => 'nullable|integer|min:0',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $feeStructure = FeeStructure::create([
            'institute_id' => $request->user()->institute_id,
            'class_id' => $request->class_id,
            'fee_type' => $request->fee_type,
            'amount' => $request->amount,
            'frequency' => $request->frequency,
            'due_date' => $request->due_date,
            'late_fee' => $request->late_fee ?? 0,
            'grace_period_days' => $request->grace_period_days ?? 0,
            'description' => $request->description,
            'status' => 'active'
        ]);

        $feeStructure->load(['class', 'institute']);

        return response()->json([
            'success' => true,
            'message' => 'Fee structure created successfully',
            'data' => $feeStructure
        ], 201);
    }

    public function updateFeeStructure(Request $request, $id)
    {
        $feeStructure = FeeStructure::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'amount' => 'sometimes|numeric|min:0',
            'due_date' => 'sometimes|date',
            'late_fee' => 'sometimes|numeric|min:0',
            'grace_period_days' => 'sometimes|integer|min:0',
            'status' => 'sometimes|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $feeStructure->update($request->only([
            'amount', 'due_date', 'late_fee', 'grace_period_days', 'description', 'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Fee structure updated successfully',
            'data' => $feeStructure
        ]);
    }

    // Fee Payment Management
    public function getFeePayments(Request $request)
    {
        $query = FeePayment::with(['student.user', 'feeStructure'])
                          ->whereHas('student', function($q) use ($request) {
                              $q->byInstitute($request->user()->institute_id);
                          });

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_date_from')) {
            $query->where('payment_date', '>=', $request->payment_date_from);
        }

        if ($request->has('payment_date_to')) {
            $query->where('payment_date', '<=', $request->payment_date_to);
        }

        $payments = $query->orderBy('payment_date', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    public function recordPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,cheque,bank_transfer,online,card',
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $feeStructure = FeeStructure::findOrFail($request->fee_structure_id);
            $student = Student::findOrFail($request->student_id);

            // Calculate late fee if applicable
            $paymentDate = \Carbon\Carbon::parse($request->payment_date);
            $dueDate = \Carbon\Carbon::parse($feeStructure->due_date);
            $lateFee = 0;

            if ($paymentDate->gt($dueDate->addDays($feeStructure->grace_period_days))) {
                $lateFee = $feeStructure->late_fee;
            }

            // Generate receipt number
            $year = date('Y');
            $month = date('m');
            $lastPayment = FeePayment::whereYear('created_at', $year)
                                   ->whereMonth('created_at', $month)
                                   ->orderBy('id', 'desc')
                                   ->first();

            $sequence = $lastPayment ? (int)substr($lastPayment->receipt_number, -4) + 1 : 1;
            $receiptNumber = 'RCP' . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            $amountDue = $feeStructure->amount + $lateFee - ($request->discount ?? 0);

            $payment = FeePayment::create([
                'student_id' => $request->student_id,
                'fee_structure_id' => $request->fee_structure_id,
                'receipt_number' => $receiptNumber,
                'amount_due' => $amountDue,
                'amount_paid' => $request->amount_paid,
                'late_fee' => $lateFee,
                'discount' => $request->discount ?? 0,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'payment_date' => $request->payment_date,
                'due_date' => $feeStructure->due_date,
                'status' => $request->amount_paid >= $amountDue ? 'paid' : 'partial',
                'remarks' => $request->remarks,
                'collected_by' => $request->user()->full_name
            ]);

            DB::commit();

            $payment->load(['student.user', 'feeStructure']);

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => $payment
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Payment recording failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStudentFees($studentId)
    {
        $student = Student::with(['class', 'feePayments.feeStructure'])->findOrFail($studentId);
        
        // Get all fee structures for the student's class
        $feeStructures = FeeStructure::where('class_id', $student->class_id)
                                   ->active()
                                   ->get();

        $feesSummary = [];
        
        foreach ($feeStructures as $structure) {
            $payments = $student->feePayments->where('fee_structure_id', $structure->id);
            $totalPaid = $payments->sum('amount_paid');
            $totalDue = $structure->amount;
            
            $feesSummary[] = [
                'fee_structure' => $structure,
                'total_due' => $totalDue,
                'total_paid' => $totalPaid,
                'balance' => $totalDue - $totalPaid,
                'status' => $totalPaid >= $totalDue ? 'paid' : 'pending',
                'payments' => $payments->values()
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student,
                'fees_summary' => $feesSummary
            ]
        ]);
    }

    public function getFeeCollectionReport(Request $request)
    {
        $query = FeePayment::with(['student.user', 'feeStructure'])
                          ->whereHas('student', function($q) use ($request) {
                              $q->byInstitute($request->user()->institute_id);
                          });

        if ($request->has('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        if ($request->has('class_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $payments = $query->paid()->get();
        
        $summary = [
            'total_collections' => $payments->count(),
            'total_amount' => $payments->sum('amount_paid'),
            'payment_methods' => $payments->groupBy('payment_method')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('amount_paid')
                ];
            }),
            'daily_collections' => $payments->groupBy(function($payment) {
                return $payment->payment_date->format('Y-m-d');
            })->map(function($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('amount_paid')
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'payments' => $payments
            ]
        ]);
    }

    public function generateReceipt($paymentId)
    {
        $payment = FeePayment::with(['student.user', 'feeStructure.class'])
                            ->findOrFail($paymentId);

        // In a real implementation, you would generate a PDF here
        // For now, return the receipt data
        $receiptData = [
            'receipt_number' => $payment->receipt_number,
            'payment_date' => $payment->payment_date,
            'student_name' => $payment->student->user->full_name,
            'admission_number' => $payment->student->admission_number,
            'class' => $payment->feeStructure->class->full_name,
            'fee_type' => $payment->feeStructure->fee_type,
            'amount_due' => $payment->amount_due,
            'amount_paid' => $payment->amount_paid,
            'late_fee' => $payment->late_fee,
            'discount' => $payment->discount,
            'payment_method' => $payment->payment_method,
            'collected_by' => $payment->collected_by,
            'remarks' => $payment->remarks
        ];

        return response()->json([
            'success' => true,
            'data' => $receiptData
        ]);
    }

    // Dummy payment gateway integration
    public function processOnlinePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'amount' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Dummy payment processing
        $transactionId = 'TXN' . time() . rand(1000, 9999);
        $paymentStatus = rand(0, 1) ? 'success' : 'failed'; // Random success/failure

        if ($paymentStatus === 'success') {
            // Record the payment
            $paymentRequest = new Request([
                'student_id' => $request->student_id,
                'fee_structure_id' => $request->fee_structure_id,
                'amount_paid' => $request->amount,
                'payment_method' => 'online',
                'payment_date' => now(),
                'transaction_id' => $transactionId,
                'remarks' => 'Online payment via gateway'
            ]);

            return $this->recordPayment($paymentRequest);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment failed. Please try again.',
            'data' => [
                'transaction_id' => $transactionId,
                'status' => 'failed'
            ]
        ], 400);
    }
}