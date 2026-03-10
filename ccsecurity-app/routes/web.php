<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InsideUserController;
use App\Http\Controllers\OutsideUserController;
use App\Http\Controllers\SecurityGuardController;

Route::get('/', function () {return view('welcome');})->name('welcome');

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
        Route::get('/admin/form', [SuperAdminAuthController::class, 'showAddForm'])->name('superadmin.admin.show.add.form');
        Route::post('/store/admin', [SuperAdminAuthController::class, 'storeAdmin'])->name('superadmin.storeAdmin');
        Route::get('/admin/{id}/details', [SuperAdminAuthController::class, 'showAdminDetails'])->name('superadmin.admin.show');
        Route::get('/admin/{id}/edit',[SuperAdminAuthController::class, 'viewEditForm' ])->name('superadmin.admin.edit');
        Route::delete('/admin/{id}', [SuperAdminAuthController::class, 'deleteAdmin'])->name('superadmin.admin.delete');
        Route::put('/admin/{id}/update', [SuperAdminAuthController::class, 'updateAdmin'])->name('superadmin.admin.update');
        
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
        Route::get('/profile', [AdminController::class, 'showProfile'])->name('admin.profile.show');

        //crud for security
        Route::get('/security/user/table',[AdminController::class, 'showSecurityUserCrud'])->name('security.user.table.section');
        Route::delete('/security/bulk-delete', [AdminController::class, 'bulkDeleteSecurityGuards'])->name('admin.security.bulk-delete');
        Route::get('/security/user/add-section', [AdminController::class, 'showAddSecurityGuardUser'])->name('security.user.add.section');
        Route::post('/security/store-user', [AdminController::class, 'storeSecurityGuard'])->name('security.add.accept');
        Route::get('/security/guard-user-details/{id}', [AdminController::class, 'showSecurityUserDetail'])->name('security.guard.user.details');
        Route::get('/security/guard-user-edit/{id}', [AdminController::class, 'viewSecurityUserForm'])->name('security.guard.user.edit');
        Route::put('/update/{id}/user', [AdminController::class, 'updateSecurityUser'])->name('security.guard.user.update');
        Route::delete('/security/guard-user/{id}/delete', [AdminController::class,'deleteSecurityUser'])->name('security.guard.user.delete');

        // Shift Management for Admin
        Route::get('/shift-management', [AdminController::class, 'showShiftManagement'])->name('admin.shift.management');
        Route::delete('/shifts/bulk-delete', [AdminController::class, 'bulkDeleteShifts'])->name('admin.shift.bulk-delete');
        Route::post('/assign-shift', [AdminController::class, 'assignShift'])->name('admin.assign.shift');
        Route::delete('/shift/{id}/delete', [AdminController::class, 'deleteShift'])->name('admin.shift.delete');
        Route::get('/security/{id}/shifts', [AdminController::class, 'showGuardShifts'])->name('admin.guard.shifts');

        
        //Create, Read, Update, Delete; for insider
        Route::get('/crud-section', [AdminController::class,'showCrudSection'])->name('admin.show.crudSection');
        Route::get('/add-form', [AdminController::class, 'showAddUserForm'])->name('admin.add.user');
        Route::post('/user-store',[AdminController::class,'storeUser'])->name('admin.add.user.accept');
        Route::get('/user/{id}/details', [AdminController::class, 'showUserDetail'])->name('admin.user.details');
        Route::get('/user/{id}/edit-form', [AdminController::class, 'viewEditForm'])->name('admin.user.edit.form');
        Route::delete('/user/{id}/delete', [AdminController::class,'deleteUser'])->name('admin.user.delete');
        Route::put('/update/{id}', [AdminController::class, 'updateUser'])->name('admin.update.user');

        //list
        Route::get('/outsider/waiting-list', [AdminController::class, 'ShowOutsiderList'])->name('show.admin.outsider.list');
        Route::get('/outsider/add', [AdminController::class, 'showAddOutsiderForm'])->name('admin.outsider.add');
        Route::post('/outsider/store', [AdminController::class, 'storeOutsider'])->name('admin.outsider.store');
        Route::get('/outsider/{id}/edit', [AdminController::class, 'editOutsider'])->name('admin.outsider.edit');
        Route::put('/outsider/{id}/update', [AdminController::class, 'updateOutsider'])->name('admin.outsider.update');
        Route::delete('/outsider/bulk-delete', [AdminController::class, 'bulkDeleteOutsiders'])->name('admin.outsider.bulk-delete');
        Route::delete('/outsider/{id}/delete', [AdminController::class, 'deleteOutsider'])->name('admin.outsider.delete');
        Route::patch('/outsider/approved/{id}', [AdminController::class, 'ApprovedOutsider'])->name('admin.approved.user');
        Route::patch('/outsider/rejected/{id}', [AdminController::class, 'RejectOutsider'])->name('admin.rejected.user');

        // Visit Requests Management
        Route::get('/visit-requests', [AdminController::class, 'showVisitRequests'])->name('admin.visit.requests');
        Route::patch('/visit-request-approve/{id}', [AdminController::class, 'approveVisitRequest'])->name('admin.visit.approve');
        Route::patch('/visit-request-reject/{id}', [AdminController::class, 'rejectVisitRequest'])->name('admin.visit.reject');

        //QR Status Management
        Route::get('/qr-status-management', [AdminController::class, 'showQrStatusManagement'])->name('admin.qr.status.management');
        Route::delete('/inside-user/bulk-delete', [AdminController::class, 'bulkDeleteInsideUsers'])->name('admin.inside-user.bulk-delete');
        Route::patch('/qr-status-toggle/{id}', [AdminController::class, 'toggleQrStatus'])->name('admin.qr.status.toggle');
        Route::post('/qr-status-bulk-toggle', [AdminController::class, 'bulkToggleQrStatus'])->name('admin.qr.status.bulk.toggle');

        // Auto-Delete Cleanup Management
        Route::get('/cleanup-settings', [AdminController::class, 'showCleanupSettings'])->name('admin.cleanup.settings');
        Route::post('/cleanup-settings/update-table', [AdminController::class, 'updateTableSettings'])->name('admin.cleanup.update-table');
        Route::post('/cleanup-settings/run-now', [AdminController::class, 'runCleanupNow'])->name('admin.cleanup.run-now');
        Route::post('/cleanup-settings/toggle-global', [AdminController::class, 'toggleGlobalAutoDelete'])->name('admin.cleanup.toggle-global');
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

//security guard user
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

        // Entry/Exit Logs routes
        Route::get('/entry-logs', [SecurityGuardController::class, 'viewEntryLogs'])->name('security.entry.logs');

        // Shift Management routes
        Route::get('/shift-management', [SecurityGuardController::class, 'showShiftManagement'])->name('security.shift.management');
        Route::post('/clock-in', [SecurityGuardController::class, 'clockIn'])->name('security.clock.in');
        Route::post('/clock-out', [SecurityGuardController::class, 'clockOut'])->name('security.clock.out');
        Route::post('/submit-handover', [SecurityGuardController::class, 'submitHandover'])->name('security.submit.handover');
        Route::get('/shift-schedule', [SecurityGuardController::class, 'showShiftSchedule'])->name('security.shift.schedule');
        Route::get('/shift-history', [SecurityGuardController::class, 'showShiftHistory'])->name('security.shift.history');

        // QR Status Management routes
        Route::get('/qr-status-management', [SecurityGuardController::class, 'showQrStatusManagement'])->name('security.qr.status.management');
        Route::patch('/qr-status-toggle/{id}', [SecurityGuardController::class, 'toggleQrStatus'])->name('security.qr.status.toggle');
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

        // Visit Request routes
        Route::get('/visit-request', [OutsideUserController::class, 'showVisitRequest'])->name('outsideuser.visit.request');
        Route::post('/visit-request', [OutsideUserController::class, 'submitVisitRequest'])->name('outsideuser.visit.submit');
        Route::get('/visit-history', [OutsideUserController::class, 'visitHistory'])->name('outsideuser.visit.history');
        Route::get('/reactivate-qr', [OutsideUserController::class, 'reactivateQR'])->name('outsideuser.reactivate.qr');

        // Profile routes
        Route::get('/profile', [OutsideUserController::class, 'showProfile'])->name('outsideuser.profile.show');
        Route::post('/profile/update', [OutsideUserController::class, 'updateProfile'])->name('outsideuser.profile.update');

        // Notification routes
        Route::get('/notifications', [OutsideUserController::class, 'notifications'])->name('outsideuser.notifications');
        Route::post('/notifications/{id}/read', [OutsideUserController::class, 'markNotificationAsRead'])->name('outsideuser.notifications.read');
        Route::post('/notifications/read-all', [OutsideUserController::class, 'markAllNotificationsAsRead'])->name('outsideuser.notifications.read-all');
    });

});