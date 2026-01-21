<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('superadmin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'active'
        ])) {
            return redirect()->route('superadmin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or not a super admin'
        ]);
    }

    public function logout()
    {
        Auth::guard('superadmin')->logout();
        return redirect()->route('superadmin.login');
    }
}
