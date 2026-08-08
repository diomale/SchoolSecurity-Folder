<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\securityguard;
use App\Models\InsideUser;
use App\Models\OutsideUser;
use App\Models\Shift;
use App\Models\VisitRequest;
use App\Models\Notification;
use App\Models\CleanupSetting;
use App\Models\CleanupTableSetting;
use App\Models\EntryLog;
use App\Models\Event;
use App\Models\ShiftLog;
use App\Models\QuickPass;
use App\Models\ParentChildConnection;
use App\Rules\CurrentAdminPassword;
use App\Models\AdminDevice;
use App\Services\AdminActivityLogger;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeviceVerificationMail;
use Carbon\Carbon;


class AdminController extends Controller
{
    // =========================================================================
    // DASHBOARD & PROFILE
    // =========================================================================

    public function dashboard(){
        $totalInsideUsers = InsideUser::count();
        $totalGuards = securityguard::count();
        $totalOutsideUsers = OutsideUser::count();
        $totalEntryLogs = EntryLog::count();
        $totalEvents = \App\Models\Event::count();
        $pendingVisitRequests = VisitRequest::where('status', 'pending')->count();
        $pendingConnections = \App\Models\ParentChildConnection::where('status', 'pending')->count();
        $currentlyInside = \App\Models\CurrentlyInside::count();
        $activeGuards = securityguard::where('status', 1)->count();

        return view('admin.dashboard', compact(
            'totalInsideUsers',
            'totalGuards',
            'totalOutsideUsers',
            'totalEntryLogs',
            'totalEvents',
            'pendingVisitRequests',
            'pendingConnections',
            'currentlyInside',
            'activeGuards'
        ));
    }

    public function showProfile()
    {
        return view('Admin.user_profile');
    }


    // =========================================================================
    // OUTSIDE USER APPROVAL WORKFLOW
    // =========================================================================

    public function ShowOutsiderList(Request $request)
    {
        $query = OutsideUser::query();

        if ($request->filled('search')) {
            $search = $request->search;
            // Split search into words for better multi-word matching
            $searchWords = explode(' ', trim($search));
            
            $query->where(function($q) use ($searchWords, $search) {
                // Match full name as a whole string
                $q->where('fullname', 'LIKE', "%{$search}%")
                  // Also match individual words in first/last name
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone_number', 'LIKE', "%{$search}%")
                  ->orWhere('qr_value', 'LIKE', "%{$search}%");
                
                // If multiple words, also try matching each word separately
                if (count($searchWords) > 1) {
                    foreach ($searchWords as $word) {
                        $word = trim($word);
                        if (!empty($word)) {
                            $q->orWhere('first_name', 'LIKE', "%{$word}%")
                              ->orWhere('last_name', 'LIKE', "%{$word}%")
                              ->orWhere('fullname', 'LIKE', "%{$word}%");
                        }
                    }
                }
            });
        }

        $outside_users = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('Admin.AdminWaitingList.outside_user_list', compact('outside_users'));
    }

    public function bulkDeleteOutsiders(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:mysql_second.outside_user,id',
            'admin_password' => ['required', new CurrentAdminPassword]
        ]);

        OutsideUser::whereIn('id', $request->user_ids)->delete();

        return redirect()->back()->with('success', count($request->user_ids) . ' users deleted successfully!');
    }

    public function showAddOutsiderForm()
    {
        return view('Admin.AdminWaitingList.outside_user_add');
    }

    public function storeOutsider(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:mysql_second.outside_user,email',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        $qrValue = 'OUT-ADMIN-' . strtoupper(uniqid() . rand(1000, 9999));

        OutsideUser::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'fullname' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'qr_value' => $qrValue,
            'qr_status' => 'active',
            'qr_expires_at' => now()->addDay(),
            'status' => OutsideUser::STATUS_APPROVED,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AdminActivityLogger::userManagement('Created Outside User', "Created outside user: {$request->first_name} {$request->last_name}", [
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
        ]);

        return redirect()->route('show.admin.outsider.list')->with('success', 'Walk-in account created successfully! QR code will expire in 24 hours.');
    }

    public function editOutsider(Request $request, $id)
    {
        $outside_user = OutsideUser::findOrFail($id);
        $backUrl = $request->query('back_url', route('show.admin.outsider.list'));
        return view('Admin.AdminWaitingList.outside_user_edit', compact('outside_user', 'backUrl'));
    }

    public function updateOutsider(Request $request, $id)
    {
        $outside_user = OutsideUser::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:mysql_second.outside_user,email,' . $id,
            'phone_number' => 'nullable|string|max:20',
            'qr_status' => 'required|string',
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'fullname' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'qr_status' => $request->qr_status,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $outside_user->update($data);

        AdminActivityLogger::userManagement('Updated Outside User', "Updated outside user: {$outside_user->fullname}", [
            'user_id' => $outside_user->id,
            'name' => $outside_user->fullname,
        ]);

        return redirect()->route('show.admin.outsider.list')->with('success', 'User updated successfully!');
    }

    public function deleteOutsider($id)
    {
        $request = request();
        
        // Verify admin password
        $request->validate([
            'admin_password' => ['required', new CurrentAdminPassword]
        ]);
        
        $outside_user = OutsideUser::findOrFail($id);
        $outside_user->delete();

        AdminActivityLogger::userManagement('Deleted Outside User', "Deleted outside user: {$outside_user->fullname}", [
            'user_id' => $outside_user->id,
            'name' => $outside_user->fullname,
        ]);

        return redirect()->route('show.admin.outsider.list')->with('success', 'User deleted successfully!');
    }

    public function ApprovedOutsider($id)
    {
        $outside_user = OutsideUser::findOrFail($id);

        if ($outside_user->status === OutsideUser::STATUS_APPROVED) {
            return redirect()->back()->with('info', 'This user is already approved.');
        }

        $outside_user->update([
            'status' => OutsideUser::STATUS_APPROVED,
            'updated_at' => now(),
        ]);

        AdminActivityLogger::userManagement('Approved Outside User', "Approved outside user: {$outside_user->fullname}", [
            'user_id' => $outside_user->id,
            'name' => $outside_user->fullname,
        ]);

        return redirect()
            ->back()
            ->with('success', "User {$outside_user->first_name} approved successfully!");
    }

    public function RejectOutsider($id)
    {
        $outside_user = OutsideUser::findOrFail($id);

        if ($outside_user->status === OutsideUser::STATUS_REJECTED) {
            return redirect()->back()->with('info', 'This user is already rejected.');
        }

        $outside_user->update([
            'status' => OutsideUser::STATUS_REJECTED,
            'updated_at' => now(),
        ]);

        AdminActivityLogger::userManagement('Rejected Outside User', "Rejected outside user: {$outside_user->fullname}", [
            'user_id' => $outside_user->id,
            'name' => $outside_user->fullname,
        ]);

        return redirect()
            ->back()
            ->with('success', "User {$outside_user->first_name} rejected successfully!");
    }


    // =========================================================================
    // SECURITY GUARD MANAGEMENT (CRUD)
    // =========================================================================

    public function showSecurityUserCrud(Request $request)
    {
        $query = securityguard::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('fullname', 'LIKE', "%{$search}%");
            });
        }

        $security_guard_users = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('Admin.SecurityCrudSection.security_table_section', compact('security_guard_users'));
    }

    public function bulkDeleteSecurityGuards(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:mysql_second.security_guard_user,id',
            'admin_password' => ['required', new CurrentAdminPassword]
        ]);

        securityguard::whereIn('id', $request->user_ids)->delete();

        return redirect()->back()->with('success', count($request->user_ids) . ' security guards deleted successfully!');
    }

    public function showAddSecurityGuardUser()
    {
        return view('Admin.SecurityCrudSection.security_add_section');
    }

    public function storeSecurityGuard(Request $request)
    {
        $validate = $request->validate([
            'first_name'=>'required|string|max:150',
            'last_name'=>'required|string|max:150',
            'email'=>'required|string|max:150',
            'password'=>'required|string|min:8',
        ]);

        securityguard::create([
            'first_name' => $validate['first_name'],
            'last_name' => $validate['last_name'],
            'fullname' => $validate['first_name'] . ' ' . $validate['last_name'],
            'email' => $validate['email'],
            'password' => Hash::make($validate['password']),
            'created_at' => now(),
            'updated_at' => now(),
            'status' => 1,
        ]);

        AdminActivityLogger::userManagement('Created Security Guard', "Created security guard: {$validate['first_name']} {$validate['last_name']}", [
            'name' => $validate['first_name'] . ' ' . $validate['last_name'],
            'email' => $validate['email'],
        ]);

        return redirect()->route('security.user.table.section')
            ->with('success', 'New user created successfully!');
    }

    public function showSecurityUserDetail(Request $request, $id)
    {
        $security_guard_user = securityguard::findOrFail($id);
        $backUrl = $request->query('back_url', route('security.user.table.section'));
        return view('Admin.SecurityCrudSection.security_user_details', compact('security_guard_user', 'backUrl'));
    }

    public function viewSecurityUserForm(Request $request, $id)
    {
        $security_guard_user = securityguard::findOrFail($id);
        $backUrl = $request->query('back_url', route('security.user.table.section'));
        return view('Admin.SecurityCrudSection.security_user_edit_form', compact('security_guard_user', 'backUrl'));
    }

    public function updateSecurityUser(Request $request, $id)
    {
        $security_guard_user = securityguard::findOrFail($id);

        $request->validate([
            'first_name'=>'required|string|max:150',
            'last_name'=>'required|string|max:150',
            'email'=>'required|string|unique:mysql_second.inside_user,email,' . $id,
            'password' => 'nullable|string|min:8',
            'status' => 'required|boolean', 
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'status']);

        if ($request->filled('password')){
            $data['password'] = $request->password; 
        }

        $data['updated_at'] = now(); 

        $security_guard_user->update($data);

        AdminActivityLogger::userManagement('Updated Security Guard', "Updated security guard: {$security_guard_user->fullname}", [
            'user_id' => $security_guard_user->id,
            'name' => $security_guard_user->fullname,
        ]);

        return redirect()
        ->route('security.user.table.section')
        ->with('Success', 'Security Guard User updated successfully');
    }

    public function deleteSecurityUser($id)
    {
        $request = request();
        
        // Verify admin password
        $request->validate([
            'admin_password' => ['required', new CurrentAdminPassword]
        ]);
        
        $security_guard_user = securityguard::findOrFail($id);
        $security_guard_user->delete();

        AdminActivityLogger::userManagement('Deleted Security Guard', "Deleted security guard: {$security_guard_user->fullname}", [
            'user_id' => $security_guard_user->id,
            'name' => $security_guard_user->fullname,
        ]);

        return redirect()->route('security.user.table.section')->with('Success', 'Deleted Successfully');
    }


    // =========================================================================
    // QR CODE STATUS MANAGEMENT
    // =========================================================================

    public function showQrStatusManagement(Request $request)
    {
        $search = $request->search;
        
        $studentQuery = InsideUser::where('role', 'student');
        $staffQuery = InsideUser::where('role', 'staff');

        if ($request->filled('search')) {
            $filter = function($q) use ($search) {
                $q->where('fullname', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%")
                  ->orWhere('qr_value', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            };
            $studentQuery->where($filter);
            $staffQuery->where($filter);
        }
        
        $students = $studentQuery->orderBy('id', 'desc')->paginate(15, ['*'], 'students_page');
        $staff = $staffQuery->orderBy('id', 'desc')->paginate(15, ['*'], 'staff_page');
        
        return view('Admin.QrStatusManagement.qr_status_management', compact('students', 'staff'));
    }

    public function bulkDeleteInsideUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:mysql_second.inside_user,id',
            'admin_password' => ['required', new CurrentAdminPassword]
        ]);

        $ids = $request->user_ids;
        $blockedIds = $this->insideUserIdsWithRelatedRecords($ids);
        $deletableIds = array_values(array_diff(array_map('intval', $ids), $blockedIds));

        if (!empty($deletableIds)) {
            InsideUser::whereIn('id', $deletableIds)->delete();
        }

        AdminActivityLogger::userManagement('Bulk Deleted Inside Users', "Bulk deleted " . count($deletableIds) . " inside users", [
            'user_ids' => $deletableIds,
            'count' => count($deletableIds),
        ]);

        $message = count($deletableIds) . ' user(s) deleted successfully!';
        if (count($blockedIds) > 0) {
            $message .= ' ' . count($blockedIds) . ' skipped (they have scan history, events, or parent connections). Deactivate instead.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function toggleQrStatus($id)
    {
        $inside_user = InsideUser::findOrFail($id);
        
        $newStatus = in_array(strtolower($inside_user->qr_status), ['active']) ? 'inactive' : 'active';
        
        $inside_user->update([
            'qr_status' => $newStatus,
            'updated_at' => now(),
        ]);

        AdminActivityLogger::qrManagement('Toggled QR Status', "Toggled QR status for {$inside_user->fullname} to {$newStatus}", [
            'user_id' => $inside_user->id,
            'name' => $inside_user->fullname,
            'new_status' => $newStatus,
        ]);
        
        return redirect()->back()->with('success', "QR status for {$inside_user->fullname} changed to {$newStatus}!");
    }

    public function bulkToggleQrStatus(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:mysql_second.inside_user,id'
        ]);
        
        $newStatus = $request->new_status ?? 'inactive';
        
        InsideUser::whereIn('id', $request->user_ids)->update([
            'qr_status' => $newStatus,
            'updated_at' => now(),
        ]);

        AdminActivityLogger::qrManagement('Bulk Toggled QR Status', "Bulk toggled QR status for " . count($request->user_ids) . " users", [
            'user_ids' => $request->user_ids,
            'count' => count($request->user_ids),
            'new_status' => $newStatus,
        ]);
        
        return redirect()->back()->with('success', "QR status updated for " . count($request->user_ids) . " users!");
    }


    // =========================================================================
    // INSIDE USER MANAGEMENT (CRUD)
    // =========================================================================

    public function showCrudSection(Request $request)
    {
        $query = InsideUser::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('fullname', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('role', 'LIKE', "%{$search}%");
            });
        }

        $inside_users = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('Admin.AdminCrudSection.admin_crud', compact('inside_users'));
    }

    public function bulkDeleteUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:mysql_second.inside_user,id',
            'admin_password' => ['required', new CurrentAdminPassword]
        ]);

        $ids = $request->user_ids;
        $blockedIds = $this->insideUserIdsWithRelatedRecords($ids);
        $deletableIds = array_values(array_diff(array_map('intval', $ids), $blockedIds));

        if (!empty($deletableIds)) {
            InsideUser::whereIn('id', $deletableIds)->delete();
        }

        $message = count($deletableIds) . ' user(s) deleted successfully!';
        if (count($blockedIds) > 0) {
            $message .= ' ' . count($blockedIds) . ' skipped (they have scan history, events, or parent connections). Deactivate instead.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function showAddUserForm()
    {
        return view('Admin.AdminCrudSection.inside_user_add_form');
    }

    public function storeUser(Request $request)
    {
        $validate = $request->validate([
            'first_name' => 'required|string|max:150',
            'last_name'  => 'required|string|max:150',
            'email'      => 'required|email|max:150|unique:mysql_second.inside_user,email',
            'password'   => 'required|string|min:8',
            'role'       => 'required|string|max:250',
            'qr_status'  => 'required|string|max:250',
        ]);

        InsideUser::create([
            'first_name' => $validate['first_name'],
            'last_name'  => $validate['last_name'],
            'fullname'   => $validate['first_name'] . ' ' . $validate['last_name'],
            'email'      => $validate['email'],
            'role'       => $validate['role'],
            'password'   => $validate['password'], 
            'created_at' => now(),
            'updated_at' => now(),
            'status'     => 1,
            'qr_status'  => $validate['qr_status'],
        ]);

        AdminActivityLogger::userManagement('Created Inside User', "Created inside user: {$validate['first_name']} {$validate['last_name']}", [
            'user_id' => null,
            'name' => $validate['first_name'] . ' ' . $validate['last_name'],
            'email' => $validate['email'],
        ]);

        return redirect()->route('admin.show.crudSection')
            ->with('success', 'New user created successfully!');
    }

    public function showUserDetail(Request $request, $id)
    {
        $inside_user = InsideUser::findOrFail($id);
        $backUrl = $request->query('back_url', route('admin.show.crudSection'));
        return view('Admin.AdminCrudSection.admin_user_details', compact('inside_user', 'backUrl'));
    }

    public function viewEditForm(Request $request, $id)
    {
        $inside_user = InsideUser::findOrFail($id);
        $backUrl = $request->query('back_url', route('admin.show.crudSection'));

        return view('Admin.AdminCrudSection.admin_user_edit_form', compact('inside_user', 'backUrl'));
    }

    public function updateUser(Request $request, $id)
    {
        $inside_user = InsideUser::findOrFail($id);

        $request->validate([
            'first_name'=>'required|string|max:150',
            'last_name'=>'required|string|max:150',
            'email'=>'required|string|unique:mysql_second.inside_user,email,' . $id,
            'password' => 'nullable|string|min:8',
            'status' => 'required|boolean', 
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'status']);

        if ($request->filled('password')){
            $data['password'] = $request->password; 
        }

        $data['updated_at'] = now(); 

        $inside_user->update($data);

        AdminActivityLogger::userManagement('Updated Inside User', "Updated inside user: {$inside_user->fullname}", [
            'user_id' => $inside_user->id,
            'name' => $inside_user->fullname,
        ]);

        return redirect()
        ->route('admin.show.crudSection')
        ->with('Success', 'User updated successfully');
    }

    public function deleteUser($id)
    {
        $request = request();
        
        // Verify admin password
        $request->validate([
            'admin_password' => ['required', new CurrentAdminPassword]
        ]);
        
        $inside_user = InsideUser::findOrFail($id);

        if (!empty($this->insideUserIdsWithRelatedRecords([$inside_user->id]))) {
            return redirect()->route('admin.show.crudSection')->with('error', "Cannot delete {$inside_user->fullname}: they have scan history, events, or parent connections. Deactivate the user instead.");
        }

        $inside_user->delete();

        AdminActivityLogger::userManagement('Deleted Inside User', "Deleted inside user: {$inside_user->fullname}", [
            'user_id' => $inside_user->id,
            'name' => $inside_user->fullname,
        ]);

        return redirect()->route('admin.show.crudSection')->with('success', 'Deleted Successfully');
    }

    private function insideUserIdsWithRelatedRecords(array $ids): array
    {
        $ids = array_map('intval', $ids);
        $blocked = [];
        $blocked = array_merge($blocked, EntryLog::whereIn('inside_user_id', $ids)->pluck('inside_user_id')->all());
        $blocked = array_merge($blocked, Event::whereIn('inside_user_id', $ids)->pluck('inside_user_id')->all());
        $blocked = array_merge($blocked, ParentChildConnection::whereIn('inside_user_id', $ids)->pluck('inside_user_id')->all());
        return array_values(array_unique($blocked));
    }


    // =========================================================================
    // AUTHENTICATION (LOGIN/LOGOUT)
    // =========================================================================

    public function showAdminLogin()
    {
        return view('Admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Rate limiting: Check for too many failed attempts
        $email = $request->email;
        $rateLimitKey = 'admin_login_attempts_' . md5($email);
        $maxAttempts = 5;
        $lockoutMinutes = 15;

        $attempts = cache()->get($rateLimitKey, 0);
        if ($attempts >= $maxAttempts) {
            return back()->withErrors([
                'email' => 'Too many failed attempts. Please try again in ' . $lockoutMinutes . ' minutes.'
            ])->withInput($request->only('email'));
        }

        if (Auth::guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ])) {
            $user = Auth::guard('admin')->user();

            // Reset rate limit on successful login
            cache()->forget($rateLimitKey);

            // Generate device fingerprint
            $fingerprint = $this->generateDeviceFingerprint($request);

            // Check if device is trusted
            $trustedDevice = AdminDevice::where('admin_id', $user->id)
                ->where('device_fingerprint', $fingerprint)
                ->where('is_trusted', true)
                ->first();

            if (!$trustedDevice) {
                // New device - require verification
                Auth::guard('admin')->logout();

                // Generate verification code
                $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                // Store verification data in session
                $sessionData = [
                    'admin_device_verification_user_id' => $user->id,
                    'admin_device_verification_code' => $verificationCode,
                    'admin_device_verification_fingerprint' => $fingerprint,
                    'admin_device_verification_ip' => $request->ip(),
                    'admin_device_verification_ua' => $request->userAgent(),
                    'admin_device_verification_created_at' => now()->timestamp,
                ];

                foreach ($sessionData as $key => $value) {
                    session()->put($key, $value);
                }
                session()->save();

                // Get device info for email
                $deviceInfo = $this->getDeviceInfo($request);

                // Send verification email
                try {
                    Mail::to($user->email)->send(new DeviceVerificationMail(
                        $verificationCode,
                        $user->email,
                        $deviceInfo
                    ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send admin verification email: ' . $e->getMessage());
                }

                return redirect()->route('admin.device.verify.show');
            }

            // Trusted device - update last used
            $trustedDevice->update(['last_used_at' => now()]);

            return redirect()->route('admin.dashboard');
        }

        // Rate limiting on failed login
        cache()->put($rateLimitKey, $attempts + 1, now()->addMinutes($lockoutMinutes));

        AdminActivityLogger::auth('Login Failed', 'Failed login attempt for email: ' . $request->email, ['email' => $request->email]);

        return back()->withErrors([
            'email' => 'Invalid credentials'
        ])->withInput($request->only('email'));
    }

    public function logout()
    {
        $admin = Auth::guard('admin')->user();
        AdminActivityLogger::auth('Logout', 'Admin logged out: ' . ($admin->name ?? $admin->email ?? 'Unknown'));
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    // Device Verification
    public function showDeviceVerification()
    {
        $userId = session('admin_device_verification_user_id');

        if (!$userId) {
            return redirect()->route('admin.login');
        }

        $user = \App\Models\admin::find($userId);

        if (!$user) {
            return redirect()->route('admin.login');
        }

        return view('Admin.device-verification', compact('user'));
    }

    public function verifyDevice(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6',
        ]);

        $storedCode = session('admin_device_verification_code');
        $createdAt = session('admin_device_verification_created_at');

        // Check if code is expired (15 minutes)
        if ($createdAt && (now()->timestamp - $createdAt) > 900) {
            session()->forget([
                'admin_device_verification_user_id',
                'admin_device_verification_code',
                'admin_device_verification_fingerprint',
                'admin_device_verification_ip',
                'admin_device_verification_ua',
                'admin_device_verification_created_at',
            ]);

            return back()->withErrors([
                'verification_code' => 'Verification code has expired. Please login again.'
            ])->route('admin.login');
        }

        // Rate limiting for verification code attempts
        $rateLimitKey = 'admin_verify_attempts_' . md5(session('admin_device_verification_user_id', 'unknown'));
        $maxAttempts = 5;
        $lockoutMinutes = 15;

        $attempts = cache()->get($rateLimitKey, 0);
        if ($attempts >= $maxAttempts) {
            session()->forget([
                'admin_device_verification_user_id',
                'admin_device_verification_code',
                'admin_device_verification_fingerprint',
                'admin_device_verification_ip',
                'admin_device_verification_ua',
                'admin_device_verification_created_at',
            ]);

            return back()->withErrors([
                'verification_code' => 'Too many failed attempts. Please login again.'
            ])->route('admin.login');
        }

        // Verify code
        if ($request->verification_code !== $storedCode) {
            // Increment rate limit on failed verification
            cache()->put($rateLimitKey, $attempts + 1, now()->addMinutes($lockoutMinutes));

            return back()->withErrors([
                'verification_code' => 'Invalid verification code.'
            ]);
        }

        // Reset rate limit on successful verification
        cache()->forget($rateLimitKey);

        // Code is valid - trust this device
        $userId = session('admin_device_verification_user_id');
        $fingerprint = session('admin_device_verification_fingerprint');
        $ip = session('admin_device_verification_ip');
        $ua = session('admin_device_verification_ua');

        $deviceInfo = $this->parseUserAgent($ua);

        AdminDevice::updateOrCreate(
            [
                'admin_id' => $userId,
                'device_fingerprint' => $fingerprint,
            ],
            [
                'ip_address' => $ip,
                'user_agent' => $ua,
                'browser' => $deviceInfo['browser'],
                'os' => $deviceInfo['os'],
                'is_trusted' => true,
                'last_used_at' => now(),
            ]
        );

        // Clear verification session data
        session()->forget([
            'admin_device_verification_user_id',
            'admin_device_verification_code',
            'admin_device_verification_fingerprint',
            'admin_device_verification_ip',
            'admin_device_verification_ua',
            'admin_device_verification_created_at',
        ]);

        // Log the user in
        Auth::guard('admin')->login(\App\Models\admin::find($userId));

        $admin = \App\Models\admin::find($userId);
        AdminActivityLogger::auth('Login Successful', 'Admin logged in via device verification: ' . ($admin->name ?? $admin->email ?? 'Unknown'));

        return redirect()->route('admin.dashboard');
    }

    public function resendVerificationCode()
    {
        $userId = session('admin_device_verification_user_id');

        if (!$userId) {
            return redirect()->route('admin.login');
        }

        // Rate limiting for resend attempts
        $rateLimitKey = 'admin_resend_attempts_' . md5($userId);
        $maxAttempts = 3;
        $lockoutMinutes = 15;

        $attempts = cache()->get($rateLimitKey, 0);
        if ($attempts >= $maxAttempts) {
            return back()->withErrors([
                'verification_code' => 'Too many resend attempts. Please login again.'
            ])->route('admin.login');
        }

        // Increment resend rate limit
        cache()->put($rateLimitKey, $attempts + 1, now()->addMinutes($lockoutMinutes));

        $user = \App\Models\admin::find($userId);

        // Generate new verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update session
        session([
            'admin_device_verification_code' => $verificationCode,
            'admin_device_verification_created_at' => now(),
        ]);

        // Get device info
        $request = request();
        $deviceInfo = $this->getDeviceInfo($request);

        // Send verification email
        try {
            Mail::to($user->email)->send(new DeviceVerificationMail(
                $verificationCode,
                $user->email,
                $deviceInfo
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to resend admin verification email: ' . $e->getMessage());
        }

        return back()->with('success', 'Verification code has been resent to your email.');
    }

    private function generateDeviceFingerprint(Request $request)
    {
        $data = [
            $request->userAgent(),
            $request->ip(),
        ];

        return md5(implode('|', $data));
    }

    private function getDeviceInfo(Request $request)
    {
        $ua = $request->userAgent();
        $deviceInfo = $this->parseUserAgent($ua);

        return $deviceInfo['browser'] . ' on ' . $deviceInfo['os'];
    }

    private function parseUserAgent($ua)
    {
        $browser = 'Unknown Browser';
        $os = 'Unknown OS';

        if (str_contains($ua, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($ua, 'Edg')) {
            $browser = 'Edge';
        } elseif (str_contains($ua, 'Chrome')) {
            $browser = 'Chrome';
        } elseif (str_contains($ua, 'Safari')) {
            $browser = 'Safari';
        } elseif (str_contains($ua, 'Opera') || str_contains($ua, 'OPR')) {
            $browser = 'Opera';
        }

        if (str_contains($ua, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($ua, 'Mac')) {
            $os = 'macOS';
        } elseif (str_contains($ua, 'Linux')) {
            $os = 'Linux';
        } elseif (str_contains($ua, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) {
            $os = 'iOS';
        }

        return ['browser' => $browser, 'os' => $os];
    }


    // =========================================================================
    // SHIFT MANAGEMENT
    // =========================================================================

    public function showShiftManagement(Request $request)
    {
        $securityGuards = securityguard::where('status', 1)->get();
        $query = Shift::with('securityGuardUser');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('securityGuardUser', function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('fullname', 'LIKE', "%{$search}%");
            })->orWhere('shift_date', 'LIKE', "%{$search}%")
              ->orWhere('status', 'LIKE', "%{$search}%");
        } else {
            $query->where('shift_date', '>=', today());
        }

        $shifts = $query->orderBy('shift_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(20);

        return view('Admin.ShiftManagement.shift_management', compact('securityGuards', 'shifts'));
    }

    public function bulkDeleteShifts(Request $request)
    {
        $request->validate([
            'shift_ids' => 'required|array',
            'shift_ids.*' => 'exists:mysql_second.shifts,id'
        ]);

        Shift::whereIn('id', $request->shift_ids)->delete();

        return redirect()->back()->with('success', count($request->shift_ids) . ' shifts deleted successfully!');
    }

    public function assignShift(Request $request)
    {
        $request->validate([
            'security_guard_user_id' => ['required', function($attribute, $value, $fail) {
                $exists = DB::connection('mysql_second')
                    ->table('security_guard_user')
                    ->where('id', $value)
                    ->exists();
                if (!$exists) {
                    $fail('The selected security guard does not exist.');
                }
            }],
            'shift_date' => 'required_if:recurring_type,single|nullable|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'recurring_type' => 'required|in:single,recurring',
            'recurring_days' => 'required_if:recurring_type,recurring|array',
            'recurring_days.*' => 'required_if:recurring_type,recurring|integer|between:0,6',
            'recurring_end_date' => 'nullable|required_if:recurring_type,recurring|date|after_or_equal:shift_date',
        ]);

        if ($request->recurring_type === 'single') {
            Shift::create([
                'security_guard_user_id' => $request->security_guard_user_id,
                'shift_date' => $request->shift_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'scheduled',
            ]);
        } else {
            $startDate = $request->shift_date ? Carbon::parse($request->shift_date) : Carbon::today();
            $endDate = Carbon::parse($request->recurring_end_date);
            $recurringDays = $request->recurring_days; 
            
            $currentDate = clone $startDate;
            $shiftsCreated = 0;

            while ($currentDate <= $endDate) {
                if (in_array($currentDate->dayOfWeek, $recurringDays)) {
                    Shift::create([
                        'security_guard_user_id' => $request->security_guard_user_id,
                        'shift_date' => $currentDate->format('Y-m-d'),
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'status' => 'scheduled',
                    ]);
                    $shiftsCreated++;
                }
                $currentDate->addDay();
            }

            return redirect()->back()->with('success', "{$shiftsCreated} recurring shifts assigned successfully!");
        }

        return redirect()->back()->with('success', 'Shift assigned successfully!');
    }

    public function deleteShift($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return redirect()->back()->with('success', 'Shift deleted successfully!');
    }

    public function showGuardShifts(Request $request, $guardId)
    {
        $guard = securityguard::findOrFail($guardId);
        $shifts = Shift::where('security_guard_user_id', $guardId)
            ->orderBy('shift_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        $backUrl = $request->query('back_url', route('admin.shift.management'));

        return view('Admin.ShiftManagement.guard_shifts', compact('guard', 'shifts', 'backUrl'));
    }


    // =========================================================================
    // VISIT REQUESTS MANAGEMENT
    // =========================================================================

    public function showVisitRequests()
    {
        $visitRequests = VisitRequest::with('outsideUser')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('Admin.VisitRequests.visit_requests', compact('visitRequests'));
    }

    public function approveVisitRequest($id)
    {
        $visitRequest = VisitRequest::findOrFail($id);

        $visitRequest->update([
            'status' => 'approved',
            'admin_remarks' => 'Approved by admin',
        ]);

        $visitRequest->outsideUser->update([
            'qr_status' => 'active',
            'status' => OutsideUser::STATUS_APPROVED,
            'updated_at' => now(),
        ]);

        Notification::create([
            'outside_user_id' => $visitRequest->outside_user_id,
            'type' => 'visit_approved',
            'title' => 'Visit Request Approved',
            'message' => "Your visit request for {$visitRequest->visit_date->format('M d, Y')} at {$visitRequest->visit_time->format('h:i A')} has been approved. Your QR code is now active.",
            'related_type' => 'visit_request',
            'related_id' => $visitRequest->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AdminActivityLogger::visitRequest('Approved Visit Request', "Approved visit request from {$visitRequest->outsideUser->fullname}", [
            'visit_request_id' => $visitRequest->id,
            'name' => $visitRequest->outsideUser->fullname,
            'visit_date' => $visitRequest->visit_date,
        ]);

        return redirect()->back()->with('success', 'Visit request approved and QR code activated!');
    }

    public function rejectVisitRequest($id)
    {
        $visitRequest = VisitRequest::findOrFail($id);

        $visitRequest->update([
            'status' => 'rejected',
            'admin_remarks' => 'Request rejected by admin',
        ]);

        Notification::create([
            'outside_user_id' => $visitRequest->outside_user_id,
            'type' => 'visit_rejected',
            'title' => 'Visit Request Rejected',
            'message' => "Your visit request for {$visitRequest->visit_date->format('M d, Y')} at {$visitRequest->visit_time->format('h:i A')} has been rejected. Remarks: {$visitRequest->admin_remarks}",
            'related_type' => 'visit_request',
            'related_id' => $visitRequest->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AdminActivityLogger::visitRequest('Rejected Visit Request', "Rejected visit request from {$visitRequest->outsideUser->fullname}", [
            'visit_request_id' => $visitRequest->id,
            'name' => $visitRequest->outsideUser->fullname,
            'visit_date' => $visitRequest->visit_date,
        ]);

        return redirect()->back()->with('success', 'Visit request rejected.');
    }

    // =========================================================================
    // PARENT-CHILD CONNECTION MANAGEMENT
    // =========================================================================

    /**
     * Show all parent-child connection requests
     */
    public function showConnectionRequests()
    {
        $connectionRequests = ParentChildConnection::with(['outsideUser', 'insideUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $pendingCount = ParentChildConnection::where('status', ParentChildConnection::STATUS_PENDING)->count();
        $approvedCount = ParentChildConnection::where('status', ParentChildConnection::STATUS_APPROVED)->count();
        $rejectedCount = ParentChildConnection::where('status', ParentChildConnection::STATUS_REJECTED)->count();

        return view('Admin.ConnectionRequests.connection_requests', compact('connectionRequests', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Approve a parent-child connection request
     */
    public function approveConnectionRequest($id)
    {
        $connection = ParentChildConnection::findOrFail($id);

        // Check if inside user has accepted
        if (!$connection->isInsideUserAccepted()) {
            return redirect()->back()->with('error', 'Cannot approve: Student has not accepted this connection request yet.');
        }

        $connection->approve('Approved by admin');

        Notification::create([
            'outside_user_id' => $connection->outside_user_id,
            'type' => 'connection_approved',
            'title' => 'Child Connection Approved',
            'message' => "Your connection request with {$connection->insideUser->fullname} has been approved. You can now track their entry and exit at school.",
            'related_type' => 'parent_child_connection',
            'related_id' => $connection->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Connection request approved!');
    }

    /**
     * Reject a parent-child connection request
     */
    public function rejectConnectionRequest($id, Request $request)
    {
        $connection = ParentChildConnection::findOrFail($id);

        $remarks = $request->input('admin_remarks', 'Request rejected by admin');

        $connection->reject($remarks);

        Notification::create([
            'outside_user_id' => $connection->outside_user_id,
            'type' => 'connection_rejected',
            'title' => 'Child Connection Rejected',
            'message' => "Your connection request with {$connection->insideUser->fullname} has been rejected. Remarks: {$remarks}",
            'related_type' => 'parent_child_connection',
            'related_id' => $connection->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Connection request rejected.');
    }

    // =========================================================================
    // AUTO-DELETE CLEANUP MANAGEMENT
    // =========================================================================

    /**
     * Show cleanup settings page
     */
    public function showCleanupSettings()
    {
        $tableSettings = CleanupTableSetting::getAllSettings();

        // Get statistics for each table
        $stats = [
            'entry_logs' => [
                'total' => EntryLog::whereIn('scan_type', ['entry', 'exit'])->count(),
                'older_than_30_days' => EntryLog::whereIn('scan_type', ['entry', 'exit'])
                    ->where('scan_at', '<', Carbon::now()->subDays(30)->toDateTimeString())
                    ->count(),
            ],
            'visit_requests' => [
                'total' => VisitRequest::count(),
                'older_than_30_days' => VisitRequest::where('created_at', '<', Carbon::now()->subDays(30)->toDateTimeString())->count(),
            ],
            'notifications' => [
                'total' => Notification::count(),
                'older_than_30_days' => Notification::where('created_at', '<', Carbon::now()->subDays(30)->toDateTimeString())->count(),
            ],
            'shift_logs' => [
                'total' => ShiftLog::count(),
                'older_than_30_days' => ShiftLog::where('created_at', '<', Carbon::now()->subDays(30)->toDateTimeString())->count(),
            ],
            'shifts' => [
                'total' => Shift::count(),
                'older_than_30_days' => Shift::where('shift_date', '<', Carbon::now()->subDays(30)->toDateString())->count(),
            ],
            'quick_passes' => [
                'total' => QuickPass::count(),
                'older_than_30_days' => QuickPass::where('created_at', '<', Carbon::now()->subDays(30)->toDateTimeString())->count(),
            ],
        ];

        $globalSettings = CleanupSetting::getInstance();

        return view('Admin.CleanupSettings.cleanup_settings', compact('tableSettings', 'stats', 'globalSettings'));
    }

    /**
     * Update settings for a specific table (with password confirmation)
     */
    public function updateTableSettings(Request $request)
    {
        // Validate password first
        $admin = Auth::guard('admin')->user();
        if (!Hash::check($request->password, $admin->password)) {
            return redirect()->back()->with('error', 'Incorrect password. Settings not updated.');
        }

        $validated = $request->validate([
            'table_name' => 'required|string|in:entry_logs,visit_requests,notifications,shift_logs,shifts,quick_passes',
            'auto_delete_enabled' => 'required|boolean',
            'retention_days' => 'required|integer|min:0|max:365',
        ]);

        CleanupTableSetting::updateSettings(
            $validated['table_name'],
            $validated['auto_delete_enabled'],
            $validated['retention_days']
        );

        $tableName = CleanupTableSetting::TABLES[$validated['table_name']];
        $status = $validated['auto_delete_enabled'] ? 'enabled' : 'disabled';

        AdminActivityLogger::system('Updated Cleanup Settings', "Updated cleanup settings for {$validated['table_name']}", [
            'table_name' => $validated['table_name'],
            'auto_delete_enabled' => $validated['auto_delete_enabled'],
            'retention_days' => $validated['retention_days'],
        ]);
        
        return redirect()->back()->with('success', "{$tableName} cleanup settings updated! Auto-delete {$status}, Retention: {$validated['retention_days']} days.");
    }

    /**
     * Run cleanup manually for a specific table (with password confirmation)
     */
    public function runCleanupNow(Request $request)
    {
        // Validate password first
        $admin = Auth::guard('admin')->user();
        if (!Hash::check($request->password, $admin->password)) {
            return redirect()->back()->with('error', 'Incorrect password. Cleanup cancelled.');
        }

        $validated = $request->validate([
            'table_name' => 'required|string|in:entry_logs,visit_requests,notifications,shift_logs,shifts,quick_passes',
            'retention_days' => 'required|integer|min:0|max:365',
        ]);

        $tableName = $validated['table_name'];
        $days = $validated['retention_days'];
        $deletedCount = 0;

        if ($tableName === 'entry_logs') {
            if (!CleanupTableSetting::isAutoDeleteEnabled('entry_logs')) {
                return redirect()->back()->with('error', 'Auto-delete is disabled for entry_logs. Enable it first.');
            }

            $cutoffDate = Carbon::now()->subDays($days);
            // Uses idx_scan_at index - 60-100x faster than STR_TO_DATE
            $deletedCount = EntryLog::where('scan_at', '<=', $cutoffDate)->count();
            EntryLog::where('scan_at', '<=', $cutoffDate)->delete();

            CleanupTableSetting::getForTable('entry_logs')->updateLastCleanupDate();
            
        } elseif ($tableName === 'visit_requests') {
            if (!CleanupTableSetting::isAutoDeleteEnabled('visit_requests')) {
                return redirect()->back()->with('error', 'Auto-delete is disabled for visit_requests. Enable it first.');
            }
            
            $cutoffDate = Carbon::now()->subDays($days);
            $deletedCount = VisitRequest::where('created_at', '<=', $cutoffDate->toDateTimeString())->count();
            VisitRequest::where('created_at', '<=', $cutoffDate->toDateTimeString())->delete();
            
            CleanupTableSetting::getForTable('visit_requests')->updateLastCleanupDate();
            
        } elseif ($tableName === 'notifications') {
            if (!CleanupTableSetting::isAutoDeleteEnabled('notifications')) {
                return redirect()->back()->with('error', 'Auto-delete is disabled for notifications. Enable it first.');
            }
            
            $cutoffDate = Carbon::now()->subDays($days);
            $deletedCount = Notification::where('created_at', '<=', $cutoffDate->toDateTimeString())->count();
            Notification::where('created_at', '<=', $cutoffDate->toDateTimeString())->delete();
            
            CleanupTableSetting::getForTable('notifications')->updateLastCleanupDate();
            
        } elseif ($tableName === 'shift_logs') {
            if (!CleanupTableSetting::isAutoDeleteEnabled('shift_logs')) {
                return redirect()->back()->with('error', 'Auto-delete is disabled for shift_logs. Enable it first.');
            }
            
            $cutoffDate = Carbon::now()->subDays($days);
            $deletedCount = ShiftLog::where('created_at', '<=', $cutoffDate->toDateTimeString())->count();
            ShiftLog::where('created_at', '<=', $cutoffDate->toDateTimeString())->delete();
            
            CleanupTableSetting::getForTable('shift_logs')->updateLastCleanupDate();
            
        } elseif ($tableName === 'shifts') {
            if (!CleanupTableSetting::isAutoDeleteEnabled('shifts')) {
                return redirect()->back()->with('error', 'Auto-delete is disabled for shifts. Enable it first.');
            }

            $cutoffDate = Carbon::now()->subDays($days);
            $deletedCount = Shift::where('shift_date', '<=', $cutoffDate->toDateString())->count();
            Shift::where('shift_date', '<=', $cutoffDate->toDateString())->delete();

            CleanupTableSetting::getForTable('shifts')->updateLastCleanupDate();

        } elseif ($tableName === 'quick_passes') {
            if (!CleanupTableSetting::isAutoDeleteEnabled('quick_passes')) {
                return redirect()->back()->with('error', 'Auto-delete is disabled for quick_passes. Enable it first.');
            }

            $cutoffDate = Carbon::now()->subDays($days);
            $deletedCount = QuickPass::where('created_at', '<=', $cutoffDate->toDateTimeString())->count();
            QuickPass::where('created_at', '<=', $cutoffDate->toDateTimeString())->delete();

            CleanupTableSetting::getForTable('quick_passes')->updateLastCleanupDate();
        }

        $tableLabel = CleanupTableSetting::TABLES[$tableName];
        $message = "Cleanup completed! {$deletedCount} records deleted from {$tableLabel} (Retention: {$days} days).";

        AdminActivityLogger::system('Ran Manual Cleanup', "Ran manual cleanup for {$tableName}", [
            'table_name' => $tableName,
            'deleted_count' => $deletedCount,
            'retention_days' => $days,
        ]);

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle global auto-delete (with password confirmation)
     */
    public function toggleGlobalAutoDelete(Request $request)
    {
        // Validate password first
        $admin = Auth::guard('admin')->user();
        if (!Hash::check($request->password, $admin->password)) {
            return redirect()->back()->with('error', 'Incorrect password. Action cancelled.');
        }

        $newStatus = CleanupSetting::toggleAutoDelete();
        $statusText = $newStatus ? 'enabled' : 'disabled';

        AdminActivityLogger::system('Toggled Global Auto-Delete', "Toggled global auto-delete to {$statusText}", [
            'new_status' => $newStatus,
        ]);
        
        return redirect()->back()->with('success', "Global auto-delete has been {$statusText}.");
    }
}  