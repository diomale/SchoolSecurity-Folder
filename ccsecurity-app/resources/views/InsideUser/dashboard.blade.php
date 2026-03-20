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
    @vite(['resources/css/InsideUserStyleFolder/insideuser_dashboard_style.css'])
</head>
<body>
    <div class="dashboard-container">
        
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-circle">CCSS</div>
                <h2 style="font-size:1.1rem; line-height:1.2;">Columban College<br><small style="font-weight: 500; font-size: 0.85rem; color: var(--text-muted);">Security System</small></h2>
            </div>

            <nav class="sidebar-nav">
                <button class="tab-button active" onclick="switchTab('overview')">
                    <span class="nav-icon">📊</span> Overview
                </button>
                <a href="{{ route('insideuser.profile.show') }}" class="nav-link">
                    <span class="nav-icon">👤</span> Profile
                </a>
                <a href="{{ route('insideuser.events.dashboard') }}" class="nav-link">
                    <span class="nav-icon">🎉</span> My Events
                </a>
                <a href="{{ route('insideuser.connection.requests') }}" class="nav-link">
                    <span class="nav-icon">🤝</span> Connection Requests
                    @if($pendingConnections->count() > 0)
                        <span class="notification-badge">{{ $pendingConnections->count() }}</span>
                    @endif
                </a>
                <a href="{{ route('insideuser.connected.parents') }}" class="nav-link">
                    <span class="nav-icon">👨‍👩‍👧</span> Connected Parents
                </a>
            </nav>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('insideuser.logout') }}" style="width: 100%;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="nav-icon">🚪</span> Logout
                    </button>
                </form>
            </div>
        </aside>

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
                            <div class="alert-icon">✓</div>
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
                            <div class="interior-icon text-purple">🎉</div>
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
                                <div class="empty-icon">📭</div>
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
