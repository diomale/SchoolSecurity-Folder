<?php

namespace App\Http\Controllers;

use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\OutsideUser;
use App\Models\VisitRequest;
use App\Models\Notification;
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

        return view('OutsideUser.dashboard', compact('visitRequests', 'pendingCount', 'unreadNotificationsCount', 'notifications'));
    }

    public function logout()
    {
        Auth::guard('outsideuser')->logout();
        return redirect()->route('outsideuser.login.show');
    }

    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::guard('outsideuser')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('outsider.dashboard'));
        }

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
        $validated = $request->validate([
            'first_name'           => 'required|string|max:150',
            'last_name'            => 'required|string|max:150',
            'email'                => 'required|string|email|max:155|unique:mysql_second.outside_user,email',
            'phone_number'         => 'required|string|max:20',
            'password'             => 'required|string|min:8|confirmed',
            'g-recaptcha-response' => ['required', new Recaptcha],
        ]);

      
        $qrValue = 'OUT-' . strtoupper(uniqid() . rand(1000, 9999));

        
        OutsideUser::create([
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password'     => Hash::make($validated['password']), 
            'qr_value'     => $qrValue,
            'qr_status'    => 'inactive',
            'status'       => OutsideUser::STATUS_PENDING,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->route('outsideuser.login.show')
            ->with('success', 'Account created! Please login and request a visit.');
    }

    /**
     * Show visit request form
     */
    public function showVisitRequest()
    {
        return view('OutsideUser.visit_request');
    }

    /**
     * Submit visit request
     */
    public function submitVisitRequest(Request $request)
    {
        $outsideUser = Auth::guard('outsideuser')->user();

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

        return redirect()->back()->with('success', 'Visit request submitted! Please wait for admin approval.');
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
}
