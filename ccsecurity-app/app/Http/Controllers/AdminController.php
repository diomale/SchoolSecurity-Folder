<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\InsideUser;


class AdminController extends Controller
{
    //Dashboard
    public function dashboard(){
        return view('admin.dashboard');
    }


    //Create, Read, Update, Delete
    public function showCrudSection()
    {
        $inside_users = InsideUser::all();
        return view('Admin.AdminCrudSection.admin_crud', compact('inside_users'));
    }

    public function showAddUserForm()
    {
        return view('Admin.AdminCrudSection.inside_user_add_form');
    }

    public function storeUser(Request $request)
    {
        $validate = $request->validate([
            'first_name'=>'required|string|max:150',
            'last_name'=>'required|string|max:150',
            'email'=>'required|string|max:150',
            'password'=>'required|string|max:150',
            'role'=>'required|string|max:250',
        ]);

        InsideUser::create([
            'first_name' => $validate['first_name'],
            'last_name' => $validate['last_name'],
            'fullname' => $validate['first_name'] . ' ' . $validate['last_name'],
            'email' => $validate['email'],
            'role' => $validate['role'],
            'password' => Hash::make($validate['password']),
            'created_at' => now(),
            'updated_at' => now(),
            'status' => 1,
        ]);

        return redirect()->route('admin.show.crudSection')
            ->with('success', 'New user created successfully!');

    }

    public function showUserDetail($id)
    {
        $inside_user = InsideUser::findOrFail($id);
        return view('Admin.AdminCrudSection.admin_user_details', compact('inside_user'));
    }

    public function viewEditForm($id)
    {
        $inside_user = InsideUser::findOrFail($id);

        return view('Admin.AdminCrudSection.admin_user_edit_form', compact('inside_user'));
    }

    public function updateUser(Request $request, $id)
    {
        $inside_user = InsideUser::findOrFail($id);

        $request->validate([
            'first_name'=>'required|string|max:150',
            'last_name'=>'required|string|max:150',
            'email'=>'required|string|unique:mysql_second.inside_user,email,' . $id,
            'password' => 'nullable|string|min:8',
            'status' => 'required|boolean', 
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'status']);

        if ($request->filled('password')){
            $data['password'] = $request->password; 
        }

        $data['updated_at'] = now(); 

        $inside_user->update($data);

        return redirect()->route('admin.show.crudSection')->with('Success', 'User updated successfully');
    }

    public function deleteUser($id)
    {
        $inside_user = InsideUser::findOrFail($id);
        $inside_user->delete();

        return redirect()->route('admin.show.crudSection')->with('Success', 'Deleted Successfully');
    }




    // Login, Logout
    public function showAdminLogin()
    {
        return view('Admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ])) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or not a super admin'
        ]);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    
}
