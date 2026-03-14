<?php

namespace App\Http\Controllers;

use App\Models\ParentChildConnection;
use App\Models\InsideUser;
use App\Models\OutsideUser;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentConnectionController extends Controller
{
    /**
     * Show the connection request form
     */
    public function showRequestForm()
    {
        $outsideUser = Auth::guard('outsideuser')->user();
        
        // Get approved connections to show already connected children
        $connectedChildren = $outsideUser->connectedChildren()->get();
        
        // Get pending connection requests
        $pendingRequests = $outsideUser->pendingConnections()->with('insideUser')->get();
        
        return view('OutsideUser.request_connection', compact('connectedChildren', 'pendingRequests'));
    }

    /**
     * Search for inside users (students/children)
     */
    public function searchInsideUser(Request $request)
    {
        $query = $request->get('query', '');
        
        if (empty($query)) {
            return response()->json(['users' => []]);
        }

        $outsideUser = Auth::guard('outsideuser')->user();
        
        // Get already connected or pending inside user IDs
        $existingConnectionIds = ParentChildConnection::where('outside_user_id', $outsideUser->id)
            ->pluck('inside_user_id');

        // Search inside users
        $users = InsideUser::where(function ($q) use ($query) {
            $q->where('first_name', 'LIKE', "%{$query}%")
              ->orWhere('last_name', 'LIKE', "%{$query}%")
              ->orWhere('fullname', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%");
        })
        ->whereNotIn('id', $existingConnectionIds)
        ->limit(10)
        ->get(['id', 'first_name', 'last_name', 'fullname', 'email', 'qr_value']);

        return response()->json(['users' => $users]);
    }

    /**
     * Submit a connection request
     */
    public function submitConnectionRequest(Request $request)
    {
        $outsideUser = Auth::guard('outsideuser')->user();

        $validated = $request->validate([
            'inside_user_id' => 'required|integer',
            'relationship' => 'required|string|max:100',
        ]);

        // Verify the inside_user exists in mysql_second connection
        $insideUser = InsideUser::find($validated['inside_user_id']);
        if (!$insideUser) {
            return back()->withErrors([
                'inside_user_id' => 'The selected student does not exist.',
            ])->withInput();
        }

        // Check if connection already exists
        $existingConnection = ParentChildConnection::where('outside_user_id', $outsideUser->id)
            ->where('inside_user_id', $validated['inside_user_id'])
            ->first();

        if ($existingConnection) {
            return back()->withErrors([
                'inside_user_id' => 'You have already requested a connection with this user.',
            ])->withInput();
        }

        // Create connection request with pending inside user approval
        // No admin approval needed - automatically approved when inside user accepts
        ParentChildConnection::create([
            'outside_user_id' => $outsideUser->id,
            'inside_user_id' => $validated['inside_user_id'],
            'relationship' => $validated['relationship'],
            'status' => ParentChildConnection::STATUS_PENDING,
            'inside_user_approval' => ParentChildConnection::INSIDE_USER_PENDING,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('outsideuser.connections.request')
            ->with('success', 'Connection request sent! The student needs to accept your request.');
    }

    /**
     * Show connection history
     */
    public function connectionHistory()
    {
        $outsideUser = Auth::guard('outsideuser')->user();
        
        $connections = ParentChildConnection::where('outside_user_id', $outsideUser->id)
            ->with('insideUser')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('OutsideUser.connection_history', compact('connections'));
    }

    /**
     * Cancel a pending connection request
     */
    public function cancelConnection($id)
    {
        $outsideUser = Auth::guard('outsideuser')->user();

        $connection = ParentChildConnection::where('outside_user_id', $outsideUser->id)
            ->where('id', $id)
            ->where('status', ParentChildConnection::STATUS_PENDING)
            ->firstOrFail();

        $connection->delete();

        return redirect()->back()->with('success', 'Connection request cancelled.');
    }

    /**
     * Cancel an approved connection
     */
    public function cancelApprovedConnection($id)
    {
        $outsideUser = Auth::guard('outsideuser')->user();

        $connection = ParentChildConnection::where('outside_user_id', $outsideUser->id)
            ->where('id', $id)
            ->where('status', ParentChildConnection::STATUS_APPROVED)
            ->firstOrFail();

        $connection->delete();

        return redirect()->back()->with('success', 'Connection cancelled. You will no longer see entry/exit records for this student.');
    }
}
