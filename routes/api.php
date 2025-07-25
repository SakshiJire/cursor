<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\FeeController;
use App\Http\Controllers\Api\TimetableController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\LearningMaterialController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (no authentication required)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
    });

    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);

    // Student Management routes
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::post('/', [StudentController::class, 'store']);
        Route::get('/{id}', [StudentController::class, 'show']);
        Route::put('/{id}', [StudentController::class, 'update']);
        Route::delete('/{id}', [StudentController::class, 'destroy']);
        Route::get('/class/{classId}', [StudentController::class, 'getByClass']);
        Route::post('/promote', [StudentController::class, 'promoteStudents']);
    });

    // Staff Management routes
    Route::prefix('staff')->group(function () {
        Route::get('/', [StaffController::class, 'index']);
        Route::post('/', [StaffController::class, 'store']);
        Route::get('/{id}', [StaffController::class, 'show']);
        Route::put('/{id}', [StaffController::class, 'update']);
        Route::delete('/{id}', [StaffController::class, 'destroy']);
        Route::get('/designation/{designation}', [StaffController::class, 'getByDesignation']);
    });

    // Parent Management routes
    Route::prefix('parents')->group(function () {
        Route::get('/', [ParentController::class, 'index']);
        Route::post('/', [ParentController::class, 'store']);
        Route::get('/{id}', [ParentController::class, 'show']);
        Route::put('/{id}', [ParentController::class, 'update']);
        Route::delete('/{id}', [ParentController::class, 'destroy']);
        Route::get('/student/{studentId}', [ParentController::class, 'getByStudent']);
        Route::post('/link-student', [ParentController::class, 'linkStudent']);
        Route::delete('/unlink-student', [ParentController::class, 'unlinkStudent']);
    });

    // Class Management routes
    Route::prefix('classes')->group(function () {
        Route::get('/', [ClassController::class, 'index']);
        Route::post('/', [ClassController::class, 'store']);
        Route::get('/{id}', [ClassController::class, 'show']);
        Route::put('/{id}', [ClassController::class, 'update']);
        Route::delete('/{id}', [ClassController::class, 'destroy']);
        Route::get('/{id}/students', [ClassController::class, 'getStudents']);
        Route::post('/{id}/subjects', [ClassController::class, 'assignSubjects']);
        Route::delete('/{id}/subjects/{subjectId}', [ClassController::class, 'removeSubject']);
    });

    // Subject Management routes
    Route::prefix('subjects')->group(function () {
        Route::get('/', [SubjectController::class, 'index']);
        Route::post('/', [SubjectController::class, 'store']);
        Route::get('/{id}', [SubjectController::class, 'show']);
        Route::put('/{id}', [SubjectController::class, 'update']);
        Route::delete('/{id}', [SubjectController::class, 'destroy']);
    });

    // Fee Management routes
    Route::prefix('fees')->group(function () {
        Route::get('/', [FeeController::class, 'index']);
        Route::post('/', [FeeController::class, 'store']);
        Route::get('/{id}', [FeeController::class, 'show']);
        Route::put('/{id}', [FeeController::class, 'update']);
        Route::post('/{id}/payment', [FeeController::class, 'makePayment']);
        Route::get('/student/{studentId}', [FeeController::class, 'getStudentFees']);
        Route::get('/summary', [FeeController::class, 'getFeesSummary']);
        Route::post('/generate-monthly', [FeeController::class, 'generateMonthlyFees']);
        Route::get('/overdue', [FeeController::class, 'getOverdueFees']);
        
        // Fee Types
        Route::get('/types', [FeeController::class, 'getFeeTypes']);
        Route::post('/types', [FeeController::class, 'createFeeType']);
        Route::put('/types/{id}', [FeeController::class, 'updateFeeType']);
        Route::delete('/types/{id}', [FeeController::class, 'deleteFeeType']);
    });

    // Timetable Management routes
    Route::prefix('timetables')->group(function () {
        Route::get('/', [TimetableController::class, 'index']);
        Route::post('/', [TimetableController::class, 'store']);
        Route::get('/{id}', [TimetableController::class, 'show']);
        Route::put('/{id}', [TimetableController::class, 'update']);
        Route::delete('/{id}', [TimetableController::class, 'destroy']);
        Route::get('/class/{classId}', [TimetableController::class, 'getClassTimetable']);
        Route::get('/staff/{staffId}', [TimetableController::class, 'getStaffTimetable']);
        Route::post('/generate', [TimetableController::class, 'generateTimetable']);
        Route::post('/copy', [TimetableController::class, 'copyTimetable']);
    });

    // Exam Management routes
    Route::prefix('exams')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::post('/', [ExamController::class, 'store']);
        Route::get('/{id}', [ExamController::class, 'show']);
        Route::put('/{id}', [ExamController::class, 'update']);
        Route::delete('/{id}', [ExamController::class, 'destroy']);
        Route::get('/class/{classId}', [ExamController::class, 'getClassExams']);
        Route::post('/{id}/results', [ExamController::class, 'addResults']);
        Route::put('/{id}/results/{resultId}', [ExamController::class, 'updateResult']);
        Route::get('/{id}/marksheet/{studentId}', [ExamController::class, 'generateMarksheet']);
        Route::get('/student/{studentId}/results', [ExamController::class, 'getStudentResults']);
        
        // Exam Types
        Route::get('/types', [ExamController::class, 'getExamTypes']);
        Route::post('/types', [ExamController::class, 'createExamType']);
        Route::put('/types/{id}', [ExamController::class, 'updateExamType']);
        Route::delete('/types/{id}', [ExamController::class, 'deleteExamType']);
    });

    // Activity Management routes
    Route::prefix('activities')->group(function () {
        Route::get('/', [ActivityController::class, 'index']);
        Route::post('/', [ActivityController::class, 'store']);
        Route::get('/{id}', [ActivityController::class, 'show']);
        Route::put('/{id}', [ActivityController::class, 'update']);
        Route::delete('/{id}', [ActivityController::class, 'destroy']);
        Route::get('/class/{classId}', [ActivityController::class, 'getClassActivities']);
        Route::post('/{id}/register', [ActivityController::class, 'registerStudent']);
        Route::delete('/{id}/unregister/{studentId}', [ActivityController::class, 'unregisterStudent']);
        Route::put('/{id}/participation/{studentId}', [ActivityController::class, 'updateParticipation']);
    });

    // Learning Material Management routes
    Route::prefix('learning-materials')->group(function () {
        Route::get('/', [LearningMaterialController::class, 'index']);
        Route::post('/', [LearningMaterialController::class, 'store']);
        Route::get('/{id}', [LearningMaterialController::class, 'show']);
        Route::put('/{id}', [LearningMaterialController::class, 'update']);
        Route::delete('/{id}', [LearningMaterialController::class, 'destroy']);
        Route::get('/class/{classId}', [LearningMaterialController::class, 'getClassMaterials']);
        Route::get('/subject/{subjectId}', [LearningMaterialController::class, 'getSubjectMaterials']);
        Route::get('/{id}/download', [LearningMaterialController::class, 'download']);
        Route::post('/{id}/view', [LearningMaterialController::class, 'logView']);
        
        // Categories
        Route::get('/categories', [LearningMaterialController::class, 'getCategories']);
        Route::post('/categories', [LearningMaterialController::class, 'createCategory']);
        Route::put('/categories/{id}', [LearningMaterialController::class, 'updateCategory']);
        Route::delete('/categories/{id}', [LearningMaterialController::class, 'deleteCategory']);
    });

    // Communication/Chat routes
    Route::prefix('chat')->group(function () {
        // Groups
        Route::get('/groups', [ChatController::class, 'getGroups']);
        Route::post('/groups', [ChatController::class, 'createGroup']);
        Route::put('/groups/{groupId}', [ChatController::class, 'updateGroup']);
        Route::post('/groups/{groupId}/members', [ChatController::class, 'addMembers']);
        Route::delete('/groups/{groupId}/members', [ChatController::class, 'removeMembers']);
        
        // Messages
        Route::get('/messages/group/{groupId}', [ChatController::class, 'getMessages']);
        Route::get('/messages/user/{userId}', [ChatController::class, 'getMessages']);
        Route::post('/messages', [ChatController::class, 'sendMessage']);
        Route::put('/messages/{messageId}', [ChatController::class, 'updateMessage']);
        Route::delete('/messages/{messageId}', [ChatController::class, 'deleteMessage']);
        Route::post('/messages/{messageId}/read', [ChatController::class, 'markAsRead']);
        Route::post('/messages/{messageId}/react', [ChatController::class, 'reactToMessage']);
        
        // Private chats
        Route::get('/private-chats', [ChatController::class, 'getPrivateChats']);
    });

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/', [NotificationController::class, 'store']);
        Route::get('/{id}', [NotificationController::class, 'show']);
        Route::put('/{id}', [NotificationController::class, 'update']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::get('/unread/count', [NotificationController::class, 'getUnreadCount']);
        
        // Announcements
        Route::get('/announcements', [NotificationController::class, 'getAnnouncements']);
        Route::post('/announcements', [NotificationController::class, 'createAnnouncement']);
        Route::put('/announcements/{id}', [NotificationController::class, 'updateAnnouncement']);
        Route::delete('/announcements/{id}', [NotificationController::class, 'deleteAnnouncement']);
        Route::post('/announcements/{id}/publish', [NotificationController::class, 'publishAnnouncement']);
    });

    // Academic Year routes
    Route::prefix('academic-years')->group(function () {
        Route::get('/', [AcademicYearController::class, 'index']);
        Route::post('/', [AcademicYearController::class, 'store']);
        Route::get('/{id}', [AcademicYearController::class, 'show']);
        Route::put('/{id}', [AcademicYearController::class, 'update']);
        Route::delete('/{id}', [AcademicYearController::class, 'destroy']);
        Route::post('/{id}/activate', [AcademicYearController::class, 'activate']);
        Route::get('/current', [AcademicYearController::class, 'getCurrentYear']);
    });

    // Reports routes
    Route::prefix('reports')->group(function () {
        Route::get('/students', [ReportController::class, 'studentsReport']);
        Route::get('/fees', [ReportController::class, 'feesReport']);
        Route::get('/attendance', [ReportController::class, 'attendanceReport']);
        Route::get('/exams', [ReportController::class, 'examsReport']);
        Route::get('/activities', [ReportController::class, 'activitiesReport']);
        Route::get('/class-summary/{classId}', [ReportController::class, 'classSummary']);
        Route::get('/student-profile/{studentId}', [ReportController::class, 'studentProfile']);
    });
});

// Fallback route for API
Route::fallback(function(){
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});