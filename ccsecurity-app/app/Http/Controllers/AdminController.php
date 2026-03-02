<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\securityguard;
use App\Models\InsideUser;
use App\Models\OutsideUser;


class AdminController extends Controller
{
    //Dashboard
    public function dashboard(){
        return view('admin.dashboard');
    }

    //approved the outsider user

    public function ShowOutsiderList()
    {
        $outside_users = OutsideUser::all();
        return view('Admin.AdminWaitingList.outside_user_list', compact('outside_users'));

    }


    public function ApprovedOutsider($id)
    {
        $outside_user = OutsideUser::findOrFail($id);

        
        if ($outside_user->status === OutsideUser::STATUS_APPROVED) {
            return redirect()->back()->with('info', 'This user is already approved.');
        }

        $outside_user->update([
            'status' => OutsideUser::STATUS_APPROVED,
            'updated_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', "User {$outside_user->first_name} approved successfully!");
    }

    public function RejectOutsider($id)
    {
        $outside_user = OutsideUser::findOrFail($id);

        
        if ($outside_user->status === OutsideUser::STATUS_REJECTED) {
            return redirect()->back()->with('info', 'This user is already rejected.');
        }

        $outside_user->update([
            'status' => OutsideUser::STATUS_REJECTED,
            'updated_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', "User {$outside_user->first_name} rejected successfully!");
    }

    //Crud for Security Guard
    public function showSecurityUserCrud()
    {
        $security_guard_users = securityguard::all();
        return view('Admin.SecurityCrudSection.security_table_section', compact('security_guard_users'));
    }

    public function showAddSecurityGuardUser()
    {
        return view('Admin.SecurityCrudSection.security_add_section');
    }

    public function storeSecurityGuard(Request $request)
    {
        $validate = $request->validate([
            'first_name'=>'required|string|max:150',
            'last_name'=>'required|string|max:150',
            'email'=>'required|string|max:150',
            'password'=>'required|string|min:8',
        ]);

        securityguard::create([
            'first_name' => $validate['first_name'],
            'last_name' => $validate['last_name'],
            'fullname' => $validate['first_name'] . ' ' . $validate['last_name'],
            'email' => $validate['email'],
            'password' => Hash::make($validate['password']),
            'created_at' => now(),
            'updated_at' => now(),
            'status' => 1,
        ]);

        return redirect()->route('security.user.table.section')
            ->with('success', 'New user created successfully!');
    }

    //QR Status Management
    public function showQrStatusManagement(Request $request)
    {
        $query = InsideUser::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fullname', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%")
                  ->orWhere('qr_value', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        $inside_users = $query->orderBy('id', 'desc')->paginate(15);
        
        return view('Admin.QrStatusManagement.qr_status_management', compact('inside_users'));
    }

    public function toggleQrStatus($id)
    {
        $inside_user = InsideUser::findOrFail($id);
        
        // Toggle between 'active' and 'inactive' (case-insensitive)
        $newStatus = in_array(strtolower($inside_user->qr_status), ['active']) ? 'inactive' : 'active';
        
        $inside_user->update([
            'qr_status' => $newStatus,
            'updated_at' => now(),
        ]);
        
        return redirect()->back()->with('success', "QR status for {$inside_user->fullname} changed to {$newStatus}!");
    }

    public function bulkToggleQrStatus(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:inside_user,id'
        ]);
        
        $newStatus = $request->new_status ?? 'inactive';
        
        InsideUser::whereIn('id', $request->user_ids)->update([
            'qr_status' => $newStatus,
            'updated_at' => now(),
        ]);
        
        return redirect()->back()->with('success', "QR status updated for " . count($request->user_ids) . " users!");
    }

    public function showSecurityUserDetail($id)
    {
        $security_guard_user = securityguard::findOrFail($id);
        return view('Admin.SecurityCrudSection.security_user_details', compact('security_guard_user'));
    }

    public function viewSecurityUserForm($id)
    {
        $security_guard_user = securityguard::findOrFail($id);
        return view('Admin.SecurityCrudSection.security_user_edit_form', compact('security_guard_user'));
    }

    public function updateSecurityUser(Request $request, $id)
    {
        $security_guard_user = securityguard::findOrFail($id);

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

        $security_guard_user->update($data);

        return redirect()
        ->route('security.user.table.section')
        ->with('Success', 'Security Guard User updated successfully');
    }

    public function deleteSecurityUser($id)
    {
        $security_guard_user = securityguard::findOrFail($id);
        $security_guard_user->delete();

        return redirect()->route('security.user.table.section')->with('Success', 'Deleted Successfully');
    }

   
    //Create, Read, Update, Delete for inside user
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
            'first_name' => 'required|string|max:150',
            'last_name'  => 'required|string|max:150',
            'email'      => 'required|email|max:150|unique:mysql_second.inside_user,email',
            'password'   => 'required|string|min:8',
            'role'       => 'required|string|max:250',
            'qr_status'  => 'required|string|max:250',
        ]);

        InsideUser::create([
            'first_name' => $validate['first_name'],
            'last_name'  => $validate['last_name'],
            'fullname'   => $validate['first_name'] . ' ' . $validate['last_name'],
            'email'      => $validate['email'],
            'role'       => $validate['role'],
            'password'   => $validate['password'], 
            'created_at' => now(),
            'updated_at' => now(),
            'status'     => 1,
            'qr_status'  => $validate['qr_status'],
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

        return redirect()
        ->route('admin.show.crudSection')
        ->with('Success', 'User updated successfully');
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
            'email' => 'Invalid credentials'
        ]);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    
}
