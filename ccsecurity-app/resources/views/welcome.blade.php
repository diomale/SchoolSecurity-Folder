<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Columban College Security System</title>
    <!-- Modern Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/welcome.css', 'resources/js/app.js'])
</head>
<body>
    <div class="welcome-container">
        <!-- Top Navigation / Header -->
        <header class="welcome-header">
            <div class="logo-area">
                <div class="logo-circle">CCSS</div>
                <h1 class="header-title">Columban College <span class="highlight">Security System</span></h1>
            </div>
        </header>

        <!-- MAIN FLEX CONTAINER -->
        <main class="main-content fade-in">
            <!-- LEFT PANEL: Tabs & Content -->
            <div class="left-panel glass-panel">
                
                <!-- Tab Buttons -->
                <div class="tab-buttons">
                    <button class="tab-button active" onclick="switchTab('login')">
                        <span class="tab-icon">🔒</span> Login Area
                    </button>
                    <button class="tab-button" onclick="switchTab('events')">
                        <span class="tab-icon">📅</span> Public Events 
                        @if(isset($publicEvents) && $publicEvents->count() > 0)
                            <span class="events-count">{{ $publicEvents->total() }}</span>
                        @endif
                    </button>
                </div>

                <!-- Login Tab Content -->
                <div id="login-tab" class="tab-content active">
                    
                    <div class="login-wrapper">
                        <div class="login-header">
                            <h2>Welcome Back</h2>
                            <p class="text-muted">Sign in to your account, Authorized Personnel Only, Student and staff.</p>
                        </div>

                        <form method="POST" action="{{ route('insideuser.login.submit') }}" class="login-form">
                            @csrf

                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" required placeholder="Enter your email">
                                @error('email')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required placeholder="Enter your password">
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary btn-block pulse-hover">Login</button>
                        </form>
                    </div>

                    <div class="divider"><span>OR</span></div>

                    <!-- Visitor + Student-Staff Navigation Cards -->
                    <div class="role-cards">
                        <div class="role-card">
                            <div class="role-icon">👤</div>
                            <div class="role-details">
                                <h3>Visitors & Guests</h3>
                                <p>Request visits & get QR access</p>
                                <div class="role-actions">
                                    <a href="{{ route('outsideuser.login.show') }}" class="btn btn-outline btn-sm">Sign In</a>
                                    <a href="{{ route('outsideuser.signup.show') }}" class="btn btn-ghost btn-sm">Register</a>
                                </div>
                            </div>
                        </div>

                        <div class="role-card">
                            <div class="role-icon">🎓</div>
                            <div class="role-details">
                                <h3>Students & Staff</h3>
                                <p>Access internal portal</p>
                                <div class="role-actions">
                                    <a href="{{ route('user.login.show') }}" class="btn btn-secondary-outline btn-sm">Internal Login</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Events Tab Content -->
                <div id="events-tab" class="tab-content">
                    <div class="events-header">
                        <h2>Upcoming Activities</h2>
                        <p class="text-muted">Register for our public campus events</p>
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
                                            <h3 class="event-title">{{ $event->event_name }}</h3>
                                        </div>
                                        <div class="event-card-body">
                                            <p class="event-description">
                                                {{ Str::limit($event->event_description, 90) ?? 'No description available.' }}
                                            </p>
                                            
                                            <div class="event-details-list">
                                                <div class="detail-item">
                                                    <span class="detail-label">Time:</span>
                                                    <span class="detail-value">{{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Deadline:</span>
                                                    <span class="detail-value text-danger">{{ $event->qr_request_deadline->format('M d, Y') }}</span>
                                                </div>
                                            </div>

                                            <div class="slots-progress">
                                                @php
                                                    $registeredCount = $event->registrations_count;
                                                    $availableSlots = $event->alien_user_limit - $registeredCount;
                                                    $percentFill = ($registeredCount / max($event->alien_user_limit, 1)) * 100;
                                                @endphp
                                                
                                                <div class="slots-header">
                                                    <span class="slots-text">
                                                        @if($availableSlots > 0)
                                                            <strong class="text-success">{{ $availableSlots }}</strong> spots left
                                                        @else
                                                            <strong class="text-danger">Event Full</strong>
                                                        @endif
                                                    </span>
                                                    <span class="slots-total">{{ $event->alien_user_limit }} total</span>
                                                </div>
                                                <div class="progress-bar">
                                                    <div class="progress-fill {{ $availableSlots == 0 ? 'full' : '' }}" style="width: {{ $percentFill }}%"></div>
                                                </div>
                                            </div>

                                            @if($event->insideUser)
                                                <p class="event-creator">Organized by {{ $event->insideUser->fullname }}</p>
                                            @endif
                                        </div>
                                        <div class="event-card-footer">
                                            @if($availableSlots > 0)
                                                <a href="{{ route('public.event.register', $event->id) }}" class="btn btn-success btn-block">Register Now</a>
                                            @else
                                                <button class="btn btn-disabled btn-block" disabled>Registration Closed</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            @if($publicEvents->hasMorePages())
                                <div class="pagination-wrapper">
                                    {{ $publicEvents->links() }}
                                </div>
                            @endif
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">🎉</div>
                                <h3>No Public Events Right Now</h3>
                                <p>Check back later for upcoming campus activities and public gatherings.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- RIGHT PANEL: Promotional/Branding Area -->
            <div class="right-panel">
                <div class="branding-content">
                    <h2>Welcome to<br>Columban College</h2>
                    <p>Experience a secure and seamless entry process designed for the safety of our students, staff, and valued guests.</p>
                    
                    <div class="features-list">
                        <div class="feature-item">
                            <span class="feature-icon">✨</span>
                            <span>Streamlined digital pass scanning</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">🛡️</span>
                            <span>Enhanced campus security protocols</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">📱</span>
                            <span>Easy mobile-friendly access requests</span>
                        </div>
                    </div>
                    
                    <!-- Include image if available -->
                    <!-- <img src="{{ asset('path/to/logo.png') }}" class="hero-image" alt="Columban Logo"> -->
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer class="welcome-footer">
            <p>&copy; {{ date('Y') }} Columban College Security System. All rights reserved.</p>
            <div class="footer-links">
                <a href="{{ route('privacy') }}">Privacy Policy</a>
                <span class="footer-divider">|</span>
                <a href="{{ route('terms') }}">Terms of Service</a>
            </div>
        </footer>
    </div>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(function(content) {
                content.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-button').forEach(function(button) {
                button.classList.remove('active');
            });
            
            document.getElementById(tabName + '-tab').classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>
