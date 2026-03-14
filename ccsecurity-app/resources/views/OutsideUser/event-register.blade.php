<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { max-width: 500px; width: 100%; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
        input:focus { outline: none; border-color: #007bff; }
        .btn { padding: 12px 24px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; width: 100%; }
        .btn-primary { background: #007bff; color: white; }
        .btn:disabled { background: #ccc; cursor: not-allowed; }
        .event-info { background: #e7f3ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .alert-error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
        .alert-warning { background: #fff3cd; border-left: 4px solid #ffc107; color: #856404; }
        .text-center { text-align: center; }
        .required { color: #dc3545; }
        .error { color: #dc3545; font-size: 12px; margin-top: 5px; }
        .nav-link { color: #007bff; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="text-center" style="margin-bottom: 20px;">
                <h1 style="margin: 0; font-size: 28px;">📅 Event Registration</h1>
                <p style="margin: 5px 0 0 0; color: #666;">Register for this event</p>
            </div>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="event-info">
                <h2 style="margin: 0 0 10px 0; font-size: 18px;">{{ $event->event_name }}</h2>
                <div style="font-size: 13px; color: #666; line-height: 1.8;">
                    <div>📅 {{ $event->event_date->format('l, F d, Y') }}</div>
                    <div>🕐 {{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}</div>
                    <div>👥 Slots: {{ $event->registrations_count }} / {{ $event->alien_user_limit }}</div>
                    <div>⏰ Registration ends: {{ $event->qr_request_deadline->format('M d, Y g:i A') }}</div>
                </div>
            </div>

            <form action="{{ route('public.event.register.submit', $event->id) }}" method="POST">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                
                <div class="form-group">
                    <label for="first_name">First Name <span class="required">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name <span class="required">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                </div>

                <button type="submit" class="btn btn-primary" {{ $isFull || $isClosed ? 'disabled' : '' }}>
                    @if($isFull)
                        Event is Full
                    @elseif($isClosed)
                        Registration Closed
                    @else
                        Register Now →
                    @endif
                </button>
            </form>

            <div class="text-center" style="margin-top: 20px;">
                <a href="{{ route('welcome') }}" class="nav-link">← Back to Home</a>
            </div>
        </div>

        <div class="alert alert-warning">
            <strong>Note:</strong> After registration, your QR code will be displayed on the screen and sent to your email. Save it for event check-in.
        </div>
    </div>
</body>
</html>
