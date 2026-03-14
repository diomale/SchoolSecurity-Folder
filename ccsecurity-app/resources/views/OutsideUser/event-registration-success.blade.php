<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { max-width: 500px; width: 100%; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; }
        .success-icon { width: 80px; height: 80px; color: #28a745; margin: 0 auto 20px; }
        .event-info { background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: left; }
        .qr-container { border: 2px dashed #ddd; padding: 20px; border-radius: 8px; display: inline-block; margin: 20px 0; }
        .btn { padding: 12px 24px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; margin: 5px; width: 100%; box-sizing: border-box; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .alert { padding: 15px; border-radius: 4px; margin-top: 20px; text-align: left; }
        .alert-warning { background: #fff3cd; border-left: 4px solid #ffc107; color: #856404; }
        .nav-link { color: #007bff; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <svg class="success-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>

            <h1 style="margin: 0 0 10px 0; font-size: 24px;">Registration Successful!</h1>
            <p style="margin: 0 0 20px 0; color: #666;">You are registered for <strong>{{ $event->event_name }}</strong></p>

            <div class="event-info">
                <h2 style="margin: 0 0 10px 0; font-size: 16px;">Event Details</h2>
                <div style="font-size: 13px; color: #666; line-height: 1.8;">
                    <div>📅 {{ $event->event_date->format('l, F d, Y') }}</div>
                    <div>🕐 {{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}</div>
                    <div>📍 School Security System Event</div>
                </div>
            </div>

            <div>
                <p style="margin: 0 0 10px 0; font-weight: 600;">Your QR Code</p>
                <div class="qr-container" style="border: none; padding: 0;">
                    {!! QrCode::size(200)->generate(route('security.event.scan', ['qr' => $registration->qr_code])) !!}
                </div>
                <p style="font-size: 12px; color: #666; font-family: monospace;">{{ $registration->qr_code }}</p>
            </div>

            <div>
                <a href="{{ route('insideuser.events.downloadQR', $registration->id) }}" target="_blank" class="btn btn-primary">📥 Download QR Code</a>
                <button onclick="window.print()" class="btn btn-secondary">🖨️ Print QR Code</button>
            </div>

            <div class="alert alert-warning">
                <strong>Important:</strong> A copy of your QR code has been sent to <strong>{{ $registration->email }}</strong>. Present this QR code at the event entrance for check-in.
            </div>

            <div style="margin-top: 20px;">
                <a href="{{ route('welcome') }}" class="nav-link">← Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
