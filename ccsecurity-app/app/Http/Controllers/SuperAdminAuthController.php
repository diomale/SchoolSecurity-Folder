<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdminAuthController extends Controller
{
    //Dashboard
    public function dashboard()
    {
        
        $admins = Admin::all(); 

        return view('superadmin.dashboard', compact('admins'));
    }

    // Create, Read, Update, Delete,
    public function showAddForm()
    {
        return view('Superadmin.SuperadminCrudSection.superadmin_add_form');
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|string|email|max:155|unique:mysql_second.admins,email',
            'password' => 'required|string|min:8|max:100'
        ]);

        Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 1,
        ]);

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'New Admin created successfully!');
    }

   
    public function showAdminDetails($id)
    {
        $admin = Admin::findOrFail($id);
        return view('Superadmin.SuperadminCrudSection.superadmin_details', compact('admin'));
    }

    public function deleteAdmin($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return redirect()->route('superadmin.dashboard')
        ->with('success', 'Admin deleted successfully!');
    }
    
    public function viewEditForm($id)
    {
        $admin = Admin::findOrFail($id);
        return view('Superadmin.SuperadminCrudSection.superadmin_edit', compact('admin'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:mysql_second.admins,email,' . $id,
            'password' => 'nullable|string|min:8',
            'status' => 'required|boolean', 
        ]);

        $data = $request->only(['name', 'email', 'status']);

        
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $admin->update($data);

        return redirect()->route('superadmin.dashboard')->with('Success', 'Admin updated successfully');
    }

    //login, logout

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
    