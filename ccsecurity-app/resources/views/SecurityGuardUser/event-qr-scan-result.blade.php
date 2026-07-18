<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event QR Scan Result</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { max-width: 450px; width: 100%; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .icon { width: 60px; height: 60px; margin: 0 auto 20px; }
        .icon-success { color: #28a745; }
        .icon-info { color: #007bff; }
        .icon-error { color: #dc3545; }
        .info-box { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .event-box { background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .btn { padding: 12px 24px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; margin: 5px; width: 100%; box-sizing: border-box; text-align: center; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            @if($success === false)
            <!-- Error State -->
            <svg class="icon icon-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h1 class="text-center" style="margin: 0 0 10px 0;">{{ $message }}</h1>
            <p class="text-center" style="color: #666; margin: 0 0 20px 0;">{{ $details }}</p>
            <a href="{{ route('security.scanner.show') }}" class="btn btn-primary">Scan Another</a>
            @else
            <!-- Success State -->
            @if($action === 'checkin')
                <svg class="icon icon-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
            @elseif($action === 'checkout')
                <svg class="icon icon-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            @else
                <svg class="icon" style="color: #666;" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
            @endif

            <h1 class="text-center" style="margin: 0 0 10px 0;">{{ $message }}</h1>

            <div class="info-box">
                <h2 style="margin: 0 0 10px 0; font-size: 16px;">Participant Details</h2>
                <div class="info-row"><span style="color: #666;">Name:</span><strong>{{ $registration->fullname }}</strong></div>
                <div class="info-row"><span style="color: #666;">Email:</span><strong>{{ $registration->email }}</strong></div>
                <div class="info-row"><span style="color: #666;">Status:</span><strong>{{ ucfirst($registration->status) }}</strong></div>
            </div>

            <div class="event-box">
                <h2 style="margin: 0 0 8px 0; font-size: 16px;">{{ $event->event_name }}</h2>
                <div style="font-size: 13px; color: #666;">
                    <div>{{ $event->event_date->format('M d, Y') }}</div>
                    <div>{{ $event->event_start_time->format('g:i A') }}</div>
                </div>
            </div>

            <div style="font-size: 12px; color: #999; text-align: center; margin: 20px 0;">
                <div>Scanned at: {{ now()->format('M d, Y g:i A') }}</div>
                <div>by: {{ Auth::guard('securityguard')->user()->fullname }}</div>
            </div>

            <a href="{{ route('security.scanner.show') }}" class="btn btn-primary">Scan Another</a>
            <a href="{{ route('security.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            @endif
        </div>
    </div>
</body>
</html>
