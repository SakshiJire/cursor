<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ClassModelController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\FeePaymentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamResultController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\StudyMaterialController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FileUploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    });

    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Institution management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('institutions', InstitutionController::class);
    });

    // Student management
    Route::apiResource('students', StudentController::class);
    Route::prefix('students')->group(function () {
        Route::post('/{student}/documents', [StudentController::class, 'uploadDocuments']);
        Route::get('/{student}/profile', [StudentController::class, 'profile']);
        Route::get('/{student}/fees', [StudentController::class, 'fees']);
        Route::get('/{student}/attendance', [StudentController::class, 'attendance']);
        Route::get('/{student}/results', [StudentController::class, 'results']);
        Route::get('/by-class/{classId}', [StudentController::class, 'byClass']);
    });

    // Staff management
    Route::apiResource('staff', StaffController::class);
    Route::prefix('staff')->group(function () {
        Route::post('/{staff}/documents', [StaffController::class, 'uploadDocuments']);
        Route::get('/{staff}/profile', [StaffController::class, 'profile']);
        Route::get('/{staff}/leaves', [StaffController::class, 'leaves']);
        Route::get('/{staff}/salary', [StaffController::class, 'salary']);
        Route::get('/teachers', [StaffController::class, 'teachers']);
    });

    // Class management
    Route::apiResource('classes', ClassModelController::class);
    Route::prefix('classes')->group(function () {
        Route::get('/{class}/students', [ClassModelController::class, 'students']);
        Route::get('/{class}/timetable', [ClassModelController::class, 'timetable']);
        Route::get('/{class}/subjects', [ClassModelController::class, 'subjects']);
        Route::post('/{class}/assign-teacher', [ClassModelController::class, 'assignTeacher']);
    });

    // Subject management
    Route::apiResource('subjects', SubjectController::class);
    Route::prefix('subjects')->group(function () {
        Route::get('/{subject}/teachers', [SubjectController::class, 'teachers']);
        Route::get('/{subject}/classes', [SubjectController::class, 'classes']);
    });

    // Fee management
    Route::apiResource('fee-structures', FeeStructureController::class);
    Route::apiResource('fee-payments', FeePaymentController::class);
    Route::prefix('fees')->group(function () {
        Route::post('/payments/{payment}/receipt', [FeePaymentController::class, 'generateReceipt']);
        Route::get('/outstanding', [FeePaymentController::class, 'outstanding']);
        Route::get('/reports', [FeePaymentController::class, 'reports']);
    });

    // Attendance management
    Route::apiResource('attendances', AttendanceController::class);
    Route::prefix('attendance')->group(function () {
        Route::post('/mark', [AttendanceController::class, 'mark']);
        Route::get('/class/{classId}/date/{date}', [AttendanceController::class, 'byClassAndDate']);
        Route::get('/student/{studentId}/month/{month}', [AttendanceController::class, 'monthlyReport']);
        Route::post('/bulk-mark', [AttendanceController::class, 'bulkMark']);
    });

    // Examination management
    Route::apiResource('exams', ExamController::class);
    Route::apiResource('exam-results', ExamResultController::class);
    Route::prefix('exams')->group(function () {
        Route::get('/{exam}/results', [ExamController::class, 'results']);
        Route::post('/{exam}/publish-results', [ExamController::class, 'publishResults']);
        Route::get('/{exam}/report-cards', [ExamController::class, 'reportCards']);
        Route::post('/results/bulk-entry', [ExamResultController::class, 'bulkEntry']);
    });

    // Timetable management
    Route::apiResource('timetables', TimetableController::class);
    Route::prefix('timetable')->group(function () {
        Route::get('/class/{classId}', [TimetableController::class, 'byClass']);
        Route::get('/teacher/{teacherId}', [TimetableController::class, 'byTeacher']);
        Route::post('/generate', [TimetableController::class, 'generate']);
    });

    // Learning Management System
    Route::apiResource('study-materials', StudyMaterialController::class);
    Route::prefix('study-materials')->group(function () {
        Route::post('/upload', [StudyMaterialController::class, 'upload']);
        Route::get('/class/{classId}/subject/{subjectId}', [StudyMaterialController::class, 'byClassAndSubject']);
        Route::post('/{material}/download', [StudyMaterialController::class, 'download']);
    });

    // Leave management
    Route::apiResource('leaves', LeaveController::class);
    Route::prefix('leaves')->group(function () {
        Route::post('/{leave}/approve', [LeaveController::class, 'approve']);
        Route::post('/{leave}/reject', [LeaveController::class, 'reject']);
        Route::get('/staff/{staffId}', [LeaveController::class, 'byStaff']);
        Route::get('/pending', [LeaveController::class, 'pending']);
    });

    // Salary management
    Route::apiResource('salaries', SalaryController::class);
    Route::prefix('salaries')->group(function () {
        Route::post('/process/{month}', [SalaryController::class, 'processMonth']);
        Route::get('/staff/{staffId}', [SalaryController::class, 'byStaff']);
        Route::post('/{salary}/payslip', [SalaryController::class, 'generatePayslip']);
    });

    // Transport management
    Route::apiResource('transports', TransportController::class);
    Route::prefix('transport')->group(function () {
        Route::get('/{transport}/students', [TransportController::class, 'students']);
        Route::post('/{transport}/assign-student', [TransportController::class, 'assignStudent']);
        Route::delete('/{transport}/unassign-student/{student}', [TransportController::class, 'unassignStudent']);
        Route::get('/routes', [TransportController::class, 'routes']);
    });

    // Hostel management
    Route::apiResource('hostels', HostelController::class);
    Route::prefix('hostel')->group(function () {
        Route::get('/{hostel}/rooms', [HostelController::class, 'rooms']);
        Route::get('/{hostel}/students', [HostelController::class, 'students']);
        Route::post('/assign-room', [HostelController::class, 'assignRoom']);
        Route::post('/checkout', [HostelController::class, 'checkout']);
    });

    // Communication/Messaging
    Route::apiResource('messages', MessageController::class);
    Route::prefix('messages')->group(function () {
        Route::get('/inbox', [MessageController::class, 'inbox']);
        Route::get('/sent', [MessageController::class, 'sent']);
        Route::post('/{message}/read', [MessageController::class, 'markAsRead']);
        Route::post('/broadcast', [MessageController::class, 'broadcast']);
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/admission', [ReportController::class, 'admission']);
        Route::get('/fee-collection', [ReportController::class, 'feeCollection']);
        Route::get('/attendance', [ReportController::class, 'attendance']);
        Route::get('/exam-results', [ReportController::class, 'examResults']);
        Route::get('/staff-salary', [ReportController::class, 'staffSalary']);
        Route::get('/transport', [ReportController::class, 'transport']);
        Route::get('/hostel', [ReportController::class, 'hostel']);
    });

    // File upload
    Route::prefix('files')->group(function () {
        Route::post('/upload', [FileUploadController::class, 'upload']);
        Route::delete('/{file}', [FileUploadController::class, 'delete']);
    });
});