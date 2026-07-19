<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OutsideUserVerifyEmail;
use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Models\OutsideUser;
use App\Models\VisitRequest;
use App\Models\Notification;
use App\Models\ParentChildConnection;
use Carbon\Carbon;

class OutsideUserController extends Controller
{

    public function dashboard()
    {
        $outsideUser = Auth::guard('outsideuser')->user();
        
        // Generate QR value if missing
        if (!$outsideUser->qr_value) {
            $outsideUser->qr_value = 'OUT-' . strtoupper(uniqid() . rand(1000, 9999));
            $outsideUser->save();
        }
        
        // Cache dashboard data for 5 minutes to reduce database load
        // Priority 4: Query caching optimization
        $cacheKey = 'outside_user_dashboard_' . $outsideUser->id . '_' . today()->toDateString();
        
        $data = Cache::remember($cacheKey, 300, function () use ($outsideUser) {
            // Get user's visit requests
            $visitRequests = VisitRequest::where('outside_user_id', $outsideUser->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Get pending requests count
            $pendingCount = VisitRequest::where('outside_user_id', $outsideUser->id)
                ->where('status', 'pending')
                ->count();
            
            // Get unread notifications count
            $unreadNotificationsCount = Notification::where('outside_user_id', $outsideUser->id)
                ->where('is_read', false)
                ->count();
            
            // Get recent unread notifications only (hide read notifications)
            $notifications = Notification::where('outside_user_id', $outsideUser->id)
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Get connection requests count
            $pendingConnectionCount = ParentChildConnection::where('outside_user_id', $outsideUser->id)
                ->where('status', ParentChildConnection::STATUS_PENDING)
                ->count();
            
            // Get approved connections
            $approvedConnections = ParentChildConnection::where('outside_user_id', $outsideUser->id)
                ->where('status', ParentChildConnection::STATUS_APPROVED)
                ->with('insideUser')
                ->limit(3)
                ->get();
            
            // Get connected children IDs
            $connectedChildrenIds = ParentChildConnection::where('outside_user_id', $outsideUser->id)
                ->where('status', ParentChildConnection::STATUS_APPROVED)
                ->pluck('inside_user_id');
            
            // Get recent entry/exit logs for connected children
            $childrenEntryLogs = [];
            if ($connectedChildrenIds->count() > 0) {
                $childrenEntryLogs = \App\Models\EntryLog::whereIn('inside_user_id', $connectedChildrenIds)
                    ->with(['insideUser', 'securityGuardUser'])
                    ->orderBy('scan_at', 'desc')
                    ->limit(10)
                    ->get();
            }
            
            return [
                'visitRequests' => $visitRequests,
                'pendingCount' => $pendingCount,
                'unreadNotificationsCount' => $unreadNotificationsCount,
                'notifications' => $notifications,
                'pendingConnectionCount' => $pendingConnectionCount,
                'approvedConnections' => $approvedConnections,
                'childrenEntryLogs' => $childrenEntryLogs,
            ];
        });
        
        // Extract cached data
        $visitRequests = $data['visitRequests'];
        $pendingCount = $data['pendingCount'];
        $unreadNotificationsCount = $data['unreadNotificationsCount'];
        $notifications = $data['notifications'];
        $pendingConnectionCount = $data['pendingConnectionCount'];
        $approvedConnections = $data['approvedConnections'];
        $childrenEntryLogs = $data['childrenEntryLogs'];

        return view('OutsideUser.dashboard', compact('visitRequests', 'pendingCount', 'unreadNotificationsCount', 'notifications', 'pendingConnectionCount', 'approvedConnections', 'childrenEntryLogs'));
    }

    public function logout()
    {
        Auth::guard('outsideuser')->logout();
        return redirect()->route('outsideuser.login.show');
    }

    public function Login(Request $request)
    {
        // --- Anti-brute-force: Rate limit (5 attempts per IP per 15 min) ---
        $ip = $request->ip();
        $rateKey = "outsideuser_login:{$ip}";
        $attempts = (int) Cache::get($rateKey, 0);
        if ($attempts >= 5) {
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in 15 minutes.',
            ])->onlyInput('email');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::guard('outsideuser')->attempt($credentials)) {
            // Clear rate limit on successful login
            Cache::forget($rateKey);

            $user = Auth::guard('outsideuser')->user();
            
            // Check if email is verified
            if (!$user->email_verified_at) {
                Auth::guard('outsideuser')->logout();
                return redirect()->route('outsideuser.verify.notice')
                    ->with('email', $user->email);
            }
            
            // Check if account is approved
            if ($user->status !== OutsideUser::STATUS_APPROVED) {
                Auth::guard('outsideuser')->logout();
                return back()->withErrors([
                    'email' => 'Your account is pending admin approval. Please wait for approval before logging in.',
                ])->onlyInput('email');
            }
            
            $request->session()->regenerate();
            return redirect()->intended(route('outsider.dashboard'));
        }

        // Increment rate limit on failed login
        Cache::put($rateKey, $attempts + 1, now()->addMinutes(15));

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function ShowLogin()
    {
        return view('OutsideUser.login');
    }

    public function showSignup()
    {
        return view('OutsideUser.signup');
    }

    public function SignupRequest(Request $request)
    {
        // --- Anti-brute-force: Rate limit (5 signups per IP per 15 min) ---
        $ip = $request->ip();
        $rateKey = "outsideuser_signup:{$ip}";
        $attempts = (int) Cache::get($rateKey, 0);
        if ($attempts >= 5) {
            return back()->withErrors([
                'email' => 'Too many registration attempts. Please try again in 15 minutes.',
            ])->onlyInput('email');
        }

        $validated = $request->validate([
            'first_name'           => 'required|string|max:150',
            'last_name'            => 'required|string|max:150',
            'email'                => 'required|string|email|max:155|unique:mysql_second.outside_user,email',
            'phone_number'         => 'required|string|max:20',
            'password'             => 'required|string|min:8|confirmed',
            'g-recaptcha-response' => ['required', new Recaptcha],
            'agree_terms'          => 'accepted',
        ]);

        // Increment rate limit
        Cache::put($rateKey, $attempts + 1, now()->addMinutes(15));

        $qrValue = 'OUT-' . strtoupper(uniqid() . rand(1000, 9999));
        $verificationToken = Str::random(64);

        $user = OutsideUser::create([
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password'     => Hash::make($validated['password']),
            'qr_value'     => $qrValue,
            'qr_status'    => 'inactive',
            'status'       => OutsideUser::STATUS_APPROVED,
            'email_verification_token' => $verificationToken,
            'terms_accepted_at'    => now(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Send verification email
        $verificationUrl = route('outsideuser.verify.email', $verificationToken);
        try {
            Mail::to($user->email)->send(new OutsideUserVerifyEmail($user, $verificationUrl));
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email: ' . $e->getMessage());
        }

        return redirect()->route('outsideuser.verify.notice')
            ->with('email', $user->email);
    }

    /**
     * Show visit request form
     */
    public function showVisitRequest()
    {
        return view('OutsideUser.visit_request');
    }

    public function submitVisitRequest(Request $request)
    {
        $outsideUser = Auth::guard('outsideuser')->user();

        // Check if user already has an active visit request (pending or approved for today/future)
        $existingRequest = VisitRequest::where('outside_user_id', $outsideUser->id)
            ->where(function($query) {
                $query->where('status', 'pending')
                    ->orWhere(function($q) {
                        $q->where('status', 'approved')
                          ->where('visit_date', '>=', today());
                    });
            })
            ->first();

        if ($existingRequest) {
            $statusMsg = $existingRequest->status === 'pending' ? 'is still pending approval' : 'is already approved';
            return redirect()->back()->with('error', "You already have an active visit request that {$statusMsg}. Please wait for it to expire or be completed before requesting again.");
        }

        $validated = $request->validate([
            'visit_date' => 'required|date|after_or_equal:today',
            'visit_time' => 'required',
            'purpose' => 'required|string|max:500',
            'person_to_meet' => 'required|string|max:150',
        ]);

        VisitRequest::create([
            'outside_user_id' => $outsideUser->id,
            'visit_date' => $validated['visit_date'],
            'visit_time' => $validated['visit_time'],
            'purpose' => $validated['purpose'],
            'person_to_meet' => $validated['person_to_meet'],
            'status' => 'pending',
        ]);

        return redirect()->route('outsider.dashboard')->with('success', 'Visit request submitted! Please wait for admin approval.');
    }

    /**
     * Show visit history
     */
    public function visitHistory()
    {
        $outsideUser = Auth::guard('outsideuser')->user();
        
        $visitRequests = VisitRequest::where('outside_user_id', $outsideUser->id)
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        return view('OutsideUser.visit_history', compact('visitRequests'));
    }

    /**
     * Reactivate QR code (request new visit)
     */
    public function reactivateQR()
    {
        $outsideUser = Auth::guard('outsideuser')->user();
        
        // Check if user is approved
        if ($outsideUser->status != OutsideUser::STATUS_APPROVED) {
            return redirect()->back()->with('error', 'Your account is pending admin approval.');
        }

        return view('OutsideUser.visit_request');
    }

    /**
     * Show profile page
     */
    public function showProfile()
    {
        $outsideUser = Auth::guard('outsideuser')->user();
        return view('OutsideUser.profile', compact('outsideUser'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $outsideUser = Auth::guard('outsideuser')->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'phone_number' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'current_password' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Validate current password if new password is provided
        if ($request->filled('password')) {
            if (empty($request->current_password)) {
                return back()->withErrors(['current_password' => 'Current password is required to set a new password.'])
                    ->withInput();
            }

            if (!Hash::check($request->current_password, $outsideUser->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])
                    ->withInput();
            }
        }

        $outsideUser->first_name = $validated['first_name'];
        $outsideUser->last_name = $validated['last_name'];
        $outsideUser->fullname = $validated['first_name'] . ' ' . $validated['last_name'];
        $outsideUser->phone_number = $validated['phone_number'];

        if ($request->filled('password')) {
            $outsideUser->password = Hash::make($validated['password']);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($outsideUser->profile_picture && Storage::disk('public')->exists('profiles/' . $outsideUser->profile_picture)) {
                Storage::disk('public')->delete('profiles/' . $outsideUser->profile_picture);
            }

            // Store new profile picture
            $file = $request->file('profile_picture');
            $filename = 'profile_' . $outsideUser->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('profiles', $filename, 'public');

            $outsideUser->profile_picture = $filename;
        }

        // Handle profile picture removal
        if ($request->has('remove_profile_picture')) {
            if ($outsideUser->profile_picture && Storage::disk('public')->exists('profiles/' . $outsideUser->profile_picture)) {
                Storage::disk('public')->delete('profiles/' . $outsideUser->profile_picture);
            }
            $outsideUser->profile_picture = null;
        }

        $outsideUser->updated_at = now();
        $outsideUser->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Show all notifications
     */
    public function notifications()
    {
        $outsideUser = Auth::guard('outsideuser')->user();

        $notifications = Notification::where('outside_user_id', $outsideUser->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $unreadNotificationsCount = Notification::where('outside_user_id', $outsideUser->id)
            ->where('is_read', false)
            ->count();

        return view('OutsideUser.notifications', compact('notifications', 'unreadNotificationsCount'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $outsideUser = Auth::guard('outsideuser')->user();

        $notification = Notification::where('outside_user_id', $outsideUser->id)
            ->findOrFail($id);

        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $outsideUser = Auth::guard('outsideuser')->user();

        Notification::where('outside_user_id', $outsideUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    // =========================================================================
    // EMAIL VERIFICATION
    // =========================================================================

    /**
     * Show the "verify your email" notice page
     */
    public function showVerifyNotice()
    {
        return view('OutsideUser.verify-notice');
    }

    /**
     * Handle email verification when user clicks the link
     */
    public function verifyEmail($token)
    {
        $user = OutsideUser::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('outsideuser.login.show')
                ->with('error', 'Invalid verification link.');
        }

        if ($user->email_verified_at) {
            return redirect()->route('outsideuser.login.show')
                ->with('success', 'Email already verified. Please login.');
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);

        return redirect()->route('outsideuser.login.show')
            ->with('success', 'Email verified successfully! You can now login.');
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $email = $request->session()->get('email');

        if (!$email) {
            return redirect()->route('outsideuser.login.show');
        }

        $user = OutsideUser::where('email', $email)->first();

        if (!$user || $user->email_verified_at) {
            return redirect()->route('outsideuser.login.show');
        }

        $token = Str::random(64);
        $user->update(['email_verification_token' => $token]);

        $verificationUrl = route('outsideuser.verify.email', $token);
        try {
            Mail::to($user->email)->send(new OutsideUserVerifyEmail($user, $verificationUrl));
            return back()->with('success', 'Verification email resent. Check your inbox.');
        } catch (\Exception $e) {
            \Log::error('Failed to resend verification email: ' . $e->getMessage());
            return back()->with('error', 'Failed to send email. Please try again later.');
        }
    }
}
