<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
    @vite(['resources/css/OutsideUSerStyleFolder/event_registration.css', 'resources/js/app.js'])
</head>
<body>
    <div class="er-wrapper">
        <div class="er-card">

            {{-- Page Header --}}
            <div class="er-header">
                <h1>🎟 Event Registration</h1>
                <p>Register for this event</p>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="er-alert er-alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="er-alert er-alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="er-alert er-alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Event Info --}}
            <div class="er-event-info">
                <h2>{{ $event->event_name }}</h2>
                <div class="er-event-meta">
                    <div class="er-event-meta-row">
                        <span class="er-icon">📅</span>
                        <span>{{ $event->event_date->format('l, F d, Y') }}</span>
                    </div>
                    <div class="er-event-meta-row">
                        <span class="er-icon">🕐</span>
                        <span>{{ $event->event_start_time->format('g:i A') }} – {{ $event->event_end_time->format('g:i A') }}</span>
                    </div>
                    <div class="er-event-meta-row">
                        <span class="er-icon">🪑</span>
                        <span>Slots: {{ $event->registrations_count }} / {{ $event->alien_user_limit }}</span>
                    </div>
                    <div class="er-event-meta-row">
                        <span class="er-icon">⏳</span>
                        <span>Registration ends: {{ $event->qr_request_deadline->format('M d, Y g:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Registration Form --}}
            <form class="er-form" action="{{ route('public.event.register.submit', $event->id) }}" method="POST">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <div class="er-form-group">
                    <label for="first_name">First Name <span class="er-required">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Enter your first name" required>
                </div>

                <div class="er-form-group">
                    <label for="last_name">Last Name <span class="er-required">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Enter your last name" required>
                </div>

                <div class="er-form-group">
                    <label for="email">Email <span class="er-required">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                </div>

                <div class="er-form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="e.g. 09xx xxx xxxx">
                </div>

                <button type="submit" class="er-btn er-btn-primary" {{ $isFull || $isClosed ? 'disabled' : '' }}>
                    @if($isFull)
                        Event is Full
                    @elseif($isClosed)
                        Registration Closed
                    @else
                        Register Now →
                    @endif
                </button>
            </form>

            {{-- Back Link --}}
            <div class="er-footer">
                <a href="{{ route('welcome') }}" class="er-back-link">← Back to Home</a>
            </div>

        </div>{{-- /.er-card --}}

        {{-- Note Banner --}}
        <div class="er-note">
            <strong>Note:</strong> After registration, your QR code will be displayed on the screen and sent to your email. Save it for event check-in.
        </div>

    </div>{{-- /.er-wrapper --}}
</body>
</html>
