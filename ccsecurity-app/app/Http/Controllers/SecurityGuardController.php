<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OutsideUser;

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


}
