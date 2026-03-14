<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InsideUser;
use App\Models\ParentChildConnection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class InsideUserController extends Controller
{

    public function dashboard()
    {
        $insideUser = Auth::guard('insideuser')->user();
        
        // Get approved parent connections
        $connectedParents = $insideUser->connectedParents()->get();
        
        // Get pending connection requests
        $pendingConnections = $insideUser->pendingConnections()->with('outsideUser')->get();
        
        return view('InsideUser.dashboard', compact('connectedParents', 'pendingConnections'));
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
