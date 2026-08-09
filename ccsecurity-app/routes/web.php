<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\InsideUserController;
use App\Http\Controllers\InsideUserEventController;
use App\Http\Controllers\OutsideUserController;
use App\Http\Controllers\SecurityGuardController;
use App\Http\Controllers\AdminEventPrivilegeController;
use App\Http\Controllers\AdminActivityController;

Route::get('/', function () {
    return redirect()->route('welcome.page');
});

Route::get('/welcome', function () {
    $publicEvents = \App\Models\Event::with(['insideUser'])
        ->withCount('registrations')
        ->where('show_on_welcome', true)
        ->where('status', \App\Models\Event::STATUS_APPROVED)
        ->where('event_date', '>=', now()->toDateString())
        ->orderBy('event_date', 'asc')
        ->paginate(9);

    return view('welcome', compact('publicEvents'));
})->name('welcome.page');

Route::get('/login', function () {
    return view('login-choice');
})->name('login.choice');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

// Public event registration routes (no login required)
Route::get('/event/register/{eventId}', [InsideUserEventController::class, 'showPublicRegistration'])->name('public.event.register');
Route::post('/event/register/{eventId}/submit', [InsideUserEventController::class, 'submitPublicRegistration'])->name('public.event.register.submit');

// --- SUPER ADMIN ROUTES --- //
Route::prefix('superadmin')->group(function () {


    Route::middleware(['redirect.auth', 'guest:superadmin'])->group(function () {
        Route::get('/login', [SuperAdminAuthController::class, 'showLogin'])->name('superadmin.login');
        Route::post('/login', [SuperAdminAuthController::class, 'login'])->name('superadmin.login.submit');

        // Device verification routes (not logged in yet)
        Route::get('/device-verification', [SuperAdminAuthController::class, 'showDeviceVerification'])->name('superadmin.device.verify.show');
        Route::post('/device-verification', [SuperAdminAuthController::class, 'verifyDevice'])->name('superadmin.device.verify.submit');
        Route::post('/device-verification/resend', [SuperAdminAuthController::class, 'resendVerificationCode'])->name('superadmin.device.verify.resend');
    });

    
    Route::middleware('auth:superadmin')->group(function () {
        
        
        Route::get('/dashboard', [SuperAdminAuthController::class, 'dashboard'])->name('superadmin.dashboard');
        Route::get('/logs', [SuperAdminAuthController::class, 'showLogs'])->name('superadmin.logs');
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


    Route::middleware(['redirect.auth', 'guest:admin'])->group(function(){
        Route::get('/login', [AdminController::class, 'showAdminLogin'])->name('admin.login');
        Route::post('/login',[AdminController::class, 'login'])->name('admin.login.submit');
    });

    // Admin Device Verification (no auth required - user verified credentials but needs device check)
    Route::get('/device-verification', [AdminController::class, 'showDeviceVerification'])->name('admin.device.verify.show');
    Route::post('/device-verification', [AdminController::class, 'verifyDevice'])->name('admin.device.verify.submit');
    Route::post('/device-verification/resend', [AdminController::class, 'resendVerificationCode'])->name('admin.device.verify.resend');
    
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
        Route::delete('/user/bulk-delete', [AdminController::class, 'bulkDeleteUsers'])->name('admin.user.bulk-delete');
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

        // Parent-Child Connection Management (View Only - No Admin Approval Needed)
        Route::get('/connection-requests', [AdminController::class, 'showConnectionRequests'])->name('admin.connection.requests');

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

        // Event Management routes for admin
        Route::get('/events/pending', [AdminEventController::class, 'pendingEvents'])->name('admin.events.pending');
        Route::get('/events/all', [AdminEventController::class, 'allEvents'])->name('admin.events.all');
        Route::get('/events/analytics', [AdminEventController::class, 'analytics'])->name('admin.events.analytics');
        Route::get('/events/{id}', [AdminEventController::class, 'show'])->name('admin.events.show');
        Route::post('/events/{id}/approve', [AdminEventController::class, 'approve'])->name('admin.events.approve');
        Route::post('/events/{id}/reject', [AdminEventController::class, 'reject'])->name('admin.events.reject');
        Route::post('/events/{id}/mark-completed', [AdminEventController::class, 'markCompleted'])->name('admin.events.mark-completed');
        Route::post('/events/bulk-approve', [AdminEventController::class, 'bulkApprove'])->name('admin.events.bulk-approve');
        Route::post('/events/bulk-reject', [AdminEventController::class, 'bulkReject'])->name('admin.events.bulk-reject');

        // Event Privilege Management
        Route::get('/event-privileges', [AdminEventPrivilegeController::class, 'index'])->name('admin.event-privileges.index');
        Route::post('/event-privileges/{id}/toggle', [AdminEventPrivilegeController::class, 'toggle'])->name('admin.event-privileges.toggle');
        Route::post('/event-privileges/bulk-toggle', [AdminEventPrivilegeController::class, 'bulkToggle'])->name('admin.event-privileges.bulk-toggle');

        // Activity Logs
        Route::get('/activity-logs', [AdminActivityController::class, 'index'])->name('admin.activity-logs.index');
        Route::post('/activity-logs/clear-old', [AdminActivityController::class, 'clearOld'])->name('admin.activity-logs.clear-old');
    });

    
});

//-- INSIDE USER --//

use App\Http\Controllers\InsideUserConnectionController;
use App\Http\Controllers\EventCreatorApprovalController;

Route::prefix('insideuser')->group(function(){


    Route::middleware(['redirect.auth', 'guest:insideuser'])->group(function(){
        Route::get('/login',[InsideUserController::class, 'showUserLogin'])->name('user.login.show');
        Route::post('/login',[InsideUserController::class, 'login'])->name('insideuser.login.submit');

        // Device verification routes (not logged in yet)
        Route::get('/device-verification', [InsideUserController::class, 'showDeviceVerification'])->name('insideuser.device.verify.show');
        Route::post('/device-verification', [InsideUserController::class, 'verifyDevice'])->name('insideuser.device.verify.submit');
        Route::post('/device-verification/resend', [InsideUserController::class, 'resendVerificationCode'])->name('insideuser.device.verify.resend');
    });

    Route::middleware('auth:insideuser')->group(function(){
        Route::get('/dashboard',[InsideUserController::class, 'dashboard'])->name('insideuser.dashboard');
        Route::post('/logout',[InsideUserController::class, 'logout'])->name('insideuser.logout');
        Route::post('/accept-terms', [InsideUserController::class, 'acceptTerms'])->name('insideuser.accept.terms');

        Route::get('/profile', [InsideUserController::class, 'userProfile'])->name('insideuser.profile.show');

        // Connection request routes for inside user
        Route::get('/connection-requests', [InsideUserConnectionController::class, 'connectionRequests'])->name('insideuser.connection.requests');
        Route::patch('/connection-accept/{id}', [InsideUserConnectionController::class, 'acceptConnection'])->name('insideuser.connection.accept');
        Route::patch('/connection-reject/{id}', [InsideUserConnectionController::class, 'rejectConnection'])->name('insideuser.connection.reject');
        Route::get('/connected-parents', [InsideUserConnectionController::class, 'connectedParents'])->name('insideuser.connected.parents');
        Route::delete('/connection/{id}/cancel', [InsideUserConnectionController::class, 'cancelConnection'])->name('insideuser.connection.cancel');

        // Event Management routes for inside user
        Route::get('/events', [InsideUserEventController::class, 'dashboard'])->name('insideuser.events.dashboard');
        Route::get('/events/create', [InsideUserEventController::class, 'create'])->name('insideuser.events.create');
        Route::post('/events/store', [InsideUserEventController::class, 'store'])->name('insideuser.events.store');
        Route::get('/events/{id}', [InsideUserEventController::class, 'show'])->name('insideuser.events.show');
        Route::get('/events/{id}/edit', [InsideUserEventController::class, 'edit'])->name('insideuser.events.edit');
        Route::put('/events/{id}/update', [InsideUserEventController::class, 'update'])->name('insideuser.events.update');
        Route::delete('/events/{id}/cancel', [InsideUserEventController::class, 'cancel'])->name('insideuser.events.cancel');
        Route::get('/events/{id}/registrations', [InsideUserEventController::class, 'registrations'])->name('insideuser.events.registrations');
        Route::get('/events/{id}/pending-approvals', [EventCreatorApprovalController::class, 'pendingApprovals'])->name('insideuser.events.pending-approvals');
        Route::post('/events/{id}/register-walkin', [InsideUserEventController::class, 'registerWalkin'])->name('insideuser.events.registerWalkin');
        Route::get('/events/registrations/{registrationId}/download-qr', [InsideUserEventController::class, 'downloadQR'])->name('insideuser.events.downloadQR');
        Route::get('/events/registrations/{registrationId}/resend-qr', [InsideUserEventController::class, 'resendQR'])->name('insideuser.events.resendQR');
        Route::get('/events/{id}/export-registrations', [InsideUserEventController::class, 'exportRegistrations'])->name('insideuser.events.exportRegistrations');
        Route::post('/events/{id}/toggle-welcome', [InsideUserEventController::class, 'toggleWelcomeVisibility'])->name('insideuser.events.toggle-welcome');

        // Event Creator Approval routes
        Route::prefix('events/approvals')->group(function() {
            Route::get('/{eventId}', [EventCreatorApprovalController::class, 'pendingApprovals'])->name('insideuser.events.approvals.pending');
            Route::post('/{registrationId}/approve', [EventCreatorApprovalController::class, 'approve'])->name('insideuser.events.approvals.approve');
            Route::post('/{registrationId}/reject', [EventCreatorApprovalController::class, 'reject'])->name('insideuser.events.approvals.reject');
            Route::post('/{eventId}/bulk-approve', [EventCreatorApprovalController::class, 'bulkApprove'])->name('insideuser.events.approvals.bulk-approve');
            Route::post('/{eventId}/bulk-reject', [EventCreatorApprovalController::class, 'bulkReject'])->name('insideuser.events.approvals.bulk-reject');
        });


    });
});

//security guard user
Route::prefix('securityguard')->group(function(){


    Route::middleware(['redirect.auth', 'guest:securityguard'])->group(function(){
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
        Route::patch('/qr-status-toggle/{id}/{type?}', [SecurityGuardController::class, 'toggleQrStatus'])->name('security.qr.status.toggle');

        // Walk-in User Management routes
        Route::get('/walkin-users', [SecurityGuardController::class, 'showWalkinUsers'])->name('security.walkin.list');
        Route::get('/walkin-user/add', [SecurityGuardController::class, 'showAddWalkinForm'])->name('security.walkin.add');
        Route::post('/walkin-user/store', [SecurityGuardController::class, 'storeWalkinUser'])->name('security.walkin.store');
        Route::delete('/walkin-users/bulk-delete', [SecurityGuardController::class, 'bulkDeleteWalkinUsers'])->name('security.walkin.bulk-delete');
        Route::get('/user-qr/{id}/{type?}', [SecurityGuardController::class, 'viewUserQr'])->name('security.user.qr');

        // Quick Pass (Temporary QR) routes
        Route::get('/quick-pass', [SecurityGuardController::class, 'showQuickPass'])->name('security.quick-pass.list');
        Route::get('/quick-pass/create', [SecurityGuardController::class, 'createQuickPass'])->name('security.quick-pass.create');
        Route::post('/quick-pass/store', [SecurityGuardController::class, 'storeQuickPass'])->name('security.quick-pass.store');
        Route::get('/quick-pass/{id}/qr', [SecurityGuardController::class, 'showQuickPassQr'])->name('security.quick-pass.qr');
        Route::delete('/quick-pass/{id}', [SecurityGuardController::class, 'deleteQuickPass'])->name('security.quick-pass.delete');

        // Event QR Scan route
        Route::get('/event/scan/{qr}', [SecurityGuardController::class, 'scanEventQR'])->name('security.event.scan');
    });
});

//-- OUTSIDE USER --/

use App\Http\Controllers\ParentConnectionController;

Route::prefix('outsideuser')->group(function(){


    Route::middleware(['redirect.auth', 'guest:outsideuser'])->group(function(){
        Route::get('/signup', [OutsideUserController::class, 'showSignup'])->name('outsideuser.signup.show');
        Route::get('/login', [OutsideUserController::class, 'ShowLogin'])->name('outsideuser.login.show');
        Route::post('/login', [OutsideUserController::class, 'Login'])->name('outsideuser.login.submit');
        Route::post('/request',[OutsideUserController::class, 'SignupRequest'])->name('outsideuser.signup.request');

        // Email verification routes
        Route::get('/verify/notice', [OutsideUserController::class, 'showVerifyNotice'])->name('outsideuser.verify.notice');
        Route::get('/verify/{token}', [OutsideUserController::class, 'verifyEmail'])->name('outsideuser.verify.email');
        Route::post('/verify/resend', [OutsideUserController::class, 'resendVerification'])->name('outsideuser.verify.resend');
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

        // Parent-Child Connection routes
        Route::get('/connections/request', [ParentConnectionController::class, 'showRequestForm'])->name('outsideuser.connections.request');
        Route::get('/connections/search', [ParentConnectionController::class, 'searchInsideUser'])->name('outsideuser.connections.search');
        Route::post('/connections/submit', [ParentConnectionController::class, 'submitConnectionRequest'])->name('outsideuser.connections.submit');
        Route::get('/connections/history', [ParentConnectionController::class, 'connectionHistory'])->name('outsideuser.connections.history');
        Route::delete('/connections/cancel/{id}', [ParentConnectionController::class, 'cancelConnection'])->name('outsideuser.connections.cancel');
        Route::delete('/connections/cancel-approved/{id}', [ParentConnectionController::class, 'cancelApprovedConnection'])->name('outsideuser.connections.cancel.approved');
    });

});