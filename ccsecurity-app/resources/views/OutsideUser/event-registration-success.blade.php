<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    @vite(['resources/css/OutsideUser/event_registration_success.css', 'resources/js/app.js'])
</head>
<body>
    <div class="ers-wrapper">
        <div class="ers-card">

            {{-- Success Icon --}}
            <div class="ers-icon-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="ers-title">Registration Successful!</h1>
            <p class="ers-subtitle">You are registered for <strong>{{ $event->event_name }}</strong></p>

            {{-- Event Details --}}
            <div class="ers-event-info">
                <h2>Event Details</h2>
                <div class="ers-event-meta">
                    <div class="ers-event-meta-row">
                        <span class="ers-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                        <span>@if($event->event_end_date && !$event->event_date->eq($event->event_end_date)){{ $event->event_date->format('l, F d') }} – {{ $event->event_end_date->format('l, F d, Y') }}@else{{ $event->event_date->format('l, F d, Y') }}@endif</span>
                    </div>
                    <div class="ers-event-meta-row">
                        <span class="ers-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                        <span>{{ $event->event_start_time->format('g:i A') }} – {{ $event->event_end_time->format('g:i A') }}</span>
                    </div>
                    <div class="ers-event-meta-row">
                        <span class="ers-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                        <span>School Security System Event</span>
                    </div>
                </div>
            </div>

            {{-- QR Code --}}
            <div class="ers-qr-section">
                <p class="ers-qr-label">Your QR Code</p>
                <div class="ers-qr-container">
                    {!! QrCode::size(200)->generate(route('security.event.scan', ['qr' => $registration->qr_code])) !!}
                </div>
                <p class="ers-qr-code">{{ $registration->qr_code }}</p>
            </div>

            {{-- Actions --}}
            <div class="ers-actions">
                <a href="{{ route('insideuser.events.downloadQR', $registration->id) }}" target="_blank" class="ers-btn ers-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download QR Code
                </a>
                <button onclick="window.print()" class="ers-btn ers-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Print QR Code
                </button>
            </div>

            {{-- Warning --}}
            <div class="ers-alert-warning">
                <strong>Important:</strong> A copy of your QR code has been sent to <strong>{{ $registration->email }}</strong>. Present this QR code at the event entrance for check-in.
            </div>

            {{-- Back Link --}}
            <div class="ers-footer">
                <a href="{{ route('welcome.page') }}" class="ers-back-link">Back to Home</a>
            </div>

        </div>{{-- /.ers-card --}}
    </div>{{-- /.ers-wrapper --}}
</body>
</html>
