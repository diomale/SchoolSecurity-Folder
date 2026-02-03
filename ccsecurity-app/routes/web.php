<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\AdminController;


Route::get('/', function () {
    return view('welcome');
});

// --- SUPER ADMIN ROUTES --- //
Route::prefix('superadmin')->group(function () {

    
    Route::middleware('guest:superadmin')->group(function () {
        Route::get('/login', [SuperAdminAuthController::class, 'showLogin'])->name('superadmin.login');
        Route::post('/login', [SuperAdminAuthController::class, 'login'])->name('superadmin.login.submit');
    });

    
    Route::middleware('auth:superadmin')->group(function () {
        
        
        Route::get('/dashboard', [SuperAdminAuthController::class, 'dashboard'])->name('superadmin.dashboard');
        
        Route::post('/store-admin', [SuperAdminAuthController::class, 'storeAdmin'])->name('superadmin.storeAdmin');
        Route::get('/admin/{id}', [SuperAdminAuthController::class, 'showTableAdmin'])->name('superadmin.admin.show');
        
        Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('superadmin.logout');
    });
});

// --- ADMIN ROUTES --- //
Route::prefix('admin')->group(function () {


    Route::middleware('guest:admin')->group(function(){
        Route::get('/login', [AdminController::class, 'showAdminLogin'])->name('admin.login');
        Route::post('/login',[AdminController::class, 'login'])->name('admin.login.submit');
    });
    
    Route::middleware('auth:admin')->group(function(){
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    });

    
});