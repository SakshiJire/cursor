<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'class', 'academicYear', 'parents']);

        // Filters
        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $students = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'blood_group' => 'nullable|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|string',
            'admission_date' => 'required|date',
            'medical_info' => 'nullable|array',
            'emergency_contacts' => 'nullable|array',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        DB::beginTransaction();
        
        try {
            // Create user account
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'is_active' => true
            ]);

            // Generate admission number
            $admissionNumber = $this->generateAdmissionNumber();

            $studentData = $request->only([
                'first_name', 'last_name', 'class_id', 'academic_year_id',
                'date_of_birth', 'gender', 'blood_group', 'address', 'city',
                'state', 'pincode', 'admission_date', 'medical_info',
                'emergency_contacts', 'notes'
            ]);

            $studentData['user_id'] = $user->id;
            $studentData['admission_number'] = $admissionNumber;
            $studentData['status'] = 'active';

            if ($request->hasFile('photo')) {
                $studentData['photo'] = $request->file('photo')->store('students', 'public');
            }

            $student = Student::create($studentData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student created successfully',
                'data' => $student->load(['user', 'class', 'academicYear'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $student = Student::with([
            'user', 'class', 'academicYear', 'parents',
            'fees.feeType', 'fees.payments',
            'examResults.exam.subject',
            'activityParticipations.activity'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'class_id' => 'sometimes|exists:classes,id',
            'date_of_birth' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female,other',
            'blood_group' => 'nullable|string',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string',
            'state' => 'sometimes|string',
            'pincode' => 'sometimes|string',
            'medical_info' => 'nullable|array',
            'emergency_contacts' => 'nullable|array',
            'status' => 'sometimes|in:active,inactive,transferred,graduated',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $updateData = $request->except(['photo']);

        if ($request->hasFile('photo')) {
            $updateData['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($updateData);

        // Update user name if first_name or last_name changed
        if ($request->has('first_name') || $request->has('last_name')) {
            $student->user->update([
                'name' => ($request->first_name ?? $student->first_name) . ' ' . 
                         ($request->last_name ?? $student->last_name)
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully',
            'data' => $student->fresh()->load(['user', 'class', 'academicYear'])
        ]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $student->user->delete(); // This will cascade delete the student
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByClass($classId)
    {
        $students = Student::with(['user', 'academicYear'])
                          ->where('class_id', $classId)
                          ->where('status', 'active')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    public function promoteStudents(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'new_class_id' => 'required|exists:classes,id',
            'new_academic_year_id' => 'required|exists:academic_years,id'
        ]);

        DB::beginTransaction();
        
        try {
            Student::whereIn('id', $request->student_ids)
                   ->update([
                       'class_id' => $request->new_class_id,
                       'academic_year_id' => $request->new_academic_year_id
                   ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Students promoted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to promote students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function generateAdmissionNumber()
    {
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
                             ->orderBy('id', 'desc')
                             ->first();
        
        $sequence = $lastStudent ? (int)substr($lastStudent->admission_number, -4) + 1 : 1;
        
        return $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}