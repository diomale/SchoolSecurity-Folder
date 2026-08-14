<?php

namespace App\Http\Controllers;

use App\Models\admin;
use App\Models\SuperAdmin;
use App\Models\SuperAdminActivityLog;
use App\Models\SuperadminDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeviceVerificationMail;
use App\Services\SuperAdminActivityLogger;

class SuperAdminAuthController extends Controller
{
    //Dashboard
    public function dashboard()
    {
        $admins = Admin::all(); 
        
        // Overview Statistics
        $totalAdmins = Admin::count();
        $totalGuards = \App\Models\securityguard::count();
        $totalInsideUsers = \App\Models\InsideUser::count();
        $totalOutsideUsers = \App\Models\OutsideUser::count();
        $totalEntryLogs = \App\Models\EntryLog::count();
        $totalEvents = \App\Models\Event::count();
        $totalShifts = \App\Models\Shift::count();
        $currentlyInside = \App\Models\CurrentlyInside::count();
        $pendingVisitRequests = \App\Models\VisitRequest::where('status', 'pending')->count();
        $activeGuards = \App\Models\securityguard::where('status', 1)->count();

        return view('superadmin.dashboard', compact(
            'admins', 
            'totalAdmins', 
            'totalGuards', 
            'totalInsideUsers', 
            'totalOutsideUsers',
            'totalEntryLogs',
            'totalEvents',
            'totalShifts',
            'currentlyInside',
            'pendingVisitRequests',
            'activeGuards'
        ));
    }

    public function showLogs()
    {
        $logs = SuperAdminActivityLog::orderByDesc('created_at')->paginate(15);
        $totalLogs = SuperAdminActivityLog::count();

        return view('superadmin.logs', compact('logs', 'totalLogs'));
    }

    // Create, Read, Update, Delete,
    public function showAddForm()
    {
        return view('superadmin.SuperadminCrudSection.superadmin_add_form');
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|string|email|max:155|unique:mysql_second.admins,email',
            'password' => 'required|string|min:8|max:100'
        ]);

        Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 1,
        ]);

        SuperAdminActivityLogger::adminManagement('Created Admin', "Created new admin: {$validated['name']} ({$validated['email']})");

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'New Admin created successfully!');
    }

   
    public function showAdminDetails($id)
    {
        $admin = Admin::findOrFail($id);
        return view('superadmin.SuperadminCrudSection.superadmin_details', compact('admin'));
    }

    public function deleteAdmin(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        // Prevent super admin from deleting their own account
        if (Auth::guard('superadmin')->user()?->email === $admin->email) {
            return redirect()->route('superadmin.dashboard')
                ->with('error', 'You cannot delete your own administrator account.');
        }

        // Verify current password
        $request->validate([
            'admin_password' => ['required', new \App\Rules\CurrentAdminPassword('superadmin')]
        ]);

        $admin->delete();

        SuperAdminActivityLogger::adminManagement('Deleted Admin', "Deleted admin: {$admin->name} ({$admin->email})");

        return redirect()->route('superadmin.dashboard')
        ->with('success', 'Admin deleted successfully!');
    }
    
    public function viewEditForm($id)
    {
        $admin = Admin::findOrFail($id);
        return view('superadmin.SuperadminCrudSection.superadmin_edit', compact('admin'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:mysql_second.admins,email,' . $id,
            'password' => 'nullable|string|min:8',
            'status' => 'required|boolean', 
        ]);

        $data = $request->only(['name', 'email', 'status']);

        
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $admin->update($data);

        SuperAdminActivityLogger::adminManagement('Updated Admin', "Updated admin: {$admin->name} ({$admin->email})");

        return redirect()->route('superadmin.dashboard')->with('Success', 'Admin updated successfully');
    }

    //login, logout

    public function showLogin()
    {
        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => ['required', new \App\Rules\Recaptcha],
        ]);

        // Rate limiting: Check for too many failed attempts
        $email = $request->email;
        $rateLimitKey = 'superadmin_login_attempts_' . md5($email);
        $maxAttempts = 5;
        $lockoutMinutes = 15;

        $attempts = cache()->get($rateLimitKey, 0);
        if ($attempts >= $maxAttempts) {
            return back()->withErrors([
                'email' => 'Too many failed attempts. Please try again in ' . $lockoutMinutes . ' minutes.'
            ])->withInput($request->only('email'));
        }

        if (Auth::guard('superadmin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'active'
        ])) {
            $user = Auth::guard('superadmin')->user();
            
            // Reset rate limit on successful login
            cache()->forget($rateLimitKey);
            
            // Always require email verification for superadmin
            Auth::guard('superadmin')->logout();
            
            // Generate verification code
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Generate device fingerprint
            $fingerprint = $this->generateDeviceFingerprint($request);
            
            // Store verification data in session
            $sessionData = [
                'superadmin_device_verification_user_id' => $user->id,
                'superadmin_device_verification_code' => $verificationCode,
                'superadmin_device_verification_fingerprint' => $fingerprint,
                'superadmin_device_verification_ip' => $request->ip(),
                'superadmin_device_verification_ua' => $request->userAgent(),
                'superadmin_device_verification_created_at' => now()->timestamp,
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
                \Log::error('Failed to send superadmin verification email: ' . $e->getMessage());
            }
            
            return redirect()->route('superadmin.device.verify.show');
        }

        // Increment rate limit on failed login
        cache()->put($rateLimitKey, $attempts + 1, now()->addMinutes($lockoutMinutes));

        return back()->withErrors([
            'email' => 'Invalid credentials or not a super admin'
        ])->withInput($request->only('email'));
    }

    public function showDeviceVerification()
    {
        $userId = session('superadmin_device_verification_user_id');
        
        if (!$userId) {
            return redirect()->route('superadmin.login');
        }
        
        $user = SuperAdmin::find($userId);
        
        if (!$user) {
            return redirect()->route('superadmin.login');
        }
        
        return view('superadmin.device-verification', compact('user'));
    }

    public function verifyDevice(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6',
        ]);
        
        $storedCode = session('superadmin_device_verification_code');
        $createdAt = session('superadmin_device_verification_created_at');
        
        // Check if code is expired (15 minutes)
        if ($createdAt && (now()->timestamp - $createdAt) > 900) {
            session()->forget([
                'superadmin_device_verification_user_id',
                'superadmin_device_verification_code',
                'superadmin_device_verification_fingerprint',
                'superadmin_device_verification_ip',
                'superadmin_device_verification_ua',
                'superadmin_device_verification_created_at',
            ]);
            
            return redirect()->route('superadmin.login')->withErrors([
                'verification_code' => 'Verification code has expired. Please login again.'
            ]);
        }
        
        // Rate limiting for verification code attempts
        $rateLimitKey = 'superadmin_verify_attempts_' . md5(session('superadmin_device_verification_user_id', 'unknown'));
        $maxAttempts = 5;
        $lockoutMinutes = 15;

        $attempts = cache()->get($rateLimitKey, 0);
        if ($attempts >= $maxAttempts) {
            session()->forget([
                'superadmin_device_verification_user_id',
                'superadmin_device_verification_code',
                'superadmin_device_verification_fingerprint',
                'superadmin_device_verification_ip',
                'superadmin_device_verification_ua',
                'superadmin_device_verification_created_at',
            ]);
            
            return redirect()->route('superadmin.login')->withErrors([
                'verification_code' => 'Too many failed attempts. Please login again.'
            ]);
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
        $userId = session('superadmin_device_verification_user_id');
        $fingerprint = session('superadmin_device_verification_fingerprint');
        $ip = session('superadmin_device_verification_ip');
        $ua = session('superadmin_device_verification_ua');
        
        $deviceInfo = $this->parseUserAgent($ua);
        
        SuperadminDevice::updateOrCreate(
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
            'superadmin_device_verification_user_id',
            'superadmin_device_verification_code',
            'superadmin_device_verification_fingerprint',
            'superadmin_device_verification_ip',
            'superadmin_device_verification_ua',
            'superadmin_device_verification_created_at',
        ]);
        
        // Log the user in
        $superadmin = SuperAdmin::find($userId);
        Auth::guard('superadmin')->login($superadmin);

        SuperAdminActivityLogger::auth('Logged In', "Super Admin logged in: {$superadmin->name} ({$superadmin->email})");

        return redirect()->route('superadmin.dashboard');
    }

    public function resendVerificationCode()
    {
        $userId = session('superadmin_device_verification_user_id');
        
        if (!$userId) {
            return redirect()->route('superadmin.login');
        }
        
        // Rate limiting for resend attempts
        $rateLimitKey = 'superadmin_resend_attempts_' . md5($userId);
        $maxAttempts = 3;
        $lockoutMinutes = 15;

        $attempts = cache()->get($rateLimitKey, 0);
        if ($attempts >= $maxAttempts) {
            return redirect()->route('superadmin.login')->withErrors([
                'verification_code' => 'Too many resend attempts. Please login again.'
            ]);
        }
        
        // Increment resend rate limit
        cache()->put($rateLimitKey, $attempts + 1, now()->addMinutes($lockoutMinutes));
        
        $user = SuperAdmin::find($userId);
        
        // Generate new verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update session
        session([
            'superadmin_device_verification_code' => $verificationCode,
            'superadmin_device_verification_created_at' => now()->timestamp,
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
            \Log::error('Failed to resend superadmin verification email: ' . $e->getMessage());
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
        
        // Detect Browser
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
        
        // Detect OS
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

    public function logout()
    {
        $superadmin = Auth::guard('superadmin')->user();

        if ($superadmin) {
            SuperAdminActivityLogger::auth('Logged Out', "Super Admin logged out: {$superadmin->name} ({$superadmin->email})");
        }

        Auth::guard('superadmin')->logout();
        return redirect()->route('superadmin.login');
    }

    
}
    
