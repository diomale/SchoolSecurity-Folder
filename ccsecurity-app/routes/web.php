<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InsideUserController;
use App\Http\Controllers\OutsideUserController;
use App\Http\Controllers\SecurityGuardController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

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

        //crud for security
        Route::get('/security-user-table',[AdminController::class, 'showSecurityUserCrud'])->name('security.user.table.section');
        Route::get('/security-user-add-section', [AdminController::class, 'showAddSecurityGuardUser'])->name('security.user.add.section');
        Route::post('/security-store-user', [AdminController::class, 'storeSecurityGuard'])->name('security.add.accept');

        //Create, Read, Update, Delete, for insider
        Route::get('/crud-section', [AdminController::class,'showCrudSection'])->name('admin.show.crudSection');
        Route::get('/add-form', [AdminController::class, 'showAddUserForm'])->name('admin.add.user');
        Route::post('/user-store',[AdminController::class,'storeUser'])->name('admin.add.user.accept');
        Route::get('/user-{id}-details', [AdminController::class, 'showUserDetail'])->name('admin.user.details');
        Route::get('/user-{id}-edit-form', [AdminController::class, 'viewEditForm'])->name('admin.user.edit.form');
        Route::delete('/user-{id}-delete', [AdminController::class,'deleteUser'])->name('admin.user.delete');
        Route::put('/update-{id}', [AdminController::class, 'updateUser'])->name('admin.update.user');

        //list
        Route::get('/outsider-waiting-list', [AdminController::class, 'ShowOutsiderList'])->name('show.admin.outsider.list');
        Route::patch('/outsider-approved/{id}', [AdminController::class, 'ApprovedOutsider'])->name('admin.approved.user');
        Route::patch('/outsider-rejected/{id}', [AdminController::class, 'RejectOutsider'])->name('admin.rejected.user');
    });

    
});

//-- INSIDE USER --//

Route::prefix('insideuser')->group(function(){


    Route::middleware('guest:insideuser')->group(function(){
        Route::get('/login',[InsideUserController::class, 'showUserLogin'])->name('user.login.show');
        Route::post('/login',[InsideUserController::class, 'login'])->name('insideuser.login.submit');
        
    });

    Route::middleware('auth:insideuser')->group(function(){
        Route::get('/dashboard',[InsideUserController::class, 'dashboard'])->name('insideuser.dashboard');
        Route::post('/logout',[InsideUserController::class, 'logout'])->name('insideuser.logout');

        Route::get('/profile', [InsideUserController::class, 'userProfile'])->name('insideuser.profile.show');


    });
});

//securityguard
Route::prefix('securityguard')->group(function(){


    Route::middleware('guest:securityguard')->group(function(){
        Route::get('/login', [SecurityGuardController::class, 'showLogin'])->name('security.login.show');
        Route::post('/login', [SecurityGuardController::class, 'login'])->name('security.login.submit');

    });

    Route::middleware('auth:securityguard')->group(function(){
        Route::get('/dashboard', [SecurityGuardController::class, 'dashboard'])->name('security.dashboard');
        Route::post('/logout', [SecurityGuardController::class, 'logout'])->name('security.logout');

        // QR Scanner routes
        Route::get('/scanner', [SecurityGuardController::class, 'showScanner'])->name('security.scanner.show');
        Route::post('/scan-qr', [SecurityGuardController::class, 'scanQR'])->name('security.scan.qr');
        Route::get('/scan-history', [SecurityGuardController::class, 'scanHistory'])->name('security.scan.history');

    });
});

//-- OUTSIDE USER --/

Route::prefix('outsideuser')->group(function(){


    Route::middleware('guest:outsideuser')->group(function(){
        Route::get('/signup', [OutsideUserController::class, 'showSignup'])->name('outsideuser.signup.show');
        Route::get('/login', [OutsideUserController::class, 'ShowLogin'])->name('outsideuser.login.show');
        Route::post('/login', [OutsideUserController::class, 'Login'])->name('outsideuser.login.submit');
        Route::post('/request',[OutsideUserController::class, 'SignupRequest'])->name('outsideuser.signup.request');
    });

    Route::middleware('auth:outsideuser')->group(function(){
        Route::get('/dashboard', [OutsideUserController::class, 'dashboard'])->name('outsider.dashboard');
        Route::post('/logout', [OutsideUserController::class, 'logout'])->name('outsideuser.logout');
    });

});