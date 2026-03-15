<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventRegistration;

class AdminEventController extends Controller
{
    // =========================================================================
    // PENDING EVENTS FOR APPROVAL
    // =========================================================================

    public function pendingEvents(Request $request)
    {
        $query = Event::query()->pending();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event_name', 'LIKE', "%{$search}%")
                  ->orWhere('event_description', 'LIKE', "%{$search}%");
            });
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->where('event_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('event_date', '<=', $request->date_to);
        }

        $events = $query->with('insideUser')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('Admin.Events.pending', compact('events'));
    }

    // =========================================================================
    // VIEW EVENT DETAILS (Admin)
    // =========================================================================

    public function show($id)
    {
        $event = Event::where('id', $id)
            ->with('insideUser')
            ->withCount('registrations')
            ->firstOrFail();

        $recentRegistrations = $event->registrations()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('Admin.Events.show', compact('event', 'recentRegistrations'));
    }

    // =========================================================================
    // APPROVE EVENT
    // =========================================================================

    public function approve(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        $event->update([
            'status' => Event::STATUS_APPROVED,
            'admin_remarks' => $request->admin_remarks,
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.events.pending')
            ->with('success', 'Event approved successfully!');
    }

    // =========================================================================
    // REJECT EVENT
    // =========================================================================

    public function reject(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'admin_remarks' => 'required|string|max:500',
        ]);

        $event->update([
            'status' => Event::STATUS_REJECTED,
            'admin_remarks' => $request->admin_remarks,
        ]);

        return redirect()->route('admin.events.pending')
            ->with('success', 'Event rejected.');
    }

    // =========================================================================
    // ALL EVENTS (Admin View)
    // =========================================================================

    public function allEvents(Request $request)
    {
        $query = Event::query();

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event_name', 'LIKE', "%{$search}%")
                  ->orWhereHas('insideUser', function($q) use ($search) {
                      $q->where('fullname', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->where('event_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('event_date', '<=', $request->date_to);
        }

        $events = $query->with('insideUser')
            ->withCount('registrations')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $statistics = [
            'total' => Event::count(),
            'pending' => Event::where('status', Event::STATUS_PENDING)->count(),
            'approved' => Event::where('status', Event::STATUS_APPROVED)->count(),
            'rejected' => Event::where('status', Event::STATUS_REJECTED)->count(),
            'completed' => Event::where('status', Event::STATUS_COMPLETED)->count(),
        ];

        return view('Admin.Events.all-events', compact('events', 'statistics'));
    }

    // =========================================================================
    // BULK APPROVE EVENTS
    // =========================================================================

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'event_ids' => 'required|array',
            'event_ids.*' => 'exists:events,id',
        ]);

        Event::whereIn('id', $request->event_ids)->update([
            'status' => Event::STATUS_APPROVED,
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.events.pending')
            ->with('success', count($request->event_ids) . ' events approved successfully!');
    }

    // =========================================================================
    // BULK REJECT EVENTS
    // =========================================================================

    public function bulkReject(Request $request)
    {
        $request->validate([
            'event_ids' => 'required|array',
            'event_ids.*' => 'exists:events,id',
            'admin_remarks' => 'required|string|max:500',
        ]);

        Event::whereIn('id', $request->event_ids)->update([
            'status' => Event::STATUS_REJECTED,
            'admin_remarks' => $request->admin_remarks,
        ]);

        return redirect()->route('admin.events.pending')
            ->with('success', count($request->event_ids) . ' events rejected.');
    }

    // =========================================================================
    // MARK EVENT AS COMPLETED
    // =========================================================================

    public function markCompleted($id)
    {
        $event = Event::findOrFail($id);

        $event->update([
            'status' => Event::STATUS_COMPLETED,
        ]);

        return redirect()->route('admin.events.all')
            ->with('success', 'Event marked as completed.');
    }

    // =========================================================================
    // CANCEL EVENT (Admin)
    // =========================================================================

    public function cancel($id)
    {
        $event = Event::findOrFail($id);

        $event->update([
            'status' => Event::STATUS_CANCELLED,
        ]);

        return redirect()->route('admin.events.all')
            ->with('success', 'Event cancelled by admin.');
    }

    // =========================================================================
    // EVENT ANALYTICS
    // =========================================================================

    public function analytics()
    {
        $statistics = [
            'total_events' => Event::count(),
            'total_registrations' => EventRegistration::count(),
            'pending_events' => Event::where('status', Event::STATUS_PENDING)->count(),
            'approved_events' => Event::where('status', Event::STATUS_APPROVED)->count(),
            'today_events' => Event::where('event_date', today())->count(),
            'checked_in_today' => EventRegistration::whereHas('event', function($q) {
                $q->where('event_date', today());
            })->where('status', EventRegistration::STATUS_CHECKED_IN)->count(),
        ];

        // Top event creators
        $topCreators = Event::selectRaw('inside_user_id, COUNT(*) as event_count')
            ->groupBy('inside_user_id')
            ->with('insideUser')
            ->orderBy('event_count', 'desc')
            ->limit(5)
            ->get();

        // Recent events
        $recentEvents = Event::with('insideUser')
            ->withCount('registrations')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('Admin.Events.analytics', compact('statistics', 'topCreators', 'recentEvents'));
    }
}
