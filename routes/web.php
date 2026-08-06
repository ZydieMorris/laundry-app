<?php

use App\Http\Controllers\BranchAdmin\DashboardController as BranchAdminDashboardController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\SuperAdmin\BranchController as SuperAdminBranchController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::get('dashboard', DashboardRedirectController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('branches', SuperAdminBranchController::class)->only(['index', 'store', 'update']);
    Route::resource('users', SuperAdminUserController::class)->only(['index', 'store', 'update', 'destroy']);
});

Route::middleware(['auth', 'verified', 'role:branch_admin'])->prefix('branch-admin')->name('branch_admin.')->group(function () {
    Route::get('/', [BranchAdminDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/', [StaffDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/', [CustomerDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/settings.php';
