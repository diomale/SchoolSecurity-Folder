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
        Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('superadmin.logout');

        
        //Create, Read, Update, Delete,
        Route::get('/admin-form', [SuperAdminAuthController::class, 'showAddForm'])->name('superadmin.admin.show.add.form');
        Route::post('/store-admin', [SuperAdminAuthController::class, 'storeAdmin'])->name('superadmin.storeAdmin');
        Route::get('/admin-{id}-details', [SuperAdminAuthController::class, 'showAdminDetails'])->name('superadmin.admin.show');
        Route::get('/admin-{id}-edit',[SuperAdminAuthController::class, 'viewEditForm' ])->name('superadmin.admin.edit');
        Route::delete('/admin-{id}', [SuperAdminAuthController::class, 'deleteAdmin'])->name('superadmin.admin.delete');
        Route::put('/admin-{id}-update', [SuperAdminAuthController::class, 'updateAdmin'])->name('superadmin.admin.update');
        
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


        //Create, Read, Update, Delete,
        Route::get('/crud-section', [AdminController::class,'showCrudSection'])->name('admin.show.crudSection');
        Route::get('/add-form', [AdminController::class, 'showAddUserForm'])->name('admin.add.user');
        Route::post('/user-store',[AdminController::class,'storeUser'])->name('admin.add.user.accept');
        Route::get('/user-{id}-details', [AdminController::class, 'showUserDetail'])->name('admin.user.details');
        Route::get('/user-{id}-edit-form', [AdminController::class, 'viewEditForm'])->name('admin.user.edit.form');
        Route::delete('/user-{id}-delete', [AdminController::class,'deleteUser'])->name('admin.user.delete');
        Route::put('/update-{id}', [AdminController::class, 'updateUser'])->name('admin.update.user');
    });

    
});