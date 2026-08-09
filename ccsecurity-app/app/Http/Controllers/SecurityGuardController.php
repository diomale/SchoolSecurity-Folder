<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\OutsideUser;
use App\Models\EntryLog;
use App\Models\InsideUser;
use App\Models\securityguard;
use App\Models\Shift;
use App\Models\ShiftLog;
use App\Models\QuickPass;
use App\Models\EventRegistration;
use App\Models\Event;
use App\Models\CurrentlyInside;
use Carbon\Carbon;

class SecurityGuardController extends Controller
{
    //show
    public function dashboard()
    {
        $guard = Auth::guard('securityguard')->user();
        
        // Cache dashboard statistics for 5 minutes to reduce database load
        // Priority 4: Query caching optimization
        $cacheKey = 'guard_dashboard_' . $guard->id . '_' . today()->toDateString();
        
        $stats = Cache::remember($cacheKey, 300, function () use ($guard) {
            return [
                // Get recent QR status change activities from ALL guards (shared notifications)
                'recentActivities' => EntryLog::with(['insideUser', 'outsideUser', 'securityGuardUser', 'eventRegistration'])
                    ->whereNotNull('security_guard_user_id')
                    ->orderBy('id', 'desc')
                    ->limit(20)
                    ->get(),
                
                // Get statistics for current guard (only count actual entry/exit scans, not QR status toggles)
                'totalScans' => EntryLog::where('security_guard_user_id', $guard->id)
                    ->whereIn('scan_type', ['entry', 'exit'])
                    ->count(),
                'todayScans' => EntryLog::where('security_guard_user_id', $guard->id)
                    ->whereIn('scan_type', ['entry', 'exit'])
                    ->whereDate('scan_at', today())
                    ->count(),
                'todayEntries' => EntryLog::where('security_guard_user_id', $guard->id)
                    ->where('scan_type', 'entry')
                    ->whereDate('scan_at', today())
                    ->count(),
                'todayExits' => EntryLog::where('security_guard_user_id', $guard->id)
                    ->where('scan_type', 'exit')
                    ->whereDate('scan_at', today())
                    ->count(),
                
                // Get total guards count
                'totalGuards' => securityguard::count(),
            ];
        });
        
        // Extract cached stats
        $recentActivities = $stats['recentActivities'];
        $totalScans = $stats['totalScans'];
        $todayScans = $stats['todayScans'];
        $todayEntries = $stats['todayEntries'];
        $todayExits = $stats['todayExits'];
        $totalGuards = $stats['totalGuards'];

        return view('SecurityGuardUser.dashboard', compact(
            'guard',
            'recentActivities',
            'totalScans',
            'todayScans',
            'todayEntries',
            'todayExits',
            'totalGuards'
        ));
    }

    public function showLogin()
    {
        return view('SecurityGuardUser.login');
    }

    public function showScanner()
    {
        return view('SecurityGuardUser.Scanner.scanner');
    }

    //QR Status Management for Security Guard
    public function showQrStatusManagement(Request $request)
    {
        $search = $request->search;

        // Separate staff and student queries
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
        
        $students = $studentQuery->orderBy('id', 'desc')->paginate(10, ['*'], 'students_page');
        $staff = $staffQuery->orderBy('id', 'desc')->paginate(10, ['*'], 'staff_page');

        // Search for outside users (visitors)
        $outsideQuery = OutsideUser::query();
        if ($request->filled('search')) {
            $outsideQuery->where(function($q) use ($search) {
                $q->where('fullname', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%")
                  ->orWhere('qr_value', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone_number', 'LIKE', "%{$search}%");
            });
        }
        $outside_users = $outsideQuery->orderBy('id', 'desc')->paginate(10, ['*'], 'visitors_page');

        return view('SecurityGuardUser.QrStatusManagement.qr_status_management', compact('students', 'staff', 'outside_users'));
    }

    // Walk-in User Management for Security Guards
    public function showWalkinUsers(Request $request)
    {
        $query = OutsideUser::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone_number', 'LIKE', "%{$search}%")
                  ->orWhere('qr_value', 'LIKE', "%{$search}%");
            });
        }

        $outside_users = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('SecurityGuardUser.WalkinUsers.walkin_user_list', compact('outside_users'));
    }

    public function showAddWalkinForm()
    {
        return view('SecurityGuardUser.WalkinUsers.walkin_user_add');
    }

    public function storeWalkinUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:mysql_second.outside_user,email',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'purpose_of_visit' => 'required|string|max:255',
        ]);

        $qrValue = 'OUT-GUARD-' . strtoupper(uniqid() . rand(1000, 9999));

        OutsideUser::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'fullname' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'purpose_of_visit' => $request->purpose_of_visit,
            'qr_value' => $qrValue,
            'qr_status' => 'active',
            'qr_expires_at' => now()->addDay(),
            'status' => OutsideUser::STATUS_APPROVED,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('security.walkin.list')->with('success', 'Walk-in account created successfully! QR code will expire in 24 hours.');
    }

    public function bulkDeleteWalkinUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:mysql_second.outside_user,id',
            'admin_password' => ['required', new \App\Rules\CurrentAdminPassword('securityguard')]
        ]);

        OutsideUser::whereIn('id', $request->user_ids)->delete();

        return redirect()->back()->with('success', count($request->user_ids) . ' visitors deleted successfully!');
    }

    public function viewUserQr($id, $type = 'outside')
    {
        if ($type === 'inside') {
            $user = InsideUser::findOrFail($id);
        } else {
            $user = OutsideUser::findOrFail($id);
        }

        return view('SecurityGuardUser.WalkinUsers.view_qr', compact('user', 'type'));
    }

    public function toggleQrStatus($id, $type = null)
    {
        $guard = Auth::guard('securityguard')->user();

        // Use the type parameter if provided, otherwise determine by table lookup
        if ($type === 'outside') {
            // Directly look up outside user
            $outside_user = OutsideUser::findOrFail($id);
            $newStatus = in_array(strtolower($outside_user->qr_status), ['active']) ? 'inactive' : 'active';

            $outside_user->update([
                'qr_status' => $newStatus,
                'updated_at' => now(),
            ]);

            // Create notification/activity log for other guards (QR status toggle)
            EntryLog::create([
                'inside_user_id' => null,
                'outside_user_id' => $outside_user->id,
                'security_guard_user_id' => $guard->id,
                'scan_at' => now()->toDateTimeString(),
                'scan_type' => 'qr_' . $newStatus,
            ]);

            return redirect()->back()->with('success', "QR status for visitor {$outside_user->fullname} changed to {$newStatus}!");
        } else {
            // Default to inside user (students/staff)
            $inside_user = InsideUser::findOrFail($id);
            $newStatus = in_array(strtolower($inside_user->qr_status), ['active']) ? 'inactive' : 'active';

            $inside_user->update([
                'qr_status' => $newStatus,
                'updated_at' => now(),
            ]);

            // Create notification/activity log for other guards (QR status toggle)
            EntryLog::create([
                'inside_user_id' => $inside_user->id,
                'outside_user_id' => null,
                'security_guard_user_id' => $guard->id,
                'scan_at' => now()->toDateTimeString(),
                'scan_type' => 'qr_' . $newStatus,
            ]);

            return redirect()->back()->with('success', "QR status for {$inside_user->fullname} changed to {$newStatus}!");
        }
    }

    //function
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Rate limiting: Check for too many failed attempts
        $email = $request->email;
        $rateLimitKey = 'securityguard_login_attempts_' . md5($email);
        $maxAttempts = 5;
        $lockoutMinutes = 15;

        $attempts = cache()->get($rateLimitKey, 0);
        if ($attempts >= $maxAttempts) {
            return back()->withErrors([
                'email' => 'Too many failed attempts. Please try again in ' . $lockoutMinutes . ' minutes.'
            ])->withInput($request->only('email'));
        }

        // Add 'status' => 1 to ensure only active guards can log in
        if (Auth::guard('securityguard')->attempt(array_merge($credentials, ['status' => 1]))) {

            // Reset rate limit on successful login
            cache()->forget($rateLimitKey);

            // IMPORTANT: Regenerate session to prevent session fixation
            $request->session()->regenerate();

            // Only honor the intended URL if it's within the securityguard routes;
            // otherwise (e.g. a different guard's portal) go to the guard dashboard.
            $intended = $request->session()->get('url.intended');
            $path = $intended ? parse_url($intended, PHP_URL_PATH) : null;
            if (!$path || !str_starts_with($path, '/securityguard')) {
                $request->session()->forget('url.intended');
                return redirect()->route('security.dashboard');
            }

            return redirect()->intended(route('security.dashboard'));
        }

        // Increment rate limit on failed login
        cache()->put($rateLimitKey, $attempts + 1, now()->addMinutes($lockoutMinutes));

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('securityguard')->logout();

        // IMPORTANT: Clear the session data and CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('security.login.show');
    }

    /**
     * Scan QR code and log entry/exit
     */
    public function scanQR(Request $request)
    {
        try {
            $request->validate([
                'qr_value' => 'required|string'
            ]);

            $qrValue = $request->qr_value;
            $guardId = Auth::guard('securityguard')->id();

            if (!$guardId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Guard not logged in'
                ], 401);
            }

            // Try to find inside user first
            $insideUser = InsideUser::where('qr_value', $qrValue)->first();
            $outsideUser = null;
            $quickPass = null;
            $eventRegistration = null;
            $userType = 'inside';
            $user = null;

            \Log::info('QR Scan attempt: ' . $qrValue);

            // If not found, try outside user
            if (!$insideUser) {
                $outsideUser = OutsideUser::where('qr_value', $qrValue)->first();
                if ($outsideUser) {
                    $userType = 'outside';
                    $user = $outsideUser;
                } else {
                    // Try quick pass
                    $quickPass = QuickPass::where('qr_value', $qrValue)->first();
                    if ($quickPass) {
                        $userType = 'quick_pass';
                        $user = $quickPass;
                    } else {
                        // Try event registration (QR codes start with EVT)
                        \Log::info('Checking for event registration, starts with EVT: ' . (strpos($qrValue, 'EVT') === 0 ? 'YES' : 'NO'));
                        if (strpos($qrValue, 'EVT') === 0) {
                            $eventRegistration = EventRegistration::where('qr_code', $qrValue)->first();
                            \Log::info('Event registration found: ' . ($eventRegistration ? 'YES' : 'NO'));
                            if ($eventRegistration) {
                                $userType = 'event';
                                $user = $eventRegistration;
                            }
                        }
                    }
                }
            } else {
                $user = $insideUser;
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code not recognized. User not found.'
                ]);
            }

            // Get user name helper
            $getUserName = function($u) {
                if (!$u) return 'Unknown User';
                if ($u instanceof QuickPass) {
                    return $u->visitor_name . ' (Quick Pass)';
                }
                return $u->fullname ?: trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: 'User ' . $u->id;
            };

            // Handle Quick Pass scanning
            if ($userType === 'quick_pass') {
                // Check if quick pass is expired
                if ($quickPass->isExpired()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quick Pass expired at ' . $quickPass->expires_at->format('M d, h:i A'),
                        'user_type' => $userType,
                        'inside_user' => [
                            'id' => $quickPass->id,
                            'fullname' => $quickPass->visitor_name . ' (Quick Pass)',
                            'qr_value' => $quickPass->qr_value,
                        ],
                        'quick_pass' => [
                            'id' => $quickPass->id,
                            'visitor_name' => $quickPass->visitor_name,
                            'vehicle_plate' => $quickPass->vehicle_plate,
                            'purpose' => $quickPass->purpose,
                            'qr_value' => $quickPass->qr_value,
                        ]
                    ]);
                }

                // Check if quick pass is still active
                if ($quickPass->status !== QuickPass::STATUS_ACTIVE) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quick Pass is no longer active (Status: ' . $quickPass->status . ')',
                        'user_type' => $userType,
                        'inside_user' => [
                            'id' => $quickPass->id,
                            'fullname' => $quickPass->visitor_name . ' (Quick Pass)',
                            'qr_value' => $quickPass->qr_value,
                        ],
                        'quick_pass' => [
                            'id' => $quickPass->id,
                            'visitor_name' => $quickPass->visitor_name,
                            'vehicle_plate' => $quickPass->vehicle_plate,
                            'purpose' => $quickPass->purpose,
                            'qr_value' => $quickPass->qr_value,
                        ]
                    ]);
                }

                // For quick pass, check entry/exit logic
                $lastEntryLog = EntryLog::where(function($q) use ($quickPass) {
                        $q->where(function($q2) use ($quickPass) {
                            $q2->where('qr_value', $quickPass->qr_value)
                               ->orWhere('quick_pass_id', $quickPass->id);
                        })
                        ->whereIn('scan_type', ['entry', 'exit']);
                    })
                    ->latest('id')
                    ->first();

                $scanType = 'entry';
                $message = 'Quick Pass entry logged';

                if ($lastEntryLog && $lastEntryLog->scan_type === 'entry') {
                    $scanType = 'exit';
                    $message = 'Quick Pass exit logged';
                }

                // Create entry log for quick pass
                $entryLog = EntryLog::create([
                    'inside_user_id' => null,
                    'outside_user_id' => null,
                    'quick_pass_id' => $quickPass->id,
                    'qr_value' => $quickPass->qr_value,
                    'security_guard_user_id' => $guardId,
                    'scan_at' => Carbon::now()->toDateTimeString(),
                    'scan_type' => $scanType,
                ]);

                // Update currently_inside tracking table (Priority 3 optimization)
                if ($scanType === 'entry') {
                    // Add to currently_inside
                    CurrentlyInside::trackEntry(
                        ['qr_value' => $quickPass->qr_value],
                        [
                            'user_type' => 'quick',
                            'user_id' => $quickPass->id,
                            'fullname' => $quickPass->visitor_name,
                            'email' => 'N/A',
                            'role' => 'Quick Pass',
                            'entered_at' => Carbon::now(),
                            'entry_log_id' => $entryLog->id,
                        ]
                    );
                } else {
                    // Remove from currently_inside (on exit)
                    CurrentlyInside::trackExit($quickPass->qr_value);
                }

                // Don't mark as used - allow multiple entries/exits until expired
                // Quick Pass is valid for unlimited scans until 11:59 PM

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'scan_type' => $scanType,
                    'scan_at' => $entryLog->scan_at,
                    'user_type' => $userType,
                    'inside_user' => [
                        'id' => $quickPass->id,
                        'fullname' => $quickPass->visitor_name . ' (Quick Pass)',
                        'qr_value' => $quickPass->qr_value,
                    ],
                    'quick_pass' => [
                        'id' => $quickPass->id,
                        'visitor_name' => $quickPass->visitor_name,
                        'vehicle_plate' => $quickPass->vehicle_plate ?? 'N/A',
                        'purpose' => $quickPass->purpose,
                        'qr_value' => $quickPass->qr_value,
                        'expires_at' => $quickPass->expires_at->format('M d, h:i A'),
                    ]
                ]);
            }

            // Check if user's QR status is active
            // Note: Event registrations don't have qr_status field, so we only check for user types that have it
            if ($userType !== 'event' && !in_array(strtolower($user->qr_status ?? ''), ['active'])) {
                // Return user info even when QR is inactive (for display purposes)
                return response()->json([
                    'success' => false,
                    'message' => 'QR code is inactive. User is not authorized.',
                    'user_type' => $userType,
                    'inside_user' => [
                        'id' => $user->id,
                        'fullname' => $getUserName($user),
                        'qr_value' => $user->qr_value,
                    ]
                ]);
            }

            // Check if outside user's QR code has expired
            if ($userType === 'outside' && $outsideUser->qr_expires_at) {
                if (Carbon::now()->gt($outsideUser->qr_expires_at)) {
                    // Auto-deactivate expired QR code
                    if ($outsideUser->qr_status === 'active') {
                        $outsideUser->update([
                            'qr_status' => 'inactive',
                            'updated_at' => now(),
                        ]);
                    }
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'QR code has expired (expired at ' . $outsideUser->qr_expires_at->format('M d, Y h:i A') . '). Please contact admin to reactivate.',
                        'user_type' => $userType,
                        'inside_user' => [
                            'id' => $outsideUser->id,
                            'fullname' => $getUserName($outsideUser),
                            'qr_value' => $outsideUser->qr_value,
                        ],
                        'qr_expires_at' => $outsideUser->qr_expires_at->format('M d, Y h:i A'),
                    ]);
                }
            }

            // Handle Event Registration scanning
            if ($userType === 'event' && $eventRegistration) {
                // Check if event is approved
                $event = $eventRegistration->event;
                if (!$event) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Event not found for this registration',
                        'user_type' => $userType,
                        'event_registration' => [
                            'id' => $eventRegistration->id,
                            'fullname' => $eventRegistration->fullname,
                            'qr_code' => $eventRegistration->qr_code,
                            'email' => $eventRegistration->email,
                        ],
                        'event' => [
                            'name' => 'Unknown',
                            'status' => 'Unknown',
                        ]
                    ]);
                }
                
                if ($event->status !== 'approved') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Event is not active (Status: ' . ucfirst($event->status) . ')',
                        'user_type' => $userType,
                        'event_registration' => [
                            'id' => $eventRegistration->id,
                            'fullname' => $eventRegistration->fullname,
                            'qr_code' => $eventRegistration->qr_code,
                            'email' => $eventRegistration->email,
                        ],
                        'event' => [
                            'name' => $event->event_name ?? 'Unknown',
                            'status' => $event->status ?? 'Unknown',
                        ]
                    ]);
                }

                // Check event date (allow today's events)
                if (!$event->event_date || $event->event_date->lt(Carbon::today())) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Event has already passed',
                        'user_type' => $userType,
                        'event_registration' => [
                            'id' => $eventRegistration->id,
                            'fullname' => $eventRegistration->fullname,
                            'qr_code' => $eventRegistration->qr_code,
                            'email' => $eventRegistration->email,
                        ],
                        'event' => [
                            'name' => $event->event_name ?? 'Unknown',
                            'date' => $event->event_date ? $event->event_date->format('M d, Y') : 'Unknown',
                        ]
                    ]);
                }

                // Handle check-in/check-out for event
                // Toggle between: registered -> checked_in -> checked_out -> checked_in -> ...
                $scanType = 'entry';
                $message = 'Event check-in successful';

                if ($eventRegistration->status === 'checked_in') {
                    // Check out
                    $scanType = 'exit';
                    $message = 'Event check-out successful';

                    $eventRegistration->update([
                        'status' => 'checked_out',
                        'checked_out_at' => now(),
                    ]);
                } elseif ($eventRegistration->status === 'checked_out') {
                    // Re-check in (allow toggling)
                    $scanType = 'entry';
                    $message = 'Event check-in successful';

                    $eventRegistration->update([
                        'status' => 'checked_in',
                        'checked_in_at' => now(),
                    ]);
                } elseif ($eventRegistration->status === 'registered') {
                    // First check-in
                    $eventRegistration->update([
                        'status' => 'checked_in',
                        'checked_in_at' => now(),
                    ]);
                }

                // Log the scan for event registration
                $entryLog = EntryLog::create([
                    'inside_user_id' => null,
                    'outside_user_id' => $eventRegistration->outside_user_id,
                    'event_registration_id' => $eventRegistration->id,
                    'quick_pass_id' => null,
                    'qr_value' => $eventRegistration->qr_code,
                    'security_guard_user_id' => $guardId,
                    'scan_at' => Carbon::now()->toDateTimeString(),
                    'scan_type' => $scanType,
                ]);

                // Update currently_inside tracking table (Priority 3 optimization)
                if ($scanType === 'entry') {
                    // Add to currently_inside
                    CurrentlyInside::trackEntry(
                        ['qr_value' => $eventRegistration->qr_code],
                        [
                            'user_type' => 'event',
                            'user_id' => $eventRegistration->id,
                            'fullname' => $eventRegistration->fullname,
                            'email' => $eventRegistration->email,
                            'role' => 'Event Attendee',
                            'entered_at' => Carbon::now(),
                            'entry_log_id' => $entryLog->id,
                        ]
                    );
                } else {
                    // Remove from currently_inside (on exit)
                    CurrentlyInside::trackExit($eventRegistration->qr_code);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'scan_type' => $scanType,
                    'scan_at' => Carbon::now()->toDateTimeString(),
                    'user_type' => $userType,
                    'event_registration' => [
                        'id' => $eventRegistration->id,
                        'fullname' => $eventRegistration->fullname,
                        'qr_code' => $eventRegistration->qr_code,
                        'email' => $eventRegistration->email,
                        'status' => $eventRegistration->status,
                    ],
                    'event' => [
                        'name' => $event->event_name ?? 'Unknown',
                        'date' => $event->event_date ? $event->event_date->format('M d, Y') : 'Unknown',
                        'time' => $event->event_start_time ? $event->event_start_time->format('g:i A') : 'Unknown',
                    ]
                ]);
            }

            // For inside users, check entry/exit logic
            if ($userType === 'inside') {
                // Only consider actual entry/exit logs, NOT QR status toggle logs
                $lastEntryLog = EntryLog::where('inside_user_id', $insideUser->id)
                    ->whereIn('scan_type', ['entry', 'exit'])
                    ->latest('id')
                    ->first();

                $scanType = 'entry';
                $message = 'Entry logged successfully';

                if ($lastEntryLog && $lastEntryLog->scan_type === 'entry') {
                    $scanType = 'exit';
                    $message = 'Exit logged successfully';
                }

                // Create entry log for inside user
                $entryLog = EntryLog::create([
                    'inside_user_id' => $insideUser->id,
                    'outside_user_id' => null,
                    'quick_pass_id' => null,
                    'event_registration_id' => null,
                    'qr_value' => $insideUser->qr_value,
                    'security_guard_user_id' => $guardId,
                    'scan_at' => Carbon::now()->toDateTimeString(),
                    'scan_type' => $scanType,
                ]);

                // Update currently_inside tracking table (Priority 3 optimization)
                if ($scanType === 'entry') {
                    // Add to currently_inside
                    CurrentlyInside::trackEntry(
                        ['qr_value' => $insideUser->qr_value],
                        [
                            'user_type' => 'inside',
                            'user_id' => $insideUser->id,
                            'fullname' => $insideUser->fullname,
                            'email' => $insideUser->email ?? 'N/A',
                            'role' => $insideUser->role ?? 'N/A',
                            'entered_at' => Carbon::now(),
                            'entry_log_id' => $entryLog->id,
                        ]
                    );
                } else {
                    // Remove from currently_inside (on exit)
                    CurrentlyInside::trackExit($insideUser->qr_value);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'scan_type' => $scanType,
                    'scan_at' => $entryLog->scan_at,
                    'user_type' => $userType,
                    'inside_user' => [
                        'id' => $insideUser->id,
                        'fullname' => $getUserName($insideUser),
                        'qr_value' => $insideUser->qr_value,
                    ]
                ]);
            }

            // For outside users, check entry/exit logic (alternate between entry and exit)
            // Only consider actual entry/exit logs, NOT QR status toggle logs
            if ($userType === 'outside' && $outsideUser) {
                $lastEntryLog = EntryLog::where('outside_user_id', $outsideUser->id)
                    ->whereIn('scan_type', ['entry', 'exit'])
                    ->latest('id')
                    ->first();

                $scanType = 'entry';
                $message = 'Visitor entry logged successfully';

                if ($lastEntryLog && $lastEntryLog->scan_type === 'entry') {
                    $scanType = 'exit';
                    $message = 'Visitor exit logged successfully';
                }

                // Create entry log for outside user
                $entryLog = EntryLog::create([
                    'inside_user_id' => null,
                    'outside_user_id' => $outsideUser->id,
                    'quick_pass_id' => null,
                    'event_registration_id' => null,
                    'qr_value' => $outsideUser->qr_value,
                    'security_guard_user_id' => $guardId,
                    'scan_at' => Carbon::now()->toDateTimeString(),
                    'scan_type' => $scanType,
                ]);

                // Update currently_inside tracking table (Priority 3 optimization)
                if ($scanType === 'entry') {
                    // Add to currently_inside
                    CurrentlyInside::trackEntry(
                        ['qr_value' => $outsideUser->qr_value],
                        [
                            'user_type' => 'outside',
                            'user_id' => $outsideUser->id,
                            'fullname' => $outsideUser->fullname,
                            'email' => $outsideUser->email ?? 'N/A',
                            'role' => 'Visitor',
                            'entered_at' => Carbon::now(),
                            'entry_log_id' => $entryLog->id,
                        ]
                    );
                } else {
                    // Remove from currently_inside (on exit)
                    CurrentlyInside::trackExit($outsideUser->qr_value);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'scan_type' => $scanType,
                    'scan_at' => $entryLog->scan_at,
                    'user_type' => $userType,
                    'inside_user' => [
                        'id' => $outsideUser->id,
                        'fullname' => $getUserName($outsideUser),
                        'qr_value' => $outsideUser->qr_value,
                    ]
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Scan error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing scan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent scan history
     */
    public function scanHistory()
    {
        $guardId = Auth::guard('securityguard')->id();

        // Only show entry/exit logs in scanner history (exclude QR status toggles)
        $scans = EntryLog::where('security_guard_user_id', $guardId)
            ->whereIn('scan_type', ['entry', 'exit'])
            ->with(['insideUser', 'outsideUser', 'quickPass', 'eventRegistration'])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Map scans to a consistent format for the frontend
        $formattedScans = $scans->map(function($scan) {
            $fullname = 'Unknown User';
            $userType = 'unknown';
            $qrValue = 'N/A';

            // Check for event registration first
            if ($scan->eventRegistration) {
                $fullname = $scan->eventRegistration->fullname;
                $userType = 'event';
                $qrValue = $scan->eventRegistration->qr_code;
                
                return [
                    'event_registration' => [
                        'fullname' => $fullname,
                        'qr_code' => $qrValue,
                    ],
                    'scan_type' => $scan->scan_type,
                    'scan_at' => $scan->scan_at,
                    'user_type' => $userType,
                ];
            }
            
            // Check for quick pass
            if ($scan->quickPass) {
                $fullname = $scan->quickPass->visitor_name . ' (Quick Pass)';
                $userType = 'quick_pass';
                $qrValue = $scan->quickPass->qr_value;
                
                return [
                    'quick_pass' => [
                        'visitor_name' => $fullname,
                        'qr_value' => $qrValue,
                    ],
                    'scan_type' => $scan->scan_type,
                    'scan_at' => $scan->scan_at,
                    'user_type' => $userType,
                ];
            }
            
            // Check for inside user
            if ($scan->insideUser) {
                $fullname = $scan->insideUser->fullname ?: trim(($scan->insideUser->first_name ?? '') . ' ' . ($scan->insideUser->last_name ?? '')) ?: 'User ' . $scan->insideUser->id;
                $userType = 'inside';
                $qrValue = $scan->insideUser->qr_value;
                
                return [
                    'inside_user' => [
                        'fullname' => $fullname,
                        'qr_value' => $qrValue,
                    ],
                    'scan_type' => $scan->scan_type,
                    'scan_at' => $scan->scan_at,
                    'user_type' => $userType,
                ];
            }
            
            // Check for outside user
            if ($scan->outsideUser) {
                $fullname = $scan->outsideUser->fullname ?: trim(($scan->outsideUser->first_name ?? '') . ' ' . ($scan->outsideUser->last_name ?? '')) ?: 'User ' . $scan->outsideUser->id;
                $userType = 'outside';
                $qrValue = $scan->outsideUser->qr_value;
                
                return [
                    'inside_user' => [
                        'fullname' => $fullname,
                        'qr_value' => $qrValue,
                    ],
                    'scan_type' => $scan->scan_type,
                    'scan_at' => $scan->scan_at,
                    'user_type' => $userType,
                ];
            }
            
            // Fallback for unknown scans
            return [
                'inside_user' => [
                    'fullname' => 'Unknown User',
                    'qr_value' => $scan->qr_value ?? 'N/A',
                ],
                'scan_type' => $scan->scan_type,
                'scan_at' => $scan->scan_at,
                'user_type' => $userType,
            ];
        });

        return response()->json([
            'scans' => $formattedScans
        ]);
    }

    /**
     * View all entry/exit logs with filters
     */
    public function viewEntryLogs(Request $request)
    {
        $query = EntryLog::with(['insideUser', 'outsideUser', 'securityGuardUser', 'quickPass', 'eventRegistration']);

        // Filter out QR status toggle logs (only show entry/exit)
        $query->whereNotIn('scan_type', ['qr_active', 'qr_inactive'])
              ->whereNotNull('scan_type');

        // Filter by scan type (entry/exit)
        if ($request->filled('scan_type')) {
            $query->where('scan_type', $request->scan_type);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('scan_at', $request->date);
        }

        // Search by user name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Search in inside users
                $q->whereHas('insideUser', function($q) use ($search) {
                    $q->where('fullname', 'LIKE', "%{$search}%")
                      ->orWhere('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                })
                // Or search in outside users
                ->orWhereHas('outsideUser', function($q) use ($search) {
                    $q->where('fullname', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                })
                // Or search in quick passes
                ->orWhereHas('quickPass', function($q) use ($search) {
                    $q->where('visitor_name', 'LIKE', "%{$search}%")
                      ->orWhere('vehicle_plate', 'LIKE', "%{$search}%");
                })
                // Or search in event registrations
                ->orWhereHas('eventRegistration', function($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                })
                // Or search by QR value (for event registrations without outside_user_id)
                ->orWhere('qr_value', 'LIKE', "%{$search}%");
            });
        }

        // Get statistics (only entry/exit, not QR toggles)
        $today = Carbon::today();
        $totalEntriesToday = EntryLog::where('scan_type', 'entry')
            ->whereDate('scan_at', $today)
            ->count();

        $totalExitsToday = EntryLog::where('scan_type', 'exit')
            ->whereDate('scan_at', $today)
            ->count();

        // Get count from currently_inside table (instant query)
        $currentlyInsideCount = CurrentlyInside::count();

        // Get people currently inside from tracking table (Priority 3 optimization)
        // OLD CODE (loads ALL entry logs - CRASHES with 1M+ records):
        // $allLogs = EntryLog::whereNotNull('qr_value')
        //     ->whereIn('scan_type', ['entry', 'exit'])
        //     ->orderBy('qr_value')
        //     ->orderBy('id', 'desc')
        //     ->get()
        //     ->groupBy('qr_value');
        
        // NEW CODE (instant query from currently_inside table):
        $currentlyInsidePeople = CurrentlyInside::getAllInside()
            ->map(function($person) {
                return [
                    'fullname' => $person->fullname,
                    'email' => $person->email,
                    'role' => $person->role ?? 'N/A',
                    'scan_at' => $person->entered_at,
                ];
            });

        $logs = $query->orderBy('scan_at', 'desc')->paginate(20);

        return view('SecurityGuardUser.EntryLogs.entry_logs', compact('logs', 'totalEntriesToday', 'totalExitsToday', 'currentlyInsideCount', 'currentlyInsidePeople'));
    }

    /**
     * Show shift management dashboard
     */
    public function showShiftManagement()
    {
        $guardId = Auth::guard('securityguard')->id();
        $guard = securityguard::findOrFail($guardId);

        // Get current active shift log
        $currentShiftLog = ShiftLog::where('security_guard_user_id', $guardId)
            ->whereNotNull('clock_in_time')
            ->whereNull('clock_out_time')
            ->with('shift')
            ->first();

        // Get today's scheduled shift
        $todayShift = Shift::where('security_guard_user_id', $guardId)
            ->where('shift_date', today())
            ->first();

        // Get shift history (last 7 days)
        $recentShiftLogs = ShiftLog::where('security_guard_user_id', $guardId)
            ->whereNotNull('clock_out_time')
            ->with('shift')
            ->orderBy('clock_out_time', 'desc')
            ->limit(10)
            ->get();

        // Statistics
        $totalShiftsThisWeek = ShiftLog::where('security_guard_user_id', $guardId)
            ->whereBetween('clock_in_time', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $totalHoursThisWeek = ShiftLog::where('security_guard_user_id', $guardId)
            ->whereBetween('clock_in_time', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereNotNull('clock_out_time')
            ->get()
            ->sum(function($log) {
                return $log->clock_in_time->diffInHours($log->clock_out_time);
            });

        return view('SecurityGuardUser.ShiftManagement.shift_management', compact(
            'currentShiftLog',
            'todayShift',
            'recentShiftLogs',
            'totalShiftsThisWeek',
            'totalHoursThisWeek'
        ));
    }

    /**
     * Clock in for shift
     */
    public function clockIn(Request $request)
    {
        $guardId = Auth::guard('securityguard')->id();

        // Check if already clocked in
        $activeShiftLog = ShiftLog::where('security_guard_user_id', $guardId)
            ->whereNotNull('clock_in_time')
            ->whereNull('clock_out_time')
            ->first();

        if ($activeShiftLog) {
            return redirect()->back()->with('error', 'You are already clocked in!');
        }

        // Find today's scheduled shift
        $todayShift = Shift::where('security_guard_user_id', $guardId)
            ->where('shift_date', today())
            ->first();

        // Create shift log
        ShiftLog::create([
            'security_guard_user_id' => $guardId,
            'shift_id' => $todayShift?->id,
            'clock_in_time' => now(),
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Successfully clocked in!');
    }

    /**
     * Clock out from shift
     */
    public function clockOut(Request $request)
    {
        $guardId = Auth::guard('securityguard')->id();

        // Find active shift log
        $activeShiftLog = ShiftLog::where('security_guard_user_id', $guardId)
            ->whereNotNull('clock_in_time')
            ->whereNull('clock_out_time')
            ->first();

        if (!$activeShiftLog) {
            return redirect()->back()->with('error', 'You are not clocked in!');
        }

        // Update clock out time
        $activeShiftLog->update([
            'clock_out_time' => now(),
            'status' => 'completed',
        ]);

        return redirect()->back()->with('success', 'Successfully clocked out!');
    }

    /**
     * Submit handover note
     */
    public function submitHandover(Request $request)
    {
        $request->validate([
            'handover_note' => 'required|string|max:1000',
            'shift_log_id' => 'required|exists:mysql_second.shift_logs,id'
        ]);

        $shiftLog = ShiftLog::findOrFail($request->shift_log_id);

        // Verify guard owns this shift log
        if ($shiftLog->security_guard_user_id !== Auth::guard('securityguard')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $shiftLog->update([
            'handover_note' => $request->handover_note,
        ]);

        return redirect()->back()->with('success', 'Handover note submitted successfully!');
    }

    /**
     * Show shift schedule
     */
    public function showShiftSchedule()
    {
        $guardId = Auth::guard('securityguard')->id();

        // Get upcoming shifts (next 30 days)
        $upcomingShifts = Shift::where('security_guard_user_id', $guardId)
            ->where('shift_date', '>=', today())
            ->orderBy('shift_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(15);

        return view('SecurityGuardUser.ShiftManagement.shift_schedule', compact('upcomingShifts'));
    }

    /**
     * Show shift history
     */
    public function showShiftHistory(Request $request)
    {
        $guardId = Auth::guard('securityguard')->id();

        $query = ShiftLog::where('security_guard_user_id', $guardId)
            ->whereNotNull('clock_out_time')
            ->with(['shift']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('clock_in_time', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('clock_in_time', '<=', $request->end_date);
        }

        $shiftHistory = $query->orderBy('clock_in_time', 'desc')->paginate(15);

        // Calculate total hours
        $totalHours = $shiftHistory->sum(function($log) {
            return $log->clock_in_time->diffInHours($log->clock_out_time);
        });

        return view('SecurityGuardUser.ShiftManagement.shift_history', compact('shiftHistory', 'totalHours'));
    }


    // =========================================================================
    // QUICK PASS (TEMPORARY QR) MANAGEMENT
    // =========================================================================

    /**
     * Show list of today's quick passes
     */
    public function showQuickPass(Request $request)
    {
        // Auto-expire active passes that are past their date/time before displaying
        QuickPass::where('status', QuickPass::STATUS_ACTIVE)
            ->where(function($q) {
                $q->where('expires_at', '<', Carbon::now())
                  ->orWhere('valid_date', '<', Carbon::today()->toDateString());
            })
            ->update(['status' => QuickPass::STATUS_EXPIRED]);

        $query = QuickPass::notDeleted();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('visitor_name', 'LIKE', "%{$search}%")
                  ->orWhere('vehicle_plate', 'LIKE', "%{$search}%")
                  ->orWhere('purpose', 'LIKE', "%{$search}%")
                  ->orWhere('qr_value', 'LIKE', "%{$search}%");
            });
        }

        $quickPasses = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('SecurityGuardUser.QuickPass.quick_pass_list', compact('quickPasses'));
    }

    /**
     * Show form to create a new quick pass
     */
    public function createQuickPass()
    {
        return view('SecurityGuardUser.QuickPass.quick_pass_create');
    }

    /**
     * Store a new quick pass
     */
    public function storeQuickPass(Request $request)
    {
        $request->validate([
            'visitor_name' => 'required|string|max:150',
            'vehicle_plate' => 'nullable|string|max:20',
            'purpose' => 'required|in:Delivery,Meeting,Parent,Contractor,Other',
            'expiry_time' => 'nullable',
        ]);

        // Generate unique QR code: QUICK-YYYYMMDD-RANDOM
        $qrValue = 'QUICK-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid() . rand(1000, 9999), -6));

        // Handle custom expiration time (for testing or early expiry)
        $expiresAt = today()->endOfDay();
        if ($request->filled('expiry_time')) {
            $expiresAt = Carbon::parse(today()->toDateString() . ' ' . $request->expiry_time);
        }

        // Create quick pass
        $quickPass = QuickPass::create([
            'visitor_name' => $request->visitor_name,
            'vehicle_plate' => $request->vehicle_plate,
            'purpose' => $request->purpose,
            'qr_value' => $qrValue,
            'valid_date' => today(),
            'expires_at' => $expiresAt,
            'status' => QuickPass::STATUS_ACTIVE,
            'created_by_guard_id' => Auth::guard('securityguard')->id(),
        ]);

        return redirect()->route('security.quick-pass.qr', $quickPass->id)
            ->with('success', 'Quick Pass created successfully!');
    }

    /**
     * Display QR code for a quick pass
     */
    public function showQuickPassQr($id)
    {
        $quickPass = QuickPass::findOrFail($id);

        // Auto-expire if past expiration time or date
        if ($quickPass->status === QuickPass::STATUS_ACTIVE && $quickPass->isExpired()) {
            $quickPass->markAsExpired();
        }

        return view('SecurityGuardUser.QuickPass.quick_pass_qr', compact('quickPass'));
    }

    /**
     * Delete a quick pass
     */
    public function deleteQuickPass($id)
    {
        $quickPass = QuickPass::findOrFail($id);
        $quickPass->delete(); // Soft delete

        return redirect()->route('security.quick-pass.list')
            ->with('success', 'Quick Pass deleted successfully!');
    }

    /**
     * Scan event QR code
     */
    public function scanEventQR($qr)
    {
        // Find registration by QR code
        $registration = EventRegistration::where('qr_code', $qr)->first();

        if (!$registration) {
            return view('SecurityGuardUser.event-qr-scan-result', [
                'success' => false,
                'message' => 'Invalid QR Code',
                'details' => 'This QR code is not recognized in our system.',
            ]);
        }

        // Check if event exists and is approved
        $event = $registration->event;
        if (!$event || $event->status !== Event::STATUS_APPROVED) {
            return view('SecurityGuardUser.event-qr-scan-result', [
                'success' => false,
                'message' => 'Event Not Active',
                'details' => 'This event is not currently active.',
            ]);
        }

        // Check event date
        if ($event->event_date->isPast()) {
            return view('SecurityGuardUser.event-qr-scan-result', [
                'success' => false,
                'message' => 'Event Expired',
                'details' => 'This event has already passed.',
            ]);
        }

        // Handle check-in/check-out
        $action = 'view';
        $success = true;
        $message = '';
        
        if ($registration->status === 'registered') {
            // Check in
            $registration->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
            ]);
            $action = 'checkin';
            $message = 'Check-in successful!';
        } elseif ($registration->status === 'checked_in') {
            // Check out
            $registration->update([
                'status' => 'checked_out',
                'checked_out_at' => now(),
            ]);
            $action = 'checkout';
            $message = 'Check-out successful!';
        } else {
            $action = 'already';
            $message = 'Already checked out';
        }

        // Log the scan
        EntryLog::create([
            'inside_user_id' => null,
            'outside_user_id' => $registration->outside_user_id,
            'quick_pass_id' => null,
            'qr_value' => $qr,
            'security_guard_user_id' => Auth::guard('securityguard')->id(),
            'scan_at' => now()->format('Y-m-d H:i:s'),
            'scan_type' => $registration->status === 'checked_out' ? 'exit' : 'entry',
        ]);

        return view('SecurityGuardUser.event-qr-scan-result', compact(
            'registration',
            'event',
            'action',
            'message',
            'success'
        ));
    }
}
