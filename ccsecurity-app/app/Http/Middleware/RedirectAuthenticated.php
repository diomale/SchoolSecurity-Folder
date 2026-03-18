<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();
        
        // Check if user is authenticated with insideuser guard
        if (Auth::guard('insideuser')->check()) {
            if (str_starts_with($path, 'insideuser')) {
                return redirect()->route('insideuser.dashboard');
            }
        }
        
        // Check if user is authenticated with superadmin guard
        if (Auth::guard('superadmin')->check()) {
            if (str_starts_with($path, 'superadmin')) {
                return redirect()->route('superadmin.dashboard');
            }
        }
        
        // Check if user is authenticated with admin guard
        if (Auth::guard('admin')->check()) {
            if (str_starts_with($path, 'admin')) {
                return redirect()->route('admin.dashboard');
            }
        }
        
        // Check if user is authenticated with securityguard guard
        if (Auth::guard('securityguard')->check()) {
            if (str_starts_with($path, 'securityguard')) {
                return redirect()->route('security.dashboard');
            }
        }
        
        // Check if user is authenticated with outsideuser guard
        if (Auth::guard('outsideuser')->check()) {
            if (str_starts_with($path, 'outsideuser')) {
                return redirect()->route('outsider.dashboard');
            }
        }
        
        return $next($request);
    }
}
