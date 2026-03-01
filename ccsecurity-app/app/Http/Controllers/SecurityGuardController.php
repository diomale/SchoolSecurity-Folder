<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OutsideUser;
use App\Models\EntryLog;
use App\Models\InsideUser;
use App\Models\securityguard;
use Carbon\Carbon;

class SecurityGuardController extends Controller
{
    //show
    public function dashboard()
    {
        return view('SecurityGuardUser.dashboard');
    }

    public function showLogin()
    {
        return view('SecurityGuardUser.login');
    }

    public function showScanner()
    {
        return view('SecurityGuardUser.Scanner.scanner');
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
        $request->validate([
            'qr_value' => 'required|string'
        ]);

        $qrValue = $request->qr_value;
        $guardId = Auth::guard('securityguard')->id();

        // Find inside user by qr_value
        $insideUser = InsideUser::where('qr_value', $qrValue)->first();

        if (!$insideUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        // Check if user has an active entry (no exit logged)
        $lastEntryLog = EntryLog::where('inside_user_id', $insideUser->id)
            ->latest('id')
            ->first();

        $scanType = 'entry';
        $message = 'Entry logged successfully';

        if ($lastEntryLog) {
            // If last scan was entry, this is an exit
            $scanType = 'exit';
            $message = 'Exit logged successfully';
        }

        // Create entry log
        $entryLog = EntryLog::create([
            'inside_user_id' => $insideUser->id,
            'security_guard_user_id' => $guardId,
            'scan_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'scan_type' => $scanType,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'scan_type' => $scanType,
            'inside_user' => [
                'id' => $insideUser->id,
                'fullname' => $insideUser->fullname,
                'qr_value' => $insideUser->qr_value,
            ]
        ]);
    }

    /**
     * Get recent scan history
     */
    public function scanHistory()
    {
        $guardId = Auth::guard('securityguard')->id();

        $scans = EntryLog::where('security_guard_user_id', $guardId)
            ->with('insideUser')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'scans' => $scans
        ]);
    }
}
