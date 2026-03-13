<?php

namespace App\Http\Controllers;

use App\Models\ParentChildConnection;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsideUserConnectionController extends Controller
{
    /**
     * Show connection requests for the inside user
     */
    public function connectionRequests()
    {
        $insideUser = Auth::guard('insideuser')->user();

        // Get all connection requests for this inside user
        $connectionRequests = ParentChildConnection::where('inside_user_id', $insideUser->id)
            ->with('outsideUser')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get counts
        $pendingCount = ParentChildConnection::where('inside_user_id', $insideUser->id)
            ->where('inside_user_approval', ParentChildConnection::INSIDE_USER_PENDING)
            ->where('status', ParentChildConnection::STATUS_PENDING)
            ->count();

        $acceptedCount = ParentChildConnection::where('inside_user_id', $insideUser->id)
            ->where('inside_user_approval', ParentChildConnection::INSIDE_USER_ACCEPTED)
            ->count();

        $rejectedCount = ParentChildConnection::where('inside_user_id', $insideUser->id)
            ->where('inside_user_approval', ParentChildConnection::INSIDE_USER_REJECTED)
            ->count();

        return view('InsideUser.connection_requests', compact('connectionRequests', 'pendingCount', 'acceptedCount', 'rejectedCount'));
    }

    /**
     * Accept a connection request - Automatically approves the connection
     */
    public function acceptConnection($id)
    {
        $insideUser = Auth::guard('insideuser')->user();

        $connection = ParentChildConnection::where('inside_user_id', $insideUser->id)
            ->where('id', $id)
            ->where('inside_user_approval', ParentChildConnection::INSIDE_USER_PENDING)
            ->firstOrFail();

        // Accept the connection and automatically approve it (no admin needed)
        $connection->update([
            'inside_user_approval' => ParentChildConnection::INSIDE_USER_ACCEPTED,
            'status' => ParentChildConnection::STATUS_APPROVED,
            'approved_at' => now(),
        ]);

        // Notify the outside user (parent)
        Notification::create([
            'outside_user_id' => $connection->outside_user_id,
            'type' => 'connection_request_accepted',
            'title' => 'Connection Request Accepted',
            'message' => "{$insideUser->fullname} has accepted your connection request. You can now view their entry/exit records.",
            'related_type' => 'parent_child_connection',
            'related_id' => $connection->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Connection accepted! You are now connected.');
    }

    /**
     * Reject a connection request
     */
    public function rejectConnection($id, Request $request)
    {
        $insideUser = Auth::guard('insideuser')->user();

        $connection = ParentChildConnection::where('inside_user_id', $insideUser->id)
            ->where('id', $id)
            ->where('inside_user_approval', ParentChildConnection::INSIDE_USER_PENDING)
            ->firstOrFail();

        $remarks = $request->input('remarks', 'Rejected by student');

        $connection->update([
            'inside_user_approval' => ParentChildConnection::INSIDE_USER_REJECTED,
            'status' => ParentChildConnection::STATUS_REJECTED,
        ]);

        // Notify the outside user (parent)
        Notification::create([
            'outside_user_id' => $connection->outside_user_id,
            'type' => 'connection_request_rejected',
            'title' => 'Connection Request Rejected',
            'message' => "{$insideUser->fullname} has rejected your connection request. Remarks: {$remarks}",
            'related_type' => 'parent_child_connection',
            'related_id' => $connection->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Connection request rejected.');
    }

    /**
     * Show connected parents
     */
    public function connectedParents()
    {
        $insideUser = Auth::guard('insideuser')->user();

        // Get approved connections with connection ID
        $connectedParents = $insideUser->approvedConnections()
            ->with('outsideUser')
            ->get()
            ->map(function($connection) {
                // Add the connection ID to the outside user for easy access in the view
                $outsideUser = $connection->outsideUser;
                $outsideUser->connection_id = $connection->id;
                $outsideUser->pivot = (object) [
                    'relationship' => $connection->relationship,
                    'approved_at' => $connection->approved_at,
                    'id' => $connection->id
                ];
                return $outsideUser;
            });

        return view('InsideUser.connected_parents', compact('connectedParents'));
    }

    /**
     * Cancel a connection
     */
    public function cancelConnection($id)
    {
        $insideUser = Auth::guard('insideuser')->user();

        $connection = ParentChildConnection::where('inside_user_id', $insideUser->id)
            ->where('id', $id)
            ->where('status', ParentChildConnection::STATUS_APPROVED)
            ->firstOrFail();

        // Delete the connection
        $connection->delete();

        return redirect()->back()->with('success', 'Connection cancelled successfully.');
    }
}
