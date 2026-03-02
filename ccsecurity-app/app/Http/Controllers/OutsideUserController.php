<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\OutsideUser;
use App\Models\VisitRequest;
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

        return view('OutsideUser.dashboard', compact('visitRequests', 'pendingCount'));
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
            'g-recaptcha-response' => 'required',
        ]);

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);


        if ($response->failed() || !$response->json('success')) {
            return back()
                ->withErrors(['captcha' => 'Captcha verification failed. Please try again.'])
                ->withInput();
        }

        // Generate unique QR value
        $qrValue = 'OUT-' . strtoupper(uniqid() . rand(1000, 9999));

        OutsideUser::create([
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'fullname'     => $validated['first_name'] . ' ' . $validated['last_name'],
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
            ->with('success', 'Account created! Please login and request a visit for QR activation.');
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
}
