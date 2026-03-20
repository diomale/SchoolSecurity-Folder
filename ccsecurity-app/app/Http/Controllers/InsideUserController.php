<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InsideUser;
use App\Models\ParentChildConnection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;


class InsideUserController extends Controller
{

    public function dashboard()
    {
        $insideUser = Auth::guard('insideuser')->user();
        
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

        return view('InsideUser.dashboard', compact('insideUser', 'connectedParents', 'pendingConnections', 'entryLogs'));
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
