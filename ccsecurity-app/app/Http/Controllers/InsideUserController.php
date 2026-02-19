<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InsideUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class InsideUserController extends Controller
{

    public function dashboard()
    {
        return view('InsideUser.dashboard');
    }

    public function userProfile()
    {
        return view('InsideUser.user_profile');
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
            'password'=>'required'
        ]);

        if (Auth::guard('insideuser')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ])){
            return redirect()->route('insideuser.dashboard');
        }
        
        return back()->withErrors([
            'email' => 'invalid credentials'
        ]);
    }

    public function logout()
    {
        Auth::guard('insideuser')->logout();
        return redirect()->route('user.login.show');
    }

}
