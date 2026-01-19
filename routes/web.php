<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.perform');
});

Route::middleware('auth')->group(function () {
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

    Route::get('/api/weather', [WeatherController::class, 'getWeather'])
        ->name('weather.get');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

