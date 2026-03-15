<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Pending Approval</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { max-width: 500px; width: 100%; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; }
        .pending-icon { width: 80px; height: 80px; color: #ffc107; margin: 0 auto 20px; }
        .event-info { background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: left; }
        .btn { padding: 12px 24px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; margin: 5px; width: 100%; box-sizing: border-box; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .alert { padding: 15px; border-radius: 4px; margin-top: 20px; text-align: left; }
        .alert-info { background: #d1ecf1; border-left: 4px solid #17a2b8; color: #0c5460; }
        .alert-warning { background: #fff3cd; border-left: 4px solid #ffc107; color: #856404; }
        .nav-link { color: #007bff; text-decoration: none; font-size: 14px; }
        .steps { text-align: left; background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .step { display: flex; align-items: flex-start; margin-bottom: 15px; }
        .step-number { background: #007bff; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0; }
        .step-content { flex: 1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <svg class="pending-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>

            <h1 style="margin: 0 0 10px 0; font-size: 24px;">Registration Submitted!</h1>
            <p style="margin: 0 0 20px 0; color: #666;">Your registration for <strong>{{ $event->event_name }}</strong> is pending approval</p>

            <div class="event-info">
                <h2 style="margin: 0 0 10px 0; font-size: 16px;">Event Details</h2>
                <div style="font-size: 13px; color: #666; line-height: 1.8;">
                    <div><strong>Event:</strong> {{ $event->event_name }}</div>
                    <div><strong>Date:</strong> {{ $event->event_date->format('l, F d, Y') }}</div>
                    <div><strong>Time:</strong> {{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}</div>
                </div>
            </div>

            <div class="steps">
                <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #333;">What Happens Next?</h3>
                
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <strong>Your registration has been submitted</strong>
                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">The event creator has received your registration request.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <strong>Awaiting Creator Approval</strong>
                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">The event creator will review and approve your registration.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <strong>Receive QR Code via Email</strong>
                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">Once approved, your QR code will be sent to <strong>{{ $registration->email }}</strong></p>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>What to Expect:</strong> You will receive an email with your QR code once the event creator approves your registration. Please check your inbox (and spam folder) regularly.
            </div>

            <div class="alert alert-warning">
                <strong>Important:</strong> Do not share your registration details. The QR code will be unique to you and required for event check-in.
            </div>

            <div style="margin-top: 20px;">
                <a href="{{ route('welcome') }}" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
