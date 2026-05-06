<?php

use Illuminate\Support\Facades\Route;

// === Контроллеры команды ===
use App\Http\Controllers\SamosvalController;
use App\Http\Controllers\SamosvalProblemsController;
use App\Http\Controllers\SamosvalSolutionsController;
use App\Http\Controllers\SamosvalRequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Общие маршруты для команды + наш функционал
*/

// --- SamosvalS ---
Route::get('/Samosvals', [SamosvalController::class, 'index']);
Route::post('/Samosvals', [SamosvalController::class, 'store']);
Route::patch('/Samosvals/{samosval}', [SamosvalController::class, 'update']);

Route::get('/Samosvals/problems', [SamosvalProblemsController::class, 'index']);
Route::post('/Samosvals/problems', [SamosvalProblemsController::class, 'store']);
Route::patch('/Samosvals/problems/{problem}', [SamosvalProblemsController::class, 'update']);

Route::get('/Samosvals/solutions', [SamosvalSolutionsController::class, 'index']);
Route::post('/Samosvals/solutions', [SamosvalSolutionsController::class, 'store']);
Route::patch('/Samosvals/solutions/{solution}', [SamosvalSolutionsController::class, 'update']);

Route::get('/Samosval-requests', [SamosvalRequestController::class, 'index']);
Route::post('/Samosval-requests', [SamosvalRequestController::class, 'store']);
Route::patch('/Samosval-requests/{samosvalRequest}', [SamosvalRequestController::class, 'update']);
Route::patch('/Samosval-requests/{samosvalRequest}/take', [SamosvalRequestController::class, 'takeInWork']);
Route::patch('/Samosval-requests/{samosvalRequest}/close', [SamosvalRequestController::class, 'close']);
Route::patch('/Samosval-requests/{samosvalRequest}/cancel', [SamosvalRequestController::class, 'cancel']);

// Inbox (входящие лиды)
use App\Http\Controllers\InboxController;
Route::get('/inbox', [InboxController::class, 'index']);
Route::get('/inbox/{id}/history', [InboxController::class, 'history']);
Route::post('/inbox', [InboxController::class, 'store']);
Route::patch('/inbox/{id}', [InboxController::class, 'update']);
Route::delete('/inbox/{id}', [InboxController::class, 'destroy']);

// Auth (авторизация)
use App\Http\Controllers\AuthController;
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);
Route::get('/users', [AuthController::class, 'users']);

// Staff members (сотрудники)
use App\Http\Controllers\StaffMemberController;
Route::get('/staff-members', [StaffMemberController::class, 'index']);
Route::post('/staff-members', [StaffMemberController::class, 'store']);
Route::patch('/staff-members/{id}', [StaffMemberController::class, 'update']);
Route::delete('/staff-members/{id}', [StaffMemberController::class, 'destroy']);

// Comments (комментарии к заявкам)
use App\Http\Controllers\CommentController;
Route::get('/leads/{leadId}/comments', [CommentController::class, 'index']);
Route::post('/leads/{leadId}/comments', [CommentController::class, 'store']);