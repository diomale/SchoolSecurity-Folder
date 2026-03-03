<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OutsideUser;
use App\Models\EntryLog;
use App\Models\InsideUser;
use App\Models\securityguard;
use App\Models\Shift;
use App\Models\ShiftLog;
use Carbon\Carbon;

class SecurityGuardController extends Controller
{
    //show
    public function dashboard()
    {
        $guard = Auth::guard('securityguard')->user();

        // Get recent QR status change activities from ALL guards (shared notifications)
        $recentActivities = EntryLog::with(['insideUser', 'outsideUser', 'securityGuardUser'])
            ->whereNotNull('security_guard_user_id')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        // Get statistics for current guard
        $totalScans = EntryLog::where('security_guard_user_id', $guard->id)->count();
        $todayScans = EntryLog::where('security_guard_user_id', $guard->id)
            ->whereDate('scan_at', today())
            ->count();
        $todayEntries = EntryLog::where('security_guard_user_id', $guard->id)
            ->where('scan_type', 'entry')
            ->whereDate('scan_at', today())
            ->count();
        $todayExits = EntryLog::where('security_guard_user_id', $guard->id)
            ->where('scan_type', 'exit')
            ->whereDate('scan_at', today())
            ->count();

        // Get total guards count
        $totalGuards = securityguard::count();

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
        // Search for inside users
        $insideQuery = InsideUser::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $insideQuery->where(function($q) use ($search) {
                $q->where('fullname', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%")
                  ->orWhere('qr_value', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        $inside_users = $insideQuery->orderBy('id', 'desc')->paginate(15);

        // Search for outside users
        $outsideQuery = OutsideUser::query();
        if ($request->filled('search')) {
            $search = $request->search;
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
        $outside_users = $outsideQuery->orderBy('id', 'desc')->paginate(15);

        return view('SecurityGuardUser.QrStatusManagement.qr_status_management', compact('inside_users', 'outside_users'));
    }

    public function toggleQrStatus($id)
    {
        $guard = Auth::guard('securityguard')->user();

        // Try to find inside user first
        $inside_user = InsideUser::find($id);
        
        if ($inside_user) {
            // Toggle inside user QR status
            $newStatus = in_array(strtolower($inside_user->qr_status), ['active']) ? 'inactive' : 'active';

            $inside_user->update([
                'qr_status' => $newStatus,
                'updated_at' => now(),
            ]);

            // Create notification/activity log for other guards
            EntryLog::create([
                'inside_user_id' => $inside_user->id,
                'outside_user_id' => null,
                'security_guard_user_id' => $guard->id,
                'scan_at' => now()->toDateTimeString(),
                'scan_type' => 'qr_' . $newStatus,
            ]);

            return redirect()->back()->with('success', "QR status for {$inside_user->fullname} changed to {$newStatus}!");
        }

        // Try to find outside user
        $outside_user = OutsideUser::findOrFail($id);
        $newStatus = in_array(strtolower($outside_user->qr_status), ['active']) ? 'inactive' : 'active';

        $outside_user->update([
            'qr_status' => $newStatus,
            'updated_at' => now(),
        ]);

        // Create notification/activity log for other guards
        EntryLog::create([
            'inside_user_id' => null,
            'outside_user_id' => $outside_user->id,
            'security_guard_user_id' => $guard->id,
            'scan_at' => now()->toDateTimeString(),
            'scan_type' => 'qr_' . $newStatus,
        ]);

        return redirect()->back()->with('success', "QR status for visitor {$outside_user->fullname} changed to {$newStatus}!");
    }

    //function
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Add 'status' => 1 to ensure only active guards can log in
        if (Auth::guard('securityguard')->attempt(array_merge($credentials, ['status' => 1]))) {

            // IMPORTANT: Regenerate session to prevent session fixation
            $request->session()->regenerate();

            return redirect()->intended(route('security.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email'); // Keeps the email in the field for the user
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
            $userType = 'inside';
            $user = null;

            // If not found, try outside user
            if (!$insideUser) {
                $outsideUser = OutsideUser::where('qr_value', $qrValue)->first();
                $userType = 'outside';
                $user = $outsideUser;
            } else {
                $user = $insideUser;
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            // Check if user's QR status is active
            if (!in_array(strtolower($user->qr_status), ['active'])) {
                // Return user info even when QR is inactive (for display purposes)
                return response()->json([
                    'success' => false,
                    'message' => 'QR code is inactive. User is not authorized.',
                    'user_type' => $userType,
                    'inside_user' => [
                        'id' => $user->id,
                        'fullname' => $user->fullname,
                        'qr_value' => $user->qr_value,
                    ]
                ]);
            }

            // For inside users, check entry/exit logic
            if ($userType === 'inside') {
                $lastEntryLog = EntryLog::where('inside_user_id', $insideUser->id)
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
                    'security_guard_user_id' => $guardId,
                    'scan_at' => Carbon::now()->toDateTimeString(),
                    'scan_type' => $scanType,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'scan_type' => $scanType,
                    'scan_at' => $entryLog->scan_at,
                    'user_type' => $userType,
                    'inside_user' => [ // Matching the key expected by frontend
                        'id' => $insideUser->id,
                        'fullname' => $insideUser->fullname,
                        'qr_value' => $insideUser->qr_value,
                    ]
                ]);
            } else {
                // For outside users, check entry/exit logic (alternate between entry and exit)
                $lastEntryLog = EntryLog::where('outside_user_id', $outsideUser->id)
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
                    'security_guard_user_id' => $guardId,
                    'scan_at' => Carbon::now()->toDateTimeString(),
                    'scan_type' => $scanType,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'scan_type' => $scanType,
                    'scan_at' => $entryLog->scan_at,
                    'user_type' => $userType,
                    'inside_user' => [ // Frontend expects 'inside_user' key even for visitors
                        'id' => $outsideUser->id,
                        'fullname' => $outsideUser->fullname,
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

        $scans = EntryLog::where('security_guard_user_id', $guardId)
            ->with(['insideUser', 'outsideUser'])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Map scans to a consistent format for the frontend
        $formattedScans = $scans->map(function($scan) {
            $user = $scan->insideUser ?: $scan->outsideUser;
            $userType = $scan->insideUser ? 'inside' : 'outside';
            return [
                'inside_user' => [
                    'fullname' => $user ? $user->fullname : 'Unknown User',
                    'qr_value' => $user ? $user->qr_value : 'N/A',
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
        $query = EntryLog::with(['insideUser', 'outsideUser', 'securityGuardUser']);

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
                });
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

        $currentlyInside = $totalEntriesToday - $totalExitsToday;

        $logs = $query->orderBy('scan_at', 'desc')->paginate(20);

        return view('SecurityGuardUser.EntryLogs.entry_logs', compact('logs', 'totalEntriesToday', 'totalExitsToday', 'currentlyInside'));
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
            'shift_log_id' => 'required|exists:shift_logs,id'
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
}
