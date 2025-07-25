<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\FeeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json(['status' => 'OK', 'timestamp' => now()]);
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/send-otp', [AuthController::class, 'sendOTP']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Student Management
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::post('/', [StudentController::class, 'store']);
        Route::get('/{id}', [StudentController::class, 'show']);
        Route::put('/{id}', [StudentController::class, 'update']);
        Route::delete('/{id}', [StudentController::class, 'destroy']);
        Route::post('/{id}/documents', [StudentController::class, 'uploadDocuments']);
        Route::get('/class/{classId}', [StudentController::class, 'getByClass']);
        Route::post('/bulk-promote', [StudentController::class, 'bulkPromote']);
    });

    // Fee Management
    Route::prefix('fees')->group(function () {
        // Fee Structures
        Route::get('/structures', [FeeController::class, 'getFeeStructures']);
        Route::post('/structures', [FeeController::class, 'storeFeeStructure']);
        Route::put('/structures/{id}', [FeeController::class, 'updateFeeStructure']);
        
        // Fee Payments
        Route::get('/payments', [FeeController::class, 'getFeePayments']);
        Route::post('/payments', [FeeController::class, 'recordPayment']);
        Route::get('/student/{studentId}', [FeeController::class, 'getStudentFees']);
        Route::get('/receipt/{paymentId}', [FeeController::class, 'generateReceipt']);
        Route::get('/collection-report', [FeeController::class, 'getFeeCollectionReport']);
        Route::post('/online-payment', [FeeController::class, 'processOnlinePayment']);
    });

    // Institute Management
    Route::prefix('institutes')->group(function () {
        Route::get('/', 'App\Http\Controllers\API\InstituteController@index');
        Route::post('/', 'App\Http\Controllers\API\InstituteController@store');
        Route::get('/{id}', 'App\Http\Controllers\API\InstituteController@show');
        Route::put('/{id}', 'App\Http\Controllers\API\InstituteController@update');
    });

    // Class Management
    Route::prefix('classes')->group(function () {
        Route::get('/', 'App\Http\Controllers\API\ClassController@index');
        Route::post('/', 'App\Http\Controllers\API\ClassController@store');
        Route::get('/{id}', 'App\Http\Controllers\API\ClassController@show');
        Route::put('/{id}', 'App\Http\Controllers\API\ClassController@update');
        Route::delete('/{id}', 'App\Http\Controllers\API\ClassController@destroy');
    });

    // Subject Management
    Route::prefix('subjects')->group(function () {
        Route::get('/', 'App\Http\Controllers\API\SubjectController@index');
        Route::post('/', 'App\Http\Controllers\API\SubjectController@store');
        Route::get('/{id}', 'App\Http\Controllers\API\SubjectController@show');
        Route::put('/{id}', 'App\Http\Controllers\API\SubjectController@update');
        Route::delete('/{id}', 'App\Http\Controllers\API\SubjectController@destroy');
    });

    // Attendance Management
    Route::prefix('attendance')->group(function () {
        Route::get('/', 'App\Http\Controllers\API\AttendanceController@index');
        Route::post('/mark', 'App\Http\Controllers\API\AttendanceController@markAttendance');
        Route::get('/class/{classId}/date/{date}', 'App\Http\Controllers\API\AttendanceController@getClassAttendance');
        Route::get('/student/{studentId}', 'App\Http\Controllers\API\AttendanceController@getStudentAttendance');
        Route::get('/report', 'App\Http\Controllers\API\AttendanceController@getAttendanceReport');
    });

    // Exam Management
    Route::prefix('exams')->group(function () {
        Route::get('/', 'App\Http\Controllers\API\ExamController@index');
        Route::post('/', 'App\Http\Controllers\API\ExamController@store');
        Route::get('/{id}', 'App\Http\Controllers\API\ExamController@show');
        Route::put('/{id}', 'App\Http\Controllers\API\ExamController@update');
        Route::delete('/{id}', 'App\Http\Controllers\API\ExamController@destroy');
        
        // Results
        Route::post('/{examId}/results', 'App\Http\Controllers\API\ExamController@storeResults');
        Route::get('/{examId}/results', 'App\Http\Controllers\API\ExamController@getResults');
        Route::get('/student/{studentId}/results', 'App\Http\Controllers\API\ExamController@getStudentResults');
        Route::get('/{examId}/report-card/{studentId}', 'App\Http\Controllers\API\ExamController@generateReportCard');
    });

    // Timetable Management
    Route::prefix('timetables')->group(function () {
        Route::get('/class/{classId}', 'App\Http\Controllers\API\TimetableController@getClassTimetable');
        Route::get('/teacher/{teacherId}', 'App\Http\Controllers\API\TimetableController@getTeacherTimetable');
        Route::post('/', 'App\Http\Controllers\API\TimetableController@store');
        Route::put('/{id}', 'App\Http\Controllers\API\TimetableController@update');
        Route::delete('/{id}', 'App\Http\Controllers\API\TimetableController@destroy');
    });

    // Learning Management System (LMS)
    Route::prefix('lms')->group(function () {
        Route::get('/materials', 'App\Http\Controllers\API\LMSController@getMaterials');
        Route::post('/materials', 'App\Http\Controllers\API\LMSController@uploadMaterial');
        Route::get('/materials/{id}', 'App\Http\Controllers\API\LMSController@showMaterial');
        Route::delete('/materials/{id}', 'App\Http\Controllers\API\LMSController@deleteMaterial');
        
        // Assignments
        Route::get('/assignments', 'App\Http\Controllers\API\LMSController@getAssignments');
        Route::post('/assignments', 'App\Http\Controllers\API\LMSController@createAssignment');
        Route::get('/assignments/{id}', 'App\Http\Controllers\API\LMSController@showAssignment');
        Route::post('/assignments/{id}/submit', 'App\Http\Controllers\API\LMSController@submitAssignment');
        Route::get('/assignments/{id}/submissions', 'App\Http\Controllers\API\LMSController@getSubmissions');
        Route::post('/assignments/{assignmentId}/grade/{submissionId}', 'App\Http\Controllers\API\LMSController@gradeSubmission');
    });

    // Staff Management
    Route::prefix('staff')->group(function () {
        Route::get('/', 'App\Http\Controllers\API\StaffController@index');
        Route::post('/', 'App\Http\Controllers\API\StaffController@store');
        Route::get('/{id}', 'App\Http\Controllers\API\StaffController@show');
        Route::put('/{id}', 'App\Http\Controllers\API\StaffController@update');
        Route::delete('/{id}', 'App\Http\Controllers\API\StaffController@destroy');
    });

    // Leave Management
    Route::prefix('leaves')->group(function () {
        Route::get('/', 'App\Http\Controllers\API\LeaveController@index');
        Route::post('/', 'App\Http\Controllers\API\LeaveController@store');
        Route::get('/{id}', 'App\Http\Controllers\API\LeaveController@show');
        Route::put('/{id}/approve', 'App\Http\Controllers\API\LeaveController@approve');
        Route::put('/{id}/reject', 'App\Http\Controllers\API\LeaveController@reject');
        Route::get('/user/{userId}', 'App\Http\Controllers\API\LeaveController@getUserLeaves');
        Route::get('/balance/{userId}', 'App\Http\Controllers\API\LeaveController@getLeaveBalance');
    });

    // Salary Management
    Route::prefix('salaries')->group(function () {
        Route::get('/structures', 'App\Http\Controllers\API\SalaryController@getStructures');
        Route::post('/structures', 'App\Http\Controllers\API\SalaryController@createStructure');
        Route::put('/structures/{id}', 'App\Http\Controllers\API\SalaryController@updateStructure');
        Route::get('/payments', 'App\Http\Controllers\API\SalaryController@getPayments');
        Route::post('/generate-payslips', 'App\Http\Controllers\API\SalaryController@generatePayslips');
        Route::get('/payslip/{id}', 'App\Http\Controllers\API\SalaryController@getPayslip');
    });

    // Communication/Chat
    Route::prefix('communication')->group(function () {
        Route::get('/chats', 'App\Http\Controllers\API\CommunicationController@getChats');
        Route::post('/chats', 'App\Http\Controllers\API\CommunicationController@createChat');
        Route::get('/chats/{id}/messages', 'App\Http\Controllers\API\CommunicationController@getMessages');
        Route::post('/chats/{id}/messages', 'App\Http\Controllers\API\CommunicationController@sendMessage');
        Route::put('/messages/{id}/read', 'App\Http\Controllers\API\CommunicationController@markAsRead');
    });

    // Transport Management
    Route::prefix('transport')->group(function () {
        Route::get('/vehicles', 'App\Http\Controllers\API\TransportController@getVehicles');
        Route::post('/vehicles', 'App\Http\Controllers\API\TransportController@createVehicle');
        Route::put('/vehicles/{id}', 'App\Http\Controllers\API\TransportController@updateVehicle');
        
        Route::get('/routes', 'App\Http\Controllers\API\TransportController@getRoutes');
        Route::post('/routes', 'App\Http\Controllers\API\TransportController@createRoute');
        Route::put('/routes/{id}', 'App\Http\Controllers\API\TransportController@updateRoute');
        
        Route::get('/student-assignments', 'App\Http\Controllers\API\TransportController@getStudentAssignments');
        Route::post('/assign-student', 'App\Http\Controllers\API\TransportController@assignStudent');
        Route::get('/route-report/{routeId}', 'App\Http\Controllers\API\TransportController@getRouteReport');
    });

    // Hostel Management
    Route::prefix('hostels')->group(function () {
        Route::get('/', 'App\Http\Controllers\API\HostelController@index');
        Route::post('/', 'App\Http\Controllers\API\HostelController@store');
        Route::get('/{id}', 'App\Http\Controllers\API\HostelController@show');
        Route::put('/{id}', 'App\Http\Controllers\API\HostelController@update');
        
        Route::get('/{hostelId}/rooms', 'App\Http\Controllers\API\HostelController@getRooms');
        Route::post('/{hostelId}/rooms', 'App\Http\Controllers\API\HostelController@createRoom');
        Route::put('/rooms/{id}', 'App\Http\Controllers\API\HostelController@updateRoom');
        
        Route::get('/student-assignments', 'App\Http\Controllers\API\HostelController@getStudentAssignments');
        Route::post('/assign-student', 'App\Http\Controllers\API\HostelController@assignStudent');
        Route::post('/checkout-student/{id}', 'App\Http\Controllers\API\HostelController@checkoutStudent');
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/dashboard', 'App\Http\Controllers\API\ReportController@getDashboard');
        Route::get('/admissions', 'App\Http\Controllers\API\ReportController@getAdmissionStats');
        Route::get('/fee-collection', 'App\Http\Controllers\API\ReportController@getFeeCollection');
        Route::get('/attendance-summary', 'App\Http\Controllers\API\ReportController@getAttendanceSummary');
        Route::get('/exam-performance', 'App\Http\Controllers\API\ReportController@getExamPerformance');
        Route::get('/transport-summary', 'App\Http\Controllers\API\ReportController@getTransportSummary');
        Route::get('/hostel-summary', 'App\Http\Controllers\API\ReportController@getHostelSummary');
        Route::get('/staff-summary', 'App\Http\Controllers\API\ReportController@getStaffSummary');
    });

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', 'App\Http\Controllers\API\UserController@index');
        Route::post('/', 'App\Http\Controllers\API\UserController@store');
        Route::get('/{id}', 'App\Http\Controllers\API\UserController@show');
        Route::put('/{id}', 'App\Http\Controllers\API\UserController@update');
        Route::delete('/{id}', 'App\Http\Controllers\API\UserController@destroy');
        Route::put('/{id}/change-password', 'App\Http\Controllers\API\UserController@changePassword');
        Route::post('/{id}/upload-profile-image', 'App\Http\Controllers\API\UserController@uploadProfileImage');
    });

    // File Management
    Route::prefix('files')->group(function () {
        Route::post('/upload', 'App\Http\Controllers\API\FileController@upload');
        Route::get('/download/{id}', 'App\Http\Controllers\API\FileController@download');
        Route::delete('/{id}', 'App\Http\Controllers\API\FileController@delete');
    });
});