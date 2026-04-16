<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PaymentController;

// Public Routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Super Admin Only
    Route::middleware('role:super_admin')->group(function () {
        Route::apiResource('schools', SchoolController::class);
        Route::apiResource('plans', PlanController::class);
        Route::get('/all-users', [UserController::class, 'index']);
    });

    // School Scoped Routes (Tenant Isolated + Subscription Check)
    Route::middleware(['tenant', 'subscription.check'])->group(function () {

        // Users (School Admin)
        Route::middleware('role:super_admin,school_admin')->group(function () {
            Route::apiResource('users', UserController::class);
        });

        // Students
        Route::middleware('role:super_admin,school_admin,teacher')->group(function () {
            Route::apiResource('students', StudentController::class);
            Route::post('/students/{id}/generate-id-card', [StudentController::class, 'generateIDCard']);
        });

        // Templates
        Route::middleware('role:super_admin,school_admin')->group(function () {
            Route::apiResource('templates', TemplateController::class)->only(['index', 'store', 'destroy']);
        });

        // Attendance - View
        Route::middleware('role:super_admin,school_admin,teacher,security_guard')->group(function () {
            Route::get('/attendance', [AttendanceController::class, 'index']);
            Route::get('/attendance/summary', [AttendanceController::class, 'dailySummary']);
        });

        // Attendance - Scan
        Route::middleware('role:super_admin,school_admin,security_guard')->group(function () {
            Route::post('/attendance/scan', [AttendanceController::class, 'scan']);
        });
    });

    // Student's Own Data
    Route::middleware(['tenant', 'role:student'])->group(function () {
        Route::get('/my/attendance', [AttendanceController::class, 'myAttendance']);
    });

    // Payment Routes (Accessible even if subscription expired)
    Route::middleware('tenant')->group(function () {
        Route::middleware('role:super_admin,school_admin')->group(function () {
            Route::post('/payments/create-order', [PaymentController::class, 'createOrder']);
            Route::post('/payments/verify', [PaymentController::class, 'verify']);
            Route::get('/payments/history', [PaymentController::class, 'history']);
        });
    });

    // Plans (viewable by all authenticated users)
    Route::get('/plans', [PlanController::class, 'index']);
});
