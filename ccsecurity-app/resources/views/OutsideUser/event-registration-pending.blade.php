<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Pending Approval</title>
    @vite(['resources/css/OutsideUSerStyleFolder/event_registration_pending.css', 'resources/js/app.js'])
</head>
<body>
    <div class="erp-wrapper">
        <div class="erp-card">

            {{-- Pending Icon --}}
            <div class="erp-icon-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="erp-title">Registration Submitted!</h1>
            <p class="erp-subtitle">
                Your registration for <strong>{{ $event->event_name }}</strong> is pending approval.
            </p>

            {{-- Event Details --}}
            <div class="erp-event-info">
                <h2>Event Details</h2>
                <div class="erp-event-meta">
                    <div class="erp-event-meta-row">
                        <strong>Event:</strong>
                        <span>{{ $event->event_name }}</span>
                    </div>
                    <div class="erp-event-meta-row">
                        <strong>Date:</strong>
                        <span>{{ $event->event_date->format('l, F d, Y') }}</span>
                    </div>
                    <div class="erp-event-meta-row">
                        <strong>Time:</strong>
                        <span>{{ $event->event_start_time->format('g:i A') }} – {{ $event->event_end_time->format('g:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Steps --}}
            <div class="erp-steps">
                <p class="erp-steps-title">What Happens Next?</p>

                <div class="erp-step">
                    <div class="erp-step-number">1</div>
                    <div class="erp-step-content">
                        <strong>Your registration has been submitted</strong>
                        <p>The event creator has received your registration request.</p>
                    </div>
                </div>

                <div class="erp-step">
                    <div class="erp-step-number">2</div>
                    <div class="erp-step-content">
                        <strong>Awaiting Creator Approval</strong>
                        <p>The event creator will review and approve your registration.</p>
                    </div>
                </div>

                <div class="erp-step">
                    <div class="erp-step-number">3</div>
                    <div class="erp-step-content">
                        <strong>Receive QR Code via Email</strong>
                        <p>Once approved, your QR code will be sent to <strong>{{ $registration->email }}</strong>.</p>
                    </div>
                </div>
            </div>

            {{-- Info Alerts --}}
            <div class="erp-alert erp-alert-info">
                <strong>What to Expect:</strong> You will receive an email with your QR code once the event creator approves your registration. Please check your inbox (and spam folder) regularly.
            </div>

            <div class="erp-alert erp-alert-warning">
                <strong>Important:</strong> Do not share your registration details. The QR code will be unique to you and required for event check-in.
            </div>

            {{-- Actions --}}
            <div class="erp-actions">
                <a href="{{ route('welcome') }}" class="erp-btn erp-btn-primary">← Back to Home</a>
            </div>

        </div>{{-- /.erp-card --}}
    </div>{{-- /.erp-wrapper --}}
</body>
</html>
