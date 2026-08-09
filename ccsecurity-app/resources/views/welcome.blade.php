<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KitaKits</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/welcome.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="welcome-container">
        <!-- Header -->
        <header class="welcome-header">
            <div class="logo-area">
                <h1 class="header-title">KitaKits: Columban College <span class="highlight">Security System</span></h1>
            </div>
            <a href="{{ route('login.choice') }}" class="btn-header-login">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                Login
            </a>
        </header>

        <!-- Main Content -->
        <main class="main-content fade-in">
            <!-- Left Panel -->
            <div class="left-panel glass-panel">
                <!-- Events -->
                <div class="events-header">
                    <h2>Upcoming Activities</h2>
                    <p class="text-muted">Register for public campus events</p>
                </div>

                <div class="events-container">
                    @if(isset($publicEvents) && $publicEvents->count() > 0)
                        <div class="events-grid">
                            @foreach($publicEvents as $event)
                                <div class="event-card">
                                    <div class="event-card-header">
                                        <div class="event-date">
                                            <span class="day">{{ $event->event_date->format('d') }}</span>
                                            <span class="month">{{ $event->event_date->format('M') }}</span>
                                        </div>
                                        <div>
                                            <h3 class="event-title">{{ $event->event_name }}</h3>
                                            @if($event->insideUser)
                                                <p class="event-creator">by {{ $event->insideUser->fullname }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="event-card-body">
                                        <p class="event-description">{{ Str::limit($event->event_description, 100) ?? 'No description available.' }}</p>

                                        <div class="event-details-list">
                                            <div class="detail-item">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                <span>@if($event->event_end_date && !$event->event_date->eq($event->event_end_date)){{ $event->event_date->format('M d') }} – {{ $event->event_end_date->format('M d, Y') }}@else{{ $event->event_date->format('M d, Y') }}@endif</span>
                                            </div>
                                            <div class="detail-item">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <span>{{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                <span>Deadline: {{ $event->qr_request_deadline->format('M d, Y') }}</span>
                                            </div>
                                        </div>

                                        @php
                                            $registeredCount = $event->registrations_count;
                                            $availableSlots = $event->alien_user_limit - $registeredCount;
                                            $percentFill = ($registeredCount / max($event->alien_user_limit, 1)) * 100;
                                        @endphp

                                        <div class="slots-progress">
                                            <div class="slots-header">
                                                <span class="slots-text">
                                                    @if($availableSlots > 0)
                                                        <strong class="text-success">{{ $availableSlots }}</strong> spots left
                                                    @else
                                                        <strong class="text-danger">Event Full</strong>
                                                    @endif
                                                </span>
                                                <span class="slots-total">of {{ $event->alien_user_limit }}</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill {{ $availableSlots == 0 ? 'full' : '' }}" style="width: {{ $percentFill }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="event-card-footer">
                                        @if($availableSlots > 0)
                                            <a href="{{ route('public.event.register', $event->id) }}" class="btn btn-success btn-block">
                                                Register Now
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                            </a>
                                        @else
                                            <button class="btn btn-disabled btn-block" disabled>Registration Closed</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($publicEvents->hasMorePages())
                            <div class="pagination-wrapper">
                                {{ $publicEvents->links() }}
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </div>
                            <h3>No Public Events Right Now</h3>
                            <p>Check back later for upcoming campus activities and public gatherings.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Panel -->
            <div class="right-panel">
                <div class="branding-content">
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon-wrap fi-1">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            </div>
                            <span>QR-based digital pass scanning</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon-wrap fi-2">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <span>Enhanced campus security protocols</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon-wrap fi-3">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                            </div>
                            <span>Mobile-friendly access requests</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="welcome-footer">
            <p>&copy; {{ date('Y') }} KitaKits: Columban College Security System</p>
            <div class="footer-links">
                <a href="{{ route('privacy') }}">Privacy Policy</a>
                <span class="footer-divider">|</span>
                <a href="{{ route('terms') }}">Terms of Service</a>
            </div>
        </footer>
    </div>
</body>
</html>
