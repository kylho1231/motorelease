<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchAndSettingsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LicensePlateTrackingController;
use App\Http\Controllers\MotorcycleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrcrTrackingController;
use App\Http\Controllers\ReleaseManagementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Customer Tracking Portal & Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', [CustomerPortalController::class, 'landing'])->name('home');

Route::prefix('track')->name('customer.')->group(function () {
    Route::get('/', [CustomerPortalController::class, 'index'])->name('track');
    Route::post('/verify', [CustomerPortalController::class, 'verify'])->name('verify');
    Route::get('/status', [CustomerPortalController::class, 'showStatus'])->name('status');
    Route::post('/logout', [CustomerPortalController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Dealership Staff Authentication
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Dealership Back-Office Portal
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {

    // Main Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile & Password Change
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Customer Management
    Route::resource('customers', CustomerController::class);

    // Motorcycle Registration & Printable Tracking Slip
    Route::resource('motorcycles', MotorcycleController::class);
    Route::get('/motorcycles/{motorcycle}/print-slip', [MotorcycleController::class, 'printSlip'])->name('motorcycles.print_slip');

    // Independent OR/CR Tracking
    Route::get('/orcr', [OrcrTrackingController::class, 'index'])->name('orcr.index');
    Route::get('/orcr/{orcrRecord}', [OrcrTrackingController::class, 'show'])->name('orcr.show');
    Route::put('/orcr/{orcrRecord}/receipt', [OrcrTrackingController::class, 'updateReceipt'])->name('orcr.update_receipt');
    Route::post('/orcr/{orcrRecord}/verify-ready', [OrcrTrackingController::class, 'verifyReady'])->name('orcr.verify_ready');
    Route::post('/orcr/{orcrRecord}/hold', [OrcrTrackingController::class, 'putOnHold'])->name('orcr.put_on_hold');

    // Independent License Plate Tracking
    Route::get('/plates', [LicensePlateTrackingController::class, 'index'])->name('plates.index');
    Route::get('/plates/{plateRecord}', [LicensePlateTrackingController::class, 'show'])->name('plates.show');
    Route::put('/plates/{plateRecord}/receipt', [LicensePlateTrackingController::class, 'updateReceipt'])->name('plates.update_receipt');
    Route::post('/plates/{plateRecord}/verify-ready', [LicensePlateTrackingController::class, 'verifyReady'])->name('plates.verify_ready');
    Route::post('/plates/{plateRecord}/hold', [LicensePlateTrackingController::class, 'putOnHold'])->name('plates.put_on_hold');

    // Document & Plate Release Processing
    Route::get('/releases', [ReleaseManagementController::class, 'index'])->name('releases.index');
    Route::get('/releases/create', [ReleaseManagementController::class, 'create'])->name('releases.create');
    Route::post('/releases', [ReleaseManagementController::class, 'store'])->name('releases.store');
    Route::get('/releases/{release}', [ReleaseManagementController::class, 'show'])->name('releases.show');
    Route::get('/releases/{release}/print-receipt', [ReleaseManagementController::class, 'printReceipt'])->name('releases.print_receipt');

    // SMS Notifications & Delivery Logs
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{notification}/resend', [NotificationController::class, 'resend'])->name('notifications.resend');

    // Reports & Analytics (Admin & Dealership Manager)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export_csv');
    });

    // Administrator Only Configurations
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle_active');

        Route::get('/settings/branches', [BranchAndSettingsController::class, 'branchIndex'])->name('settings.branches');
        Route::post('/settings/branches', [BranchAndSettingsController::class, 'branchStore'])->name('settings.branches.store');
        Route::put('/settings/branches/{branch}', [BranchAndSettingsController::class, 'branchUpdate'])->name('settings.branches.update');

        Route::get('/settings', [BranchAndSettingsController::class, 'settingsIndex'])->name('settings.index');
        Route::put('/settings', [BranchAndSettingsController::class, 'updateSettings'])->name('settings.update');
        Route::put('/settings/templates/{template}', [BranchAndSettingsController::class, 'updateTemplate'])->name('settings.template.update');

        Route::get('/settings/audit-logs', [BranchAndSettingsController::class, 'auditLogs'])->name('settings.audit_logs');
    });
});
