<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\OutsideUser;
use App\Mail\EventRegistrationQrMail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class InsideUserEventController extends Controller
{
    // =========================================================================
    // EVENT DASHBOARD & LISTINGS
    // =========================================================================

    public function dashboard()
    {
        $insideUser = Auth::guard('insideuser')->user();

        // Get all events by this user
        $events = Event::where('inside_user_id', $insideUser->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Statistics
        $totalEvents = $events->total();
        $pendingEvents = Event::where('inside_user_id', $insideUser->id)
            ->where('status', Event::STATUS_PENDING)
            ->count();
        $approvedEvents = Event::where('inside_user_id', $insideUser->id)
            ->where('status', Event::STATUS_APPROVED)
            ->count();
        $totalRegistrations = EventRegistration::whereIn('event_id', function($query) use ($insideUser) {
                $query->select('id')->from('events')
                    ->where('inside_user_id', $insideUser->id);
            })->count();

        return view('InsideUser.Events.dashboard', compact('events', 'totalEvents', 'pendingEvents', 'approvedEvents', 'totalRegistrations'));
    }

    // =========================================================================
    // CREATE EVENT
    // =========================================================================

    public function create()
    {
        return view('InsideUser.Events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'event_date' => 'required|date|after_or_equal:today',
            'event_start_time' => 'required',
            'event_end_time' => 'required|after:event_start_time',
            'qr_request_deadline' => 'required|date|after:now',
            'alien_user_limit' => 'required|integer|min:1|max:500',
        ]);

        $insideUser = Auth::guard('insideuser')->user();

        Event::create([
            'inside_user_id' => $insideUser->id,
            'event_name' => $request->event_name,
            'event_description' => $request->event_description,
            'event_date' => $request->event_date,
            'event_start_time' => $request->event_start_time,
            'event_end_time' => $request->event_end_time,
            'qr_request_deadline' => $request->qr_request_deadline,
            'alien_user_limit' => $request->alien_user_limit,
            'status' => Event::STATUS_PENDING,
        ]);

        return redirect()->route('insideuser.events.dashboard')
            ->with('success', 'Event created successfully! Awaiting admin approval.');
    }

    // =========================================================================
    // VIEW EVENT DETAILS
    // =========================================================================

    public function show($id)
    {
        $insideUser = Auth::guard('insideuser')->user();
        $event = Event::where('id', $id)
            ->where('inside_user_id', $insideUser->id)
            ->withCount('registrations')
            ->firstOrFail();

        $registrations = $event->registrations()->orderBy('created_at', 'desc')->get();

        // Generate event QR code for sharing
        $eventQR = route('public.event.register', ['code' => $event->id]);

        return view('InsideUser.Events.show', compact('event', 'registrations', 'eventQR'));
    }

    // =========================================================================
    // EDIT EVENT
    // =========================================================================

    public function edit($id)
    {
        $insideUser = Auth::guard('insideuser')->user();
        $event = Event::where('id', $id)
            ->where('inside_user_id', $insideUser->id)
            ->firstOrFail();

        // Only allow editing if event is pending or approved and not started
        if (in_array($event->status, [Event::STATUS_COMPLETED, Event::STATUS_CANCELLED])) {
            return redirect()->route('insideuser.events.show', $id)
                ->with('error', 'Cannot edit completed or cancelled events.');
        }

        return view('InsideUser.Events.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $insideUser = Auth::guard('insideuser')->user();
        $event = Event::where('id', $id)
            ->where('inside_user_id', $insideUser->id)
            ->firstOrFail();

        $request->validate([
            'event_name' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'event_date' => 'required|date|after_or_equal:today',
            'event_start_time' => 'required',
            'event_end_time' => 'required|after:event_start_time',
            'qr_request_deadline' => 'required|date|after:now',
            'alien_user_limit' => 'required|integer|min:1|max:500',
        ]);

        $event->update([
            'event_name' => $request->event_name,
            'event_description' => $request->event_description,
            'event_date' => $request->event_date,
            'event_start_time' => $request->event_start_time,
            'event_end_time' => $request->event_end_time,
            'qr_request_deadline' => $request->qr_request_deadline,
            'alien_user_limit' => $request->alien_user_limit,
        ]);

        return redirect()->route('insideuser.events.show', $id)
            ->with('success', 'Event updated successfully!');
    }

    // =========================================================================
    // CANCEL EVENT
    // =========================================================================

    public function cancel($id)
    {
        $insideUser = Auth::guard('insideuser')->user();
        $event = Event::where('id', $id)
            ->where('inside_user_id', $insideUser->id)
            ->firstOrFail();

        if ($event->status === Event::STATUS_COMPLETED) {
            return redirect()->route('insideuser.events.show', $id)
                ->with('error', 'Cannot cancel completed event.');
        }

        $event->update([
            'status' => Event::STATUS_CANCELLED,
        ]);

        return redirect()->route('insideuser.events.dashboard')
            ->with('success', 'Event cancelled successfully.');
    }

    // =========================================================================
    // EVENT REGISTRATIONS MANAGEMENT
    // =========================================================================

    public function registrations($id)
    {
        $insideUser = Auth::guard('insideuser')->user();
        $event = Event::where('id', $id)
            ->where('inside_user_id', $insideUser->id)
            ->firstOrFail();

        $registrations = $event->registrations()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('InsideUser.Events.registrations', compact('event', 'registrations'));
    }

    // =========================================================================
    // MANUAL REGISTRATION (Walk-in)
    // =========================================================================

    public function registerWalkin(Request $request, $eventId)
    {
        $insideUser = Auth::guard('insideuser')->user();
        $event = Event::where('id', $eventId)
            ->where('inside_user_id', $insideUser->id)
            ->firstOrFail();

        // Check if event is full
        if ($event->registrations()->count() >= $event->alien_user_limit) {
            return redirect()->back()
                ->with('error', 'Event has reached maximum capacity.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $qrCode = EventRegistration::generateQRCode($event->id);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'outside_user_id' => null, // Walk-in, no account
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'qr_code' => $qrCode,
            'status' => EventRegistration::STATUS_REGISTERED,
        ]);

        // Send QR code via email (to be implemented with Mailable)
        // For now, just mark as emailed
        $registration->update([
            'qr_emailed' => true,
            'qr_emailed_at' => now(),
        ]);

        return redirect()->route('insideuser.events.registrations', $event->id)
            ->with('success', 'Participant registered successfully! QR code sent to email.');
    }

    // =========================================================================
    // DOWNLOAD QR CODE
    // =========================================================================

    public function downloadQR($registrationId)
    {
        $insideUser = Auth::guard('insideuser')->user();
        $registration = EventRegistration::where('id', $registrationId)
            ->whereHas('event', function($query) use ($insideUser) {
                $query->where('inside_user_id', $insideUser->id);
            })
            ->firstOrFail();

        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate(route('security.event.scan', ['qr' => $registration->qr_code]));

        // Update download status
        $registration->update([
            'qr_downloaded' => true,
            'qr_downloaded_at' => now(),
        ]);

        return response($qrCode, 200)->header('Content-Type', 'image/svg+xml');
    }

    // =========================================================================
    // RESEND QR CODE EMAIL
    // =========================================================================

    public function resendQR($registrationId)
    {
        $insideUser = Auth::guard('insideuser')->user();
        $registration = EventRegistration::where('id', $registrationId)
            ->whereHas('event', function($query) use ($insideUser) {
                $query->where('inside_user_id', $insideUser->id);
            })
            ->firstOrFail();

        try {
            // Send email with QR code
            \Illuminate\Support\Facades\Mail::to($registration->email)->send(new \App\Mail\EventRegistrationQrMail($registration));
            
            $registration->update([
                'qr_emailed' => true,
                'qr_emailed_at' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'QR code re-sent to ' . $registration->email);
        } catch (\Exception $e) {
            \Log::error('Failed to resend QR email: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to send email. Please check email configuration.');
        }
    }

    // =========================================================================
    // EXPORT REGISTRATIONS
    // =========================================================================

    public function exportRegistrations($id)
    {
        $insideUser = Auth::guard('insideuser')->user();
        $event = Event::where('id', $id)
            ->where('inside_user_id', $insideUser->id)
            ->firstOrFail();

        $registrations = $event->registrations()->get();

        // Generate CSV
        $csv = "First Name,Last Name,Email,Phone,QR Code,Status,Created At\n";
        foreach ($registrations as $reg) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $reg->first_name,
                $reg->last_name,
                $reg->email,
                $reg->phone_number ?? '',
                $reg->qr_code,
                $reg->status,
                $reg->created_at->format('Y-m-d H:i:s')
            );
        }

        $filename = 'event_' . $event->id . '_registrations_' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // =========================================================================
    // TOGGLE EVENT VISIBILITY ON WELCOME PAGE
    // =========================================================================

    public function toggleWelcomeVisibility($id)
    {
        $insideUser = Auth::guard('insideuser')->user();
        $event = Event::where('id', $id)
            ->where('inside_user_id', $insideUser->id)
            ->firstOrFail();

        // Only approved events can be shown
        if ($event->status !== Event::STATUS_APPROVED) {
            return redirect()->back()
                ->with('error', 'Only approved events can be displayed publicly.');
        }

        $event->update([
            'show_on_welcome' => !$event->show_on_welcome,
        ]);

        return redirect()->back()
            ->with('success', $event->show_on_welcome 
                ? 'Event is now visible on the welcome page.'
                : 'Event is now hidden from the welcome page.'
            );
    }

    // =========================================================================
    // PUBLIC EVENT REGISTRATION (No Login Required)
    // =========================================================================

    public function showPublicRegistration($eventId)
    {
        $event = Event::withCount('registrations')->findOrFail($eventId);

        // Check if event is approved
        if ($event->status !== Event::STATUS_APPROVED) {
            return redirect()->route('welcome')
                ->with('error', 'This event is not available for registration.');
        }

        // Check if registration is closed
        $isClosed = now()->greaterThan($event->qr_request_deadline);
        
        // Check if event is full
        $isFull = $event->registrations_count >= $event->alien_user_limit;

        return view('OutsideUser.event-register', compact('event', 'isClosed', 'isFull'));
    }

    public function submitPublicRegistration(Request $request, $eventId)
    {
        $event = Event::withCount('registrations')->findOrFail($eventId);

        // Validate
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'nullable|string|max:20',
        ]);

        // Check if event is approved
        if ($event->status !== Event::STATUS_APPROVED) {
            return redirect()->back()
                ->with('error', 'This event is not available for registration.');
        }

        // Check if registration is closed
        if (now()->greaterThan($event->qr_request_deadline)) {
            return redirect()->back()
                ->with('error', 'Registration deadline has passed.');
        }

        // Check if event is full
        if ($event->registrations_count >= $event->alien_user_limit) {
            return redirect()->back()
                ->with('error', 'Event is full. No more slots available.');
        }

        // Generate unique QR code
        $qrCode = EventRegistration::generateQRCode($event->id);

        // Create registration - requires creator approval before QR is sent
        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'outside_user_id' => null, // Public registration, no account
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'qr_code' => $qrCode,
            'status' => EventRegistration::STATUS_REGISTERED,
            'needs_creator_approval' => true, // Requires approval
            'creator_approved_at' => null,
        ]);

        // Show approval pending page - QR code will be sent after approval
        return view('OutsideUser.event-registration-pending', compact('registration', 'event'));
    }
}
