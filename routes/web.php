<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeaveController;

Route::redirect('/', '/admin/login');

Route::middleware('guest')->group(function () {
    // Admin login routes
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])
        ->name('admin.login');

    Route::post('/admin/login', [AuthController::class, 'adminLogin'])
        ->name('admin.login.perform');

    // Employee login routes
    Route::get('/employee/login', [AuthController::class, 'showEmployeeLoginForm'])
        ->name('employee.login');

    Route::post('/employee/login', [AuthController::class, 'employeeLogin'])
        ->name('employee.login.perform');
});

// Admin routes (requires admin authentication)
Route::middleware(['auth:web', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('employees', EmployeeController::class)
        ->except(['show']);

    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::get('/attendance/upload', [AttendanceController::class, 'uploadForm'])
        ->name('attendance.upload');

    Route::post('/attendance/preview', [AttendanceController::class, 'preview'])
        ->name('attendance.preview');

    Route::post('/attendance/store', [AttendanceController::class, 'storeFromPreview'])
        ->name('attendance.store');

    Route::get('/payroll', [PayrollController::class, 'index'])
        ->name('payroll.index');

    Route::get('/payroll/export', [PayrollController::class, 'exportPdf'])
        ->name('payroll.export');

    Route::post('/payroll/generate', [PayrollController::class, 'generate'])
        ->name('payroll.generate');

    Route::post('/payroll/save', [PayrollController::class, 'savePayroll'])
        ->name('payroll.save');

    Route::get('/payroll/saved', [PayrollController::class, 'savedPayrolls'])
        ->name('payroll.saved');

    Route::get('/payroll/saved/{payroll}', [PayrollController::class, 'viewSavedPayroll'])
        ->name('payroll.view-saved');

    Route::get('/payroll/saved/{payroll}/download', [PayrollController::class, 'downloadSavedPayroll'])
        ->name('payroll.download-saved');

    Route::get('/api/weather', [WeatherController::class, 'getWeather'])
        ->name('weather.get');
});

// Employee routes (requires employee authentication)
Route::middleware('auth:employee')->group(function () {
    Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])
        ->name('employee.dashboard');

    Route::get('/employee/profile', [ProfileController::class, 'index'])
        ->name('employee.profile');

    Route::post('/employee/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('employee.profile.password');

    Route::get('/employee/payrolls', [PayrollController::class, 'employeePayrolls'])
        ->name('employee.payrolls');

    Route::get('/employee/payrolls/{payroll}/download', [PayrollController::class, 'downloadSavedPayroll'])
        ->name('employee.payroll.download');

    // Leave management routes
    Route::get('/employee/leaves', [LeaveController::class, 'index'])
        ->name('employee.leaves.index');

    Route::get('/employee/leaves/create', [LeaveController::class, 'create'])
        ->name('employee.leaves.create');

    Route::post('/employee/leaves', [LeaveController::class, 'store'])
        ->name('employee.leaves.store');
});

// Common logout route (works for both admin and employee)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:web,employee')
    ->name('logout');

