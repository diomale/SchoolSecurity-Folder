<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Guard Dashboard</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardUser/securityguard_style_dashboard.css'])
</head>
<body>
    <div class="dashboard-container">
        
        <!-- Sidebar Navigation -->
        @include('SecurityGuardUser.partials.sidebar', ['activePage' => 'dashboard'])

        <!-- Main Content Area -->
        <main class="main-content">
            
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Security <span class="highlight">Command</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Welcome back, <strong>{{ $guard->fullname }}</strong></p>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success fade-in">
                    <div class="alert-icon">✓</div>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error fade-in">
                    <div class="alert-icon">!</div>
                    <div class="alert-content">{{ session('error') }}</div>
                </div>
            @endif

            <!-- Dashboard Tab (Overview) -->
            <div id="dashboard" class="tab-content active">
                
                <div class="stats-grid mb-4">
                    <div class="stat-card fade-in" style="animation-delay: 0.1s;">
                        <div class="stat-icon bg-primary">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value">{{ $todayScans }}</span>
                            <span class="stat-label">Total Scans Today</span>
                        </div>
                    </div>
                    <div class="stat-card fade-in" style="animation-delay: 0.2s;">
                        <div class="stat-icon bg-success">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value text-success">{{ $todayEntries }}</span>
                            <span class="stat-label">Entries Today</span>
                        </div>
                    </div>
                    <div class="stat-card fade-in" style="animation-delay: 0.3s;">
                        <div class="stat-icon bg-warning">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value text-warning">{{ $todayExits }}</span>
                            <span class="stat-label">Exits Today</span>
                        </div>
                    </div>
                    <div class="stat-card fade-in" style="animation-delay: 0.4s;">
                        <div class="stat-icon bg-info">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value text-primary">{{ $totalScans }}</span>
                            <span class="stat-label">All-Time Scans</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card full-width fade-in" style="animation-delay: 0.5s;">
                    <h3>Quick Actions</h3>
                    <div class="actions-grid">
                        <a href="{{ route('security.scanner.show') }}" class="action-card">
                            <div class="action-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><line x1="7" y1="12" x2="17" y2="12"/></svg>
                            </div>
                            <div class="action-text">
                                <h4>QR Scanner</h4>
                                <p>Scan user QR codes for Entry/Exit</p>
                            </div>
                        </a>
                        <a href="{{ route('security.quick-pass.list') }}" class="action-card">
                            <div class="action-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            </div>
                            <div class="action-text">
                                <h4>Quick Pass</h4>
                                <p>Temporary same-day visitor passes</p>
                            </div>
                        </a>
                        <a href="{{ route('security.entry.logs') }}" class="action-card">
                            <div class="action-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            </div>
                            <div class="action-text">
                                <h4>Entry/Exit Logs</h4>
                                <p>View real-time campus movement</p>
                            </div>
                        </a>
                        <a href="{{ route('security.walkin.list') }}" class="action-card">
                            <div class="action-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            </div>
                            <div class="action-text">
                                <h4>Walk-in Visitors</h4>
                                <p>Manage manual guest registrations</p>
                            </div>
                        </a>
                        <a href="{{ route('security.shift.management') }}" class="action-card">
                            <div class="action-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <div class="action-text">
                                <h4>Shift Management</h4>
                                <p>Clock in/out or view your schedules</p>
                            </div>
                        </a>
                        <a href="{{ route('security.qr.status.management') }}" class="action-card">
                            <div class="action-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            </div>
                            <div class="action-text">
                                <h4>QR Status Manager</h4>
                                <p>Activate or block user access</p>
                            </div>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Profile Tab -->
            <div id="profile" class="tab-content">
                <div class="profile-layout">
                    
                    <div class="glass-card fade-in">
                        <h3>Profile Information</h3>
                        <div class="profile-details">
                            <div class="detail-row">
                                <span class="detail-label">Full Name</span>
                                <span class="detail-value">{{ $guard->fullname }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">First Name</span>
                                <span class="detail-value">{{ $guard->first_name }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Last Name</span>
                                <span class="detail-value">{{ $guard->last_name }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Email</span>
                                <span class="detail-value">{{ $guard->email }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status</span>
                                @if($guard->status == 1)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="glass-card fade-in" style="animation-delay: 0.1s;">
                        <h3>My Performance</h3>
                        <div class="performance-stats">
                            <div class="perf-stat">
                                <div class="perf-value">{{ $todayScans }}</div>
                                <div class="perf-label">Scans Today</div>
                            </div>
                            <div class="perf-stat">
                                <div class="perf-value text-success">{{ $todayEntries }}</div>
                                <div class="perf-label">Entries Today</div>
                            </div>
                            <div class="perf-stat">
                                <div class="perf-value text-warning">{{ $todayExits }}</div>
                                <div class="perf-label">Exits Today</div>
                            </div>
                            <div class="perf-stat full-row">
                                <div class="perf-value text-primary">{{ $totalScans }}</div>
                                <div class="perf-label">Total Lifetime Scans</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Notifications Tab -->
            <div id="notifications" class="tab-content">
                <div class="glass-card fade-in">
                    <div class="flex-between">
                        <h3>Recent Activities</h3>
                        <p class="text-muted" style="font-size: 0.9rem;">Global security scans & status updates</p>
                    </div>

                    @if(isset($recentActivities) && $recentActivities->count() > 0)
                    <div class="table-responsive mt-4">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Activity</th>
                                    <th>Scanned By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivities as $activity)
                                <tr>
                                    <td class="date-cell">
                                        <span class="date">{{ \Carbon\Carbon::parse($activity->scan_at)->format('M d, Y') }}</span>
                                        <span class="time">{{ \Carbon\Carbon::parse($activity->scan_at)->format('h:i A') }}</span>
                                    </td>
                                    <td>
                                        <div class="user-cell">
                                            @php
                                                $uName = 'N/A';
                                                if($activity->eventRegistration) $uName = $activity->eventRegistration->fullname;
                                                elseif($activity->insideUser) $uName = $activity->insideUser->fullname;
                                                elseif($activity->outsideUser) $uName = $activity->outsideUser->fullname ?? ($activity->outsideUser->first_name . ' ' . $activity->outsideUser->last_name);
                                            @endphp
                                            <span style="color: #000; font-weight: 600;">{{ $uName }}</span>
                                            <div class="user-info">
                                                <span class="full-name">{{ $uName }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-outline">
                                            @if($activity->eventRegistration) Event
                                            @elseif($activity->insideUser) Inside User
                                            @elseif($activity->outsideUser) Visitor
                                            @else Status @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if(str_starts_with($activity->scan_type, 'qr_'))
                                            @php $status = str_replace('qr_', '', $activity->scan_type); @endphp
                                            <span class="badge badge-warning">QR {{ strtoupper($status) }}</span>
                                        @elseif($activity->scan_type === 'entry')
                                             <span class="scan-badge scan-entry">Entry</span>
                                        @elseif($activity->scan_type === 'exit')
                                             <span class="scan-badge scan-exit">Exit</span>
                                        @else
                                            <span class="badge">{{ $activity->scan_type }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $guardName = 'Unknown';
                                            if ($activity->securityGuardUser) {
                                                $guardName = $activity->securityGuardUser->fullname;
                                                if (empty($guardName)) {
                                                    $guardName = trim($activity->securityGuardUser->first_name . ' ' . $activity->securityGuardUser->last_name);
                                                }
                                                if (empty($guardName)) {
                                                    $guardName = 'Guard #' . $activity->security_guard_user_id;
                                                }
                                            } else {
                                                $guardName = 'Guard #' . $activity->security_guard_user_id;
                                            }
                                        @endphp
                                        <span class="guard-badge">{{ $guardName }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 8h10"/><path d="M7 12h6"/></svg>
                        </div>
                        <p>No recent activities found.</p>
                        <span class="suggestion">All global scans will appear here.</span>
                    </div>
                    @endif
                </div>
            </div>

        </main>
    </div>

    <script>
        // Check local storage for active tab or default to 'dashboard'
        document.addEventListener('DOMContentLoaded', function() {
            var activeTab = localStorage.getItem('securityGuardActiveTab') || 'dashboard';
            switchTab(activeTab);
            
            // Highlight the initial tab
            var navButtons = document.querySelectorAll('.tab-button');
            navButtons.forEach(btn => {
                if(btn.getAttribute('onclick').includes(activeTab)) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        });

        function switchTab(tabName) {
            // Save to localStorage so refresh keeps tab open
            localStorage.setItem('securityGuardActiveTab', tabName);

            // Hide all tab contents
            var contents = document.getElementsByClassName('tab-content');
            for (var i = 0; i < contents.length; i++) {
                contents[i].classList.remove('active');
            }

            // Remove active class from all buttons
            var buttons = document.getElementsByClassName('tab-button');
            for (var i = 0; i < buttons.length; i++) {
                buttons[i].classList.remove('active');
            }

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Find throwing event element OR document load element
            if(event && event.currentTarget) {
                event.currentTarget.classList.add('active');
            }
        }
    </script>
</body>
</html>
