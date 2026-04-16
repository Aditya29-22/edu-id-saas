<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\FeatureController;
use App\Http\Controllers\Web\PaymentWebController;

// Auth
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Protected (Authenticated Users)
Route::middleware(['auth'])->group(function () {
    
    // Subscription block is bypassed by super_admin. Applied to app core routes.
    Route::middleware(['subscription.check'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Schools (Admin/Super Admin only)
        Route::middleware('rbac:manage_schools,manage_school_users')->group(function () {
            Route::get('/schools', [DashboardController::class, 'schools'])->name('schools');
            Route::get('/schools/create', [DashboardController::class, 'createSchool'])->name('schools.create');
            Route::post('/schools', [DashboardController::class, 'storeSchool'])->name('schools.store');
            
            Route::get('/users', [DashboardController::class, 'users'])->name('users');
            Route::get('/users/create', [DashboardController::class, 'createUser'])->name('users.create');
            Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');
        });

        // Students & Attendance
        Route::middleware('rbac:manage_students,view_students,view_school_data,scan_qr')->group(function () {
            Route::get('/students', [DashboardController::class, 'students'])->name('students');
            Route::get('/students/create', [DashboardController::class, 'createStudent'])->name('students.create');
            Route::post('/students', [DashboardController::class, 'storeStudent'])->name('students.store');
            
            Route::get('/attendance', [DashboardController::class, 'attendance'])->name('attendance');
            
            // Scanner (Guard + Admins)
            Route::get('/scanner', [FeatureController::class, 'scannerPage'])->name('scanner');
            Route::post('/scanner/process', [FeatureController::class, 'processScan'])->name('scanner.process');
        });

        // QR Codes & ID Cards
        Route::middleware('rbac:manage_students,generate_id_cards,view_school_data')->group(function () {
            Route::get('/qr-codes', [FeatureController::class, 'qrCodes'])->name('qrcodes');
            Route::get('/qr-codes/generate/{student}', [FeatureController::class, 'generateQr'])->name('qrcodes.generate');
            Route::post('/qr-codes/bulk', [FeatureController::class, 'generateBulkQr'])->name('qrcodes.bulk');
            Route::get('/id-cards', [FeatureController::class, 'idCards'])->name('idcards');
        });
    });

    // Billing and Plans (No subscription check needed to access billing)
    Route::middleware('rbac:manage_plans,manage_subscription,view_payments')->group(function () {
        Route::get('/plans', [DashboardController::class, 'plans'])->name('plans');
        
        Route::get('/subscriptions', [PaymentWebController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/subscriptions/create', [PaymentWebController::class, 'createSubscription'])->name('subscriptions.create');
        Route::post('/subscriptions', [PaymentWebController::class, 'storeSubscription'])->name('subscriptions.store');

        Route::get('/payments', [PaymentWebController::class, 'payments'])->name('payments');
        Route::get('/payments/checkout', [PaymentWebController::class, 'checkout'])->name('payments.checkout');
        Route::post('/payments/process', [PaymentWebController::class, 'processPayment'])->name('payments.process');
    });
});
