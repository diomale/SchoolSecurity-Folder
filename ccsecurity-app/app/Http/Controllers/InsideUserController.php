<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InsideUser;
use App\Models\UserDevice;
use App\Models\ParentChildConnection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeviceVerificationMail;

class InsideUserController extends Controller
{

    public function dashboard()
    {
        $insideUser = Auth::guard('insideuser')->user();
        
        // Check if user needs to accept terms
        $showTermsModal = is_null($insideUser->terms_accepted_at);
        
        // Cache dashboard data for 5 minutes to reduce database load
        // Priority 4: Query caching optimization
        $cacheKey = 'inside_user_dashboard_' . $insideUser->id . '_' . today()->toDateString();
        
        $data = Cache::remember($cacheKey, 300, function () use ($insideUser) {
            return [
                // Get approved parent connections
                'connectedParents' => $insideUser->connectedParents()->get(),
                
                // Get pending connection requests
                'pendingConnections' => $insideUser->pendingConnections()
                    ->with('outsideUser')
                    ->get(),
                
                // Get recent entry/exit logs (last 20)
                'entryLogs' => $insideUser->entryLogs()
                    ->with('securityGuardUser')
                    ->orderBy('scan_at', 'desc')
                    ->limit(20)
                    ->get(),
            ];
        });
        
        // Extract cached data
        $connectedParents = $data['connectedParents'];
        $pendingConnections = $data['pendingConnections'];
        $entryLogs = $data['entryLogs'];

        return view('InsideUser.dashboard', compact('insideUser', 'connectedParents', 'pendingConnections', 'entryLogs', 'showTermsModal'));
    }

    public function acceptTerms(Request $request)
    {
        $insideUser = Auth::guard('insideuser')->user();
        $insideUser->update(['terms_accepted_at' => now()]);
        
        return redirect()->route('insideuser.dashboard')
            ->with('success', 'Terms and Privacy Policy accepted successfully.');
    }

    public function userProfile()
    {
        $insideUser = Auth::guard('insideuser')->user();
        
        // Get approved parent connections
        $connectedParents = $insideUser->connectedParents()->get();
        
        return view('InsideUser.user_profile', compact('connectedParents'));
    }


    //Logout, Login
    public function showUserLogin()
    {
        return view('InsideUser.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required',
            'g-recaptcha-response' => ['required', new \App\Rules\Recaptcha],
        ]);

        // Rate limiting: Check for too many failed attempts
        $email = $request->email;
        $rateLimitKey = 'insideuser_login_attempts_' . md5($email);
        $maxAttempts = 5;
        $lockoutMinutes = 15;

        $attempts = cache()->get($rateLimitKey, 0);
        if ($attempts >= $maxAttempts) {
            return back()->withErrors([
                'email' => 'Too many failed attempts. Please try again in ' . $lockoutMinutes . ' minutes.'
            ])->withInput($request->only('email'));
        }

        if (Auth::guard('insideuser')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ])){
            $user = Auth::guard('insideuser')->user();
            
            // Reset rate limit on successful login
            cache()->forget($rateLimitKey);
            
            // Generate device fingerprint
            $fingerprint = $this->generateDeviceFingerprint($request);
            
            // Check if device is trusted
            $trustedDevice = UserDevice::where('inside_user_id', $user->id)
                ->where('device_fingerprint', $fingerprint)
                ->where('is_trusted', true)
                ->first();
            
            if (!$trustedDevice) {
                // New device - require verification
                Auth::guard('insideuser')->logout();
                
                // Generate verification code
                $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                
                // Store verification data in session
                $sessionData = [
                    'device_verification_user_id' => $user->id,
                    'device_verification_code' => $verificationCode,
                    'device_verification_fingerprint' => $fingerprint,
                    'device_verification_ip' => $request->ip(),
                    'device_verification_ua' => $request->userAgent(),
                    'device_verification_created_at' => now()->timestamp,
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
                    \Log::error('Failed to send inside user verification email: ' . $e->getMessage());
                }
                
                return redirect()->route('insideuser.device.verify.show');
            }
            
            // Trusted device - update last used
            $trustedDevice->update(['last_used_at' => now()]);

            return redirect()->route('insideuser.dashboard');
        }

        // Increment rate limit on failed login
        cache()->put($rateLimitKey, $attempts + 1, now()->addMinutes($lockoutMinutes));

        return back()->withErrors([
            'email' => 'invalid credentials'
        ])->withInput($request->only('email'));
    }

    public function showDeviceVerification()
    {
        $userId = session('device_verification_user_id');
        
        if (!$userId) {
            return redirect()->route('user.login.show');
        }
        
        $user = InsideUser::find($userId);
        
        if (!$user) {
            return redirect()->route('user.login.show');
        }
        
        return view('InsideUser.device-verification', compact('user'));
    }

    public function verifyDevice(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6',
        ]);
        
        $storedCode = session('device_verification_code');
        $createdAt = session('device_verification_created_at');
        
        // Check if code is expired (15 minutes)
        if ($createdAt && (now()->timestamp - $createdAt) > 900) {
            session()->forget([
                'device_verification_user_id',
                'device_verification_code',
                'device_verification_fingerprint',
                'device_verification_ip',
                'device_verification_ua',
                'device_verification_created_at',
            ]);
            
            return redirect()->route('user.login.show')->withErrors([
                'verification_code' => 'Verification code has expired. Please login again.'
            ]);
        }
        
        // Rate limiting for verification code attempts
        $rateLimitKey = 'insideuser_verify_attempts_' . md5(session('device_verification_user_id', 'unknown'));
        $maxAttempts = 5;
        $lockoutMinutes = 15;

        $attempts = cache()->get($rateLimitKey, 0);
        if ($attempts >= $maxAttempts) {
            session()->forget([
                'device_verification_user_id',
                'device_verification_code',
                'device_verification_fingerprint',
                'device_verification_ip',
                'device_verification_ua',
                'device_verification_created_at',
            ]);
            
            return redirect()->route('user.login.show')->withErrors([
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
        $userId = session('device_verification_user_id');
        $fingerprint = session('device_verification_fingerprint');
        $ip = session('device_verification_ip');
        $ua = session('device_verification_ua');
        
        $deviceInfo = $this->parseUserAgent($ua);
        
        UserDevice::updateOrCreate(
            [
                'inside_user_id' => $userId,
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
            'device_verification_user_id',
            'device_verification_code',
            'device_verification_fingerprint',
            'device_verification_ip',
            'device_verification_ua',
            'device_verification_created_at',
        ]);
        
        // Log the user in
        Auth::guard('insideuser')->login(InsideUser::find($userId));
        
        return redirect()->route('insideuser.dashboard');
    }

    public function resendVerificationCode()
    {
        $userId = session('device_verification_user_id');
        
        if (!$userId) {
            return redirect()->route('user.login.show');
        }
        
        // Rate limiting for resend attempts
        $rateLimitKey = 'insideuser_resend_attempts_' . md5($userId);
        $maxAttempts = 3;
        $lockoutMinutes = 15;

        $attempts = cache()->get($rateLimitKey, 0);
        if ($attempts >= $maxAttempts) {
            return redirect()->route('user.login.show')->withErrors([
                'verification_code' => 'Too many resend attempts. Please login again.'
            ]);
        }
        
        // Increment resend rate limit
        cache()->put($rateLimitKey, $attempts + 1, now()->addMinutes($lockoutMinutes));
        
        $user = InsideUser::find($userId);
        
        // Generate new verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update session
        session([
            'device_verification_code' => $verificationCode,
            'device_verification_created_at' => now()->timestamp,
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
            \Log::error('Failed to resend inside user verification email: ' . $e->getMessage());
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
        Auth::guard('insideuser')->logout();
        return redirect()->route('user.login.show');
    }

}
