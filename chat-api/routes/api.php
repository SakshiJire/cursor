<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\GroupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::put('update-profile', [AuthController::class, 'updateProfile']);
});

// Chat Routes (Protected)
Route::prefix('chat')->middleware('auth:api')->group(function () {
    // Private Messages
    Route::post('send-private', [ChatController::class, 'sendPrivateMessage']);
    Route::get('private/{userId}', [ChatController::class, 'getPrivateConversation']);
    
    // Group Messages
    Route::post('send-group', [ChatController::class, 'sendGroupMessage']);
    Route::get('group/{groupId}', [ChatController::class, 'getGroupConversation']);
    
    // Chat Management
    Route::get('previews', [ChatController::class, 'getChatPreviews']);
    Route::post('search', [ChatController::class, 'searchMessages']);
    Route::post('mark-seen', [ChatController::class, 'markAsSeen']);
    Route::get('unseen-count', [ChatController::class, 'getUnseenCount']);
});

// Group Management Routes (Protected)
Route::prefix('groups')->middleware('auth:api')->group(function () {
    // User Groups
    Route::get('my-groups', [GroupController::class, 'getUserGroups']);
    Route::get('{groupId}', [GroupController::class, 'getGroupDetails']);
    Route::post('{groupId}/leave', [GroupController::class, 'leaveGroup']);
    
    // Admin Only Routes
    Route::middleware('admin')->group(function () {
        Route::post('create', [GroupController::class, 'createGroup']);
        Route::get('', [GroupController::class, 'getAllGroups']);
        Route::put('{groupId}', [GroupController::class, 'updateGroup']);
        Route::delete('{groupId}', [GroupController::class, 'deleteGroup']);
        Route::post('{groupId}/add-users', [GroupController::class, 'addUsersToGroup']);
        Route::post('{groupId}/remove-user', [GroupController::class, 'removeUserFromGroup']);
        Route::get('admin/available-users', [GroupController::class, 'getAvailableUsers']);
    });
});

// Test route to verify API is working
Route::get('test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Chat API is working!',
        'timestamp' => now(),
    ]);
});