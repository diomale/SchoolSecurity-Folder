<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    @vite(['resources/css/OutsideUSerStyleFolder/event_registration_success.css', 'resources/js/app.js'])
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
                        <span class="ers-icon">📅</span>
                        <span>{{ $event->event_date->format('l, F d, Y') }}</span>
                    </div>
                    <div class="ers-event-meta-row">
                        <span class="ers-icon">🕐</span>
                        <span>{{ $event->event_start_time->format('g:i A') }} – {{ $event->event_end_time->format('g:i A') }}</span>
                    </div>
                    <div class="ers-event-meta-row">
                        <span class="ers-icon">🏫</span>
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
                    ⬇ Download QR Code
                </a>
                <button onclick="window.print()" class="ers-btn ers-btn-secondary">
                    🖨 Print QR Code
                </button>
            </div>

            {{-- Warning --}}
            <div class="ers-alert-warning">
                <strong>Important:</strong> A copy of your QR code has been sent to <strong>{{ $registration->email }}</strong>. Present this QR code at the event entrance for check-in.
            </div>

            {{-- Back Link --}}
            <div class="ers-footer">
                <a href="{{ route('welcome') }}" class="ers-back-link">← Back to Home</a>
            </div>

        </div>{{-- /.ers-card --}}
    </div>{{-- /.ers-wrapper --}}
</body>
</html>
