<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
    @vite(['resources/css/OutsideUser/event_registration.css', 'resources/js/app.js'])
</head>
<body>
    <div class="er-wrapper">
        <div class="er-card">

            {{-- Page Header --}}
            <div class="er-header">
                <h1>Event Registration</h1>
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
                        <span class="er-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                        <span>@if($event->event_end_date && !$event->event_date->eq($event->event_end_date)){{ $event->event_date->format('l, F d') }} – {{ $event->event_end_date->format('l, F d, Y') }}@else{{ $event->event_date->format('l, F d, Y') }}@endif</span>
                    </div>
                    <div class="er-event-meta-row">
                        <span class="er-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                        <span>{{ $event->event_start_time->format('g:i A') }} – {{ $event->event_end_time->format('g:i A') }}</span>
                    </div>
                    <div class="er-event-meta-row">
                        <span class="er-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                        <span>Slots: {{ $event->registrations_count }} / {{ $event->alien_user_limit }}</span>
                    </div>
                    <div class="er-event-meta-row">
                        <span class="er-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                        <span>Registration ends: {{ $event->qr_request_deadline->format('M d, Y g:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Registration Form --}}
            <form class="er-form" action="{{ route('public.event.register.submit', $event->id) }}" method="POST" id="registrationForm">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <input type="hidden" name="form_loaded_at" id="formLoadedAt" value="{{ now()->timestamp }}">

                {{-- Anti-brute-force: Honeypot (hidden from humans, bots will fill it) --}}
                <div style="position:absolute;left:-9999px;top:-9999px;opacity:0;height:0;width:0;overflow:hidden;" aria-hidden="true">
                    <label for="website">Do not fill this out</label>
                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>

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
                        Register Now
                    @endif
                </button>
            </form>

            {{-- Back Link --}}
            <div class="er-footer">
                <a href="{{ route('welcome.page') }}" class="er-back-link">Back to Home</a>
            </div>

            {{-- Security Note --}}
            <div style="text-align:center;font-size:0.75rem;color:#999;margin-top:12px;padding:0 16px;">
                This form is protected against automated submissions.
            </div>

        </div>{{-- /.er-card --}}

        {{-- Note Banner --}}
        <div class="er-note">
            <strong>Note:</strong> After registration, your QR code will be displayed on the screen and sent to your email. Save it for event check-in.
        </div>

    </div>{{-- /.er-wrapper --}}

    <script>
        document.getElementById('formLoadedAt').value = Math.floor(Date.now() / 1000);

        document.getElementById('registrationForm').addEventListener('submit', function() {
            var btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Submitting...';
        });
    </script>
</body>
</html>
