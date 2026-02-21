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
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if (Auth::guard('securityguard')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ])){
            return redirect()->route('security.dashboard');
        }
        
        return back()->withErrors([
            'email' => 'invalid credentials'
        ]);
    }

    public function logout()
    {
        Auth::guard('securityguard')->logout();
        return redirect()->route('security.login.show');
    }



}
