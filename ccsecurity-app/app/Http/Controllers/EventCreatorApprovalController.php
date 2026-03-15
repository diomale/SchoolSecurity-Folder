<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Mail\EventCreatorApprovalMail;

class EventCreatorApprovalController extends Controller
{
    /**
     * Display registrations pending approval for the event creator
     */
    public function pendingApprovals($eventId)
    {
        $insideUser = Auth::guard('insideuser')->user();
        
        $event = Event::where('id', $eventId)
            ->where('inside_user_id', $insideUser->id)
            ->firstOrFail();

        $pendingRegistrations = EventRegistration::where('event_id', $event->id)
            ->pendingApproval()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $approvedRegistrations = EventRegistration::where('event_id', $event->id)
            ->creatorApproved()
            ->orderBy('creator_approved_at', 'desc')
            ->paginate(20);

        $statistics = [
            'pending_count' => EventRegistration::where('event_id', $event->id)
                ->pendingApproval()
                ->count(),
            'approved_count' => EventRegistration::where('event_id', $event->id)
                ->creatorApproved()
                ->count(),
            'total_registrations' => EventRegistration::where('event_id', $event->id)->count(),
        ];

        return view('InsideUser.Events.approvals.pending', compact(
            'event',
            'pendingRegistrations',
            'approvedRegistrations',
            'statistics'
        ));
    }

    /**
     * Approve a registration and send QR code via email
     */
    public function approve($registrationId)
    {
        $insideUser = Auth::guard('insideuser')->user();
        
        $registration = EventRegistration::where('id', $registrationId)
            ->whereHas('event', function($query) use ($insideUser) {
                $query->where('inside_user_id', $insideUser->id);
            })
            ->firstOrFail();

        // Check if already approved
        if ($registration->isApprovedByCreator()) {
            return redirect()->back()
                ->with('info', 'This registration has already been approved.');
        }

        // Approve the registration
        $registration->approveByCreator();

        // Send QR code via email
        try {
            Mail::to($registration->email)->send(new EventCreatorApprovalMail($registration));
            
            $registration->update([
                'qr_emailed' => true,
                'qr_emailed_at' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Registration approved! QR code sent to ' . $registration->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send approval QR email: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('warning', 'Registration approved, but failed to send email. Please check email configuration.');
        }
    }

    /**
     * Reject a registration
     */
    public function reject(Request $request, $registrationId)
    {
        $insideUser = Auth::guard('insideuser')->user();
        
        $registration = EventRegistration::where('id', $registrationId)
            ->whereHas('event', function($query) use ($insideUser) {
                $query->where('inside_user_id', $insideUser->id);
            })
            ->firstOrFail();

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        // Reject the registration (keep needs_creator_approval true, but mark as rejected)
        $registration->rejectByCreator();
        
        // Optionally store rejection reason in a note (could add field later)
        if ($request->filled('rejection_reason')) {
            // Could add a rejection_reason field to the database
            \Log::info('Registration rejected: ' . $registration->id . ' - Reason: ' . $request->rejection_reason);
        }

        return redirect()->back()
            ->with('success', 'Registration rejected.');
    }

    /**
     * Bulk approve registrations
     */
    public function bulkApprove(Request $request, $eventId)
    {
        $insideUser = Auth::guard('insideuser')->user();
        
        $event = Event::where('id', $eventId)
            ->where('inside_user_id', $insideUser->id)
            ->firstOrFail();

        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:event_registrations,id',
        ]);

        $approvedCount = 0;
        $failedEmails = 0;

        foreach ($request->registration_ids as $registrationId) {
            $registration = EventRegistration::where('id', $registrationId)
                ->whereHas('event', function($query) use ($event) {
                    $query->where('id', $event->id);
                })
                ->first();

            if ($registration && $registration->needsApproval()) {
                $registration->approveByCreator();

                // Send email
                try {
                    Mail::to($registration->email)->send(new EventCreatorApprovalMail($registration));
                    $registration->update([
                        'qr_emailed' => true,
                        'qr_emailed_at' => now(),
                    ]);
                    $approvedCount++;
                } catch (\Exception $e) {
                    \Log::error('Failed to send bulk approval email: ' . $e->getMessage());
                    $failedEmails++;
                    $approvedCount++; // Still count as approved even if email fails
                }
            }
        }

        $message = $approvedCount . ' registration(s) approved.';
        if ($failedEmails > 0) {
            $message .= ' ' . $failedEmails . ' email(s) failed to send.';
        }

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Bulk reject registrations
     */
    public function bulkReject(Request $request, $eventId)
    {
        $insideUser = Auth::guard('insideuser')->user();
        
        $event = Event::where('id', $eventId)
            ->where('inside_user_id', $insideUser->id)
            ->firstOrFail();

        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:event_registrations,id',
        ]);

        $rejectedCount = 0;

        foreach ($request->registration_ids as $registrationId) {
            $registration = EventRegistration::where('id', $registrationId)
                ->whereHas('event', function($query) use ($event) {
                    $query->where('id', $event->id);
                })
                ->first();

            if ($registration && $registration->needsApproval()) {
                $registration->rejectByCreator();
                $rejectedCount++;
            }
        }

        return redirect()->back()
            ->with('success', $rejectedCount . ' registration(s) rejected.');
    }
}
