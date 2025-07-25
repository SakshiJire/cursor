<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $query = Student::with(['user', 'class', 'institute'])
                       ->byInstitute($request->user()->institute_id);

        if ($request->has('class_id')) {
            $query->byClass($request->class_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('admission_number', 'like', "%{$search}%");
        }

        $students = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:6',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'class_id' => 'required|exists:classes,id',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'father_phone' => 'required|string|max:15',
            'mother_phone' => 'nullable|string|max:15',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'blood_group' => 'nullable|string|max:10',
            'previous_school' => 'nullable|string|max:255',
            'previous_percentage' => 'nullable|numeric|min:0|max:100',
            'transport_required' => 'required|in:yes,no',
            'hostel_required' => 'required|in:yes,no'
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

            // Create user account
            $user = User::create([
                'institute_id' => $request->user()->institute_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'status' => 'active'
            ]);

            // Generate admission number
            $year = date('Y');
            $instituteCode = $user->institute->code;
            $lastStudent = Student::where('institute_id', $user->institute_id)
                                 ->where('admission_date', '>=', $year . '-01-01')
                                 ->orderBy('id', 'desc')
                                 ->first();
            
            $sequence = $lastStudent ? (int)substr($lastStudent->admission_number, -4) + 1 : 1;
            $admissionNumber = $instituteCode . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'institute_id' => $user->institute_id,
                'class_id' => $request->class_id,
                'admission_number' => $admissionNumber,
                'admission_date' => now(),
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'guardian_name' => $request->guardian_name,
                'father_phone' => $request->father_phone,
                'mother_phone' => $request->mother_phone,
                'guardian_phone' => $request->guardian_phone,
                'father_occupation' => $request->father_occupation,
                'mother_occupation' => $request->mother_occupation,
                'blood_group' => $request->blood_group,
                'medical_history' => $request->medical_history,
                'previous_school' => $request->previous_school,
                'previous_percentage' => $request->previous_percentage,
                'special_notes' => $request->special_notes,
                'transport_required' => $request->transport_required,
                'hostel_required' => $request->hostel_required,
                'status' => 'active'
            ]);

            DB::commit();

            $student->load(['user', 'class', 'institute']);

            return response()->json([
                'success' => true,
                'message' => 'Student registered successfully',
                'data' => $student
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $student = Student::with(['user', 'class', 'institute', 'feePayments', 'examResults', 'attendances'])
                         ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'class_id' => 'sometimes|exists:classes,id',
            'father_name' => 'sometimes|string|max:255',
            'mother_name' => 'sometimes|string|max:255',
            'father_phone' => 'sometimes|string|max:15',
            'mother_phone' => 'nullable|string|max:15',
            'blood_group' => 'nullable|string|max:10',
            'transport_required' => 'sometimes|in:yes,no',
            'hostel_required' => 'sometimes|in:yes,no',
            'status' => 'sometimes|in:active,inactive,passed_out,transferred'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student->update($request->only([
            'class_id', 'father_name', 'mother_name', 'father_phone', 'mother_phone',
            'blood_group', 'medical_history', 'special_notes', 'transport_required',
            'hostel_required', 'status'
        ]));

        // Update user info if provided
        if ($request->has(['first_name', 'last_name', 'address', 'city', 'state', 'pincode'])) {
            $student->user->update($request->only([
                'first_name', 'last_name', 'address', 'city', 'state', 'pincode'
            ]));
        }

        $student->load(['user', 'class', 'institute']);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully',
            'data' => $student
        ]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        
        // Soft delete by updating status
        $student->update(['status' => 'inactive']);
        $student->user->update(['status' => 'inactive']);

        return response()->json([
            'success' => true,
            'message' => 'Student deactivated successfully'
        ]);
    }

    public function uploadDocuments(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120' // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::findOrFail($id);
        $uploadedFiles = [];

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('student_documents', $filename, 'public');
                $uploadedFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'uploaded_at' => now()
                ];
            }
        }

        $existingDocs = $student->documents ?? [];
        $allDocs = array_merge($existingDocs, $uploadedFiles);
        
        $student->update(['documents' => $allDocs]);

        return response()->json([
            'success' => true,
            'message' => 'Documents uploaded successfully',
            'data' => $uploadedFiles
        ]);
    }

    public function getByClass($classId)
    {
        $students = Student::with(['user'])
                          ->byClass($classId)
                          ->active()
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    public function bulkPromote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'new_class_id' => 'required|exists:classes,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        Student::whereIn('id', $request->student_ids)
               ->update(['class_id' => $request->new_class_id]);

        return response()->json([
            'success' => true,
            'message' => 'Students promoted successfully'
        ]);
    }
}