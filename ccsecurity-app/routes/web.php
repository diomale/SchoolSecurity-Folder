<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\SuperAdminAuthController;

Route::get('/superadmin/login', [SuperAdminAuthController::class, 'showLogin'])
    ->name('superadmin.login');

Route::post('/superadmin/login', [SuperAdminAuthController::class, 'login'])
    ->name('superadmin.login.submit');

Route::post('/superadmin/logout', [SuperAdminAuthController::class, 'logout'])
    ->name('superadmin.logout');

Route::middleware('auth:superadmin')->group(function () {
    Route::get('/superadmin/dashboard', function () {
        return view('superadmin.dashboard');
    })->name('superadmin.dashboard');
});

use App\Http\Controllers\AdminController;

Route::get('/admin/login', [AdminController::class, 'showAdminLogin'])
    ->name('admin.login');
