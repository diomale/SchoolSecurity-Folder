<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inside User Dashboard</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/InsideUser/insideuser_style_dashboard.css'])
</head>
<body>
    @if($showTermsModal)
    <!-- Mandatory Terms Acceptance Modal -->
    <div id="terms-modal" class="terms-modal-overlay active">
        <div class="terms-modal">
            <div class="terms-modal-header">
                <h2>Welcome to CCSS</h2>
            </div>
            <div class="terms-modal-body">
                <div class="terms-scroll-area">
                    <div class="terms-section">
                        <h3>Privacy Policy</h3>
                        <p>This Privacy Policy describes Our policies and procedures on the collection, use and disclosure of Your information when You use the Service and tells You about Your privacy rights and how the law protects You.</p>
                        <p>We use Your Personal Data to provide and improve the Service. By using the Service, You agree to the collection and use of information in accordance with this Privacy Policy.</p>
                        <h4>Personal Data We Collect</h4>
                        <ul>
                            <li>Email address</li>
                            <li>First name and last name</li>
                            <li>Phone number</li>
                            <li>Entry and exit records</li>
                        </ul>
                        <h4>How We Use Your Data</h4>
                        <ul>
                            <li>To provide and maintain our Service</li>
                            <li>To manage Your Account</li>
                            <li>To contact You regarding security updates</li>
                            <li>To maintain campus security logs</li>
                        </ul>
                        <p><a href="{{ route('privacy') }}" target="_blank">Read full Privacy Policy &rarr;</a></p>
                    </div>

                    <div class="terms-divider"></div>

                    <div class="terms-section">
                        <h3>Terms of Service</h3>
                        <p>By accessing and using the Columban College Security System, you agree to be bound by these Terms of Service. If you do not agree, please do not use the system.</p>
                        <h4>User Accounts</h4>
                        <ul>
                            <li>You must provide accurate information when creating an account.</li>
                            <li>You are responsible for maintaining the confidentiality of your password.</li>
                            <li>You must not share your account credentials with others.</li>
                        </ul>
                        <h4>Acceptable Use</h4>
                        <p>You agree to use the system only for its intended purpose: campus security and access management. You must not:</p>
                        <ul>
                            <li>Use the system for any unlawful purpose.</li>
                            <li>Attempt to access unauthorized areas of the system.</li>
                            <li>Share your QR codes with unauthorized individuals.</li>
                            <li>Provide false information during registration or check-in.</li>
                        </ul>
                        <h4>QR Code Policy</h4>
                        <ul>
                            <li>QR codes are issued for your personal use only.</li>
                            <li>Sharing QR codes with others is strictly prohibited.</li>
                            <li>Unauthorized use of QR codes may result in account suspension.</li>
                        </ul>
                        <p><a href="{{ route('terms') }}" target="_blank">Read full Terms of Service &rarr;</a></p>
                    </div>
                </div>
            </div>
            <div class="terms-modal-footer">
                <form action="{{ route('insideuser.accept.terms') }}" method="POST" id="accept-terms-form">
                    @csrf
                    <label class="checkbox-label">
                        <input type="checkbox" id="accept-terms-checkbox" required>
                        <span>I have read and agree to the Privacy Policy and Terms of Service</span>
                    </label>
                    <button type="submit" class="btn btn-primary" id="accept-terms-btn" disabled>
                        Accept & Continue
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="dashboard-container">
        
        <!-- Sidebar Navigation -->
        @include('InsideUser.partials.sidebar', ['activePage' => 'overview', 'pendingCount' => $pendingConnections->count()])

        <!-- Main Content Area -->
        <main class="main-content">
            
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">{{ auth('insideuser')->user()->role }} <span class="highlight">Portal</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Welcome back, <strong>{{ auth('insideuser')->user()->fullname }}</strong></p>
                </div>
            </header>

            <!-- Overview Content -->
            <div id="overview" class="tab-content active">
                
                <!-- Alerts / Info Boxes -->
                <div class="alerts-container fade-in" style="animation-delay: 0.2s;">
                    @if($pendingConnections->count() > 0)
                        <div class="alert alert-warning">
                            <div class="alert-icon">!</div>
                            <div class="alert-content">
                                <h3>Pending Connection Requests</h3>
                                <p>You have <strong>{{ $pendingConnections->count() }}</strong> pending connection request(s) waiting for your approval.</p>
                                <a href="{{ route('insideuser.connection.requests') }}" class="alert-link">View and respond to requests &rarr;</a>
                            </div>
                        </div>
                    @endif

                    @if($connectedParents->count() > 0)
                        <div class="alert alert-success">
                            <div class="alert-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
                            <div class="alert-content">
                                <h3>Connected Parents/Guardians</h3>
                                <p>You have <strong>{{ $connectedParents->count() }}</strong> connected parent(s) who can see your entry/exit records.</p>
                                <a href="{{ route('insideuser.connected.parents') }}" class="alert-link">View connected parents &rarr;</a>
                            </div>
                        </div>
                    @endif

                    @if($pendingConnections->count() === 0 && $connectedParents->count() === 0)
                        <div class="alert alert-info">
                            <div class="alert-icon">i</div>
                            <div class="alert-content">
                                <h3>No Connection Requests</h3>
                                <p>You don't have any pending connection requests yet. When someone (parent/guardian) requests to connect with you, you'll see it here.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="content-grid">
                    
                    <!-- My Events Section -->
                    <div class="glass-card fade-in" style="animation-delay: 0.3s;">
                        <div class="flex-between mb-4">
                            <h3 class="section-title">My Events</h3>
                            <a href="{{ route('insideuser.events.create') }}" class="btn btn-primary btn-sm">+ Create Event</a>
                        </div>
                        <div class="card-interior bg-purple-light">
                            <div class="interior-icon text-purple"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
                            <div class="interior-text">
                                <h4>Manage Your Events</h4>
                                <p>Create and manage events for alien user registration. Track registrations, approve participants, and generate QR codes.</p>
                                <a href="{{ route('insideuser.events.dashboard') }}" class="text-link text-purple">View All Events &rarr;</a>
                            </div>
                        </div>
                    </div>

                    <!-- Entry/Exit Logs Section -->
                    <div class="glass-card span-2 fade-in" style="animation-delay: 0.4s;" id="entry-logs">
                        <h3 class="section-title mb-4">Entry / Exit Logs</h3>
                        
                        @if($entryLogs->count() > 0)
                            <div class="table-responsive">
                                <table class="modern-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Scanned By</th>
                                            <th>Date & Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($entryLogs as $index => $log)
                                        <tr>
                                            <td class="text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                @if($log->scan_type === 'entry')
                                                    <span class="scan-badge scan-entry">ENTRY</span>
                                                @elseif($log->scan_type === 'exit')
                                                    <span class="scan-badge scan-exit">EXIT</span>
                                                @else
                                                    <span class="scan-badge scan-default">{{ strtoupper($log->scan_type) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->securityGuardUser)
                                                    <span class="guard-badge">{{ $log->securityGuardUser->fullname ?? 'Guard #' . $log->security_guard_user_id }}</span>
                                                @else
                                                    <span class="guard-badge italic">System</span>
                                                @endif
                                            </td>
                                            <td class="date-cell">
                                                <span class="date">{{ $log->scan_at->format('M d, Y') }}</span>
                                                <span class="time">{{ $log->scan_at->format('h:i A') }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="table-footer-note">Showing last {{ $entryLogs->count() }} records. Total: {{ $insideUser->entryLogs()->count() }} logs</p>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon"></div>
                                <h4>No Entry/Exit Records</h4>
                                <p>You don't have any entry or exit records yet. Your logs will appear here when you scan your QR code at the security checkpoint.</p>
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </main>
    </div>

    <script>
        @if($showTermsModal)
        // Terms modal logic
        document.addEventListener('DOMContentLoaded', function() {
            var checkbox = document.getElementById('accept-terms-checkbox');
            var btn = document.getElementById('accept-terms-btn');
            
            if (checkbox && btn) {
                checkbox.addEventListener('change', function() {
                    btn.disabled = !this.checked;
                });
            }
        });
        @endif

        function switchTab(tabName) {
            // Future-proofing script just in case more tabs load dynamically on same page.
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>
