<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\InsideUser;
use App\Services\AdminActivityLogger;

class AdminEventPrivilegeController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::connection('mysql_second')->table('inside_user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('can_create_events', $request->status === 'granted' ? 1 : 0);
        }

        $users = $query->orderBy('fullname')->paginate(15)->withQueryString();

        $totalUsers = DB::connection('mysql_second')->table('inside_user')->count();
        $grantedUsers = DB::connection('mysql_second')->table('inside_user')->where('can_create_events', 1)->count();
        $deniedUsers = $totalUsers - $grantedUsers;

        return view('Admin.EventPrivileges.manage', compact('users', 'totalUsers', 'grantedUsers', 'deniedUsers'));
    }

    public function toggle($id)
    {
        $user = DB::connection('mysql_second')->table('inside_user')->where('id', $id)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $newStatus = !$user->can_create_events;

        DB::connection('mysql_second')->table('inside_user')
            ->where('id', $id)
            ->update(['can_create_events' => $newStatus]);

        $statusText = $newStatus ? 'granted' : 'revoked';

        AdminActivityLogger::eventManagement('Toggled Event Privilege', "Toggled event privilege for {$user->fullname} to {$statusText}", [
            'user_id' => $user->id,
            'name' => $user->fullname,
            'new_status' => $statusText,
        ]);

        return redirect()->back()->with('success', "Event creation privilege {$statusText} for {$user->fullname}.");
    }

    public function bulkToggle(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'action' => 'required|in:grant,revoke',
        ]);

        $newStatus = $request->action === 'grant' ? 1 : 0;

        DB::connection('mysql_second')->table('inside_user')
            ->whereIn('id', $request->user_ids)
            ->update(['can_create_events' => $newStatus]);

        $count = count($request->user_ids);
        $statusText = $newStatus ? 'granted to' : 'revoked from';

        AdminActivityLogger::eventManagement('Bulk Toggled Event Privilege', "Bulk {$statusText} event privilege for {$count} users", [
            'user_ids' => $request->user_ids,
            'count' => $count,
            'action' => $request->action,
        ]);

        return redirect()->back()->with('success', "Event creation privilege {$statusText} {$count} user(s).");
    }
}
