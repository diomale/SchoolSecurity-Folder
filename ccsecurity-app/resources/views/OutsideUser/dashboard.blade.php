<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Dashboard - School Security</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Load our custom external CSS via Vite -->
    @vite(['resources/css/OutsideUser/outsideuser_style_dashboard.css'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2 style="font-size:1.1rem; line-height:1.2;">KitaKits<br><small style="font-weight: 500; font-size: 0.85rem; color: var(--text-muted);">Columban College Security System</small></h2>
            </div>

            <nav class="sidebar-nav">
                <button class="tab-button active" onclick="switchTab('profile')">
                    <span class="nav-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span> User Profile
                </button>
                <button class="tab-button" onclick="switchTab('quick-actions')">
                    <span class="nav-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </span> Visit Request
                </button>
                <button class="tab-button" onclick="switchTab('visit-history')">
                    <span class="nav-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </span> Visit History
                </button>
                <button class="tab-button" onclick="switchTab('child-connections')">
                    <span class="nav-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </span> Child Connections
                    @if($pendingConnectionCount > 0)
                        <span class="notification-badge">{{ $pendingConnectionCount }}</span>
                    @endif
                </button>
                <button class="tab-button" onclick="switchTab('child-activity')">
                    <span class="nav-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </span> Child Activity
                </button>
            </nav>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('outsideuser.logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </span> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Header -->
            <header class="top-header">
                <div class="header-left">
                    <h1>Welcome back, <span class="highlight">{{ explode(' ', trim(auth('outsideuser')->user()->fullname))[0] }}</span></h1>
                    <p class="subtitle">Here's what's happening today.</p>
                </div>
                
                <div class="header-right">
                    <a href="{{ route('outsideuser.notifications') }}" class="notification-trigger">
                        <span class="bell-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        </span>
                        @if($unreadNotificationsCount > 0)
                            <span class="notification-count">{{ $unreadNotificationsCount }}</span>
                        @endif
                    </a>
                </div>
            </header>

            @if(session('success'))
            <div class="alert alert-success">
                <div class="alert-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="alert-content">
                    <strong>Success:</strong> {{ session('success') }}
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-error">
                <div class="alert-icon">!</div>
                <div class="alert-content">
                    <strong>Error:</strong> {{ session('error') }}
                </div>
            </div>
            @endif

            <div class="tabs-container">
                <!-- User Profile Tab -->
                <div id="profile" class="tab-content active fade-in">
                    <div class="profile-grid">
                        <!-- Left Column: Profile Info & Stats -->
                        <div class="profile-left">
                            <div class="glass-card profile-details-card">
                                <h3>Profile Information</h3>
                                <div class="info-group">
                                    <label>Full Name</label>
                                    <p>{{ auth('outsideuser')->user()->fullname }}</p>
                                </div>
                                <div class="info-group">
                                    <label>Email Address</label>
                                    <p>{{ auth('outsideuser')->user()->email }}</p>
                                </div>
                                <div class="info-group">
                                    <label>Phone Number</label>
                                    <p>{{ auth('outsideuser')->user()->phone_number }}</p>
                                </div>
                                <div class="card-actions">
                                    <a href="{{ route('outsideuser.profile.show') }}" class="btn btn-outline">Edit Profile</a>
                                </div>
                            </div>
                            
                            <div class="glass-card stats-card">
                                <h3>Visit Statistics</h3>
                                <div class="stats-grid">
                                    <div class="stat-box">
                                        <span class="stat-value">{{ $visitRequests->count() }}</span>
                                        <span class="stat-label">Total</span>
                                    </div>
                                    <div class="stat-box success">
                                        <span class="stat-value">{{ $visitRequests->where('status', 'approved')->count() }}</span>
                                        <span class="stat-label">Approved</span>
                                    </div>
                                    <div class="stat-box warning">
                                        <span class="stat-value">{{ $visitRequests->where('status', 'pending')->count() }}</span>
                                        <span class="stat-label">Pending</span>
                                    </div>
                                    <div class="stat-box danger">
                                        <span class="stat-value">{{ $visitRequests->where('status', 'rejected')->count() }}</span>
                                        <span class="stat-label">Rejected</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: QR Code -->
                        <div class="profile-right">
                            <div class="glass-card qr-card center-align">
                                <h3>My Digital Pass</h3>
                                @if(auth('outsideuser')->user()->qr_value)
                                    <div class="qr-code-wrapper" onclick="openQrModal()" title="Click to enlarge for easier scanning" style="cursor: pointer; transition: transform 0.2s;">
                                        <div class="qr-code-container">
                                            {!! QrCode::size(200)->margin(1)->generate(auth('outsideuser')->user()->qr_value) !!}
                                        </div>
                                    </div>
                                    <div class="qr-status-box">
                                        <span class="status-label">Status:</span>
                                        @if(auth('outsideuser')->user()->qr_status === 'active')
                                            <span class="badge badge-success pulse">● ACTIVE</span>
                                        @else
                                            <span class="badge badge-danger">● INACTIVE</span>
                                        @endif
                                    </div>
                                    <p class="qr-instruction">
                                        <em>Present this QR code to the guard at the entrance.</em>
                                    </p>
                                @else
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                                    </div>
                                        <p>No QR pass generated yet.</p>
                                        <span class="suggestion">Submit a visit request to get your pass.</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Notifications Section in Profile Tab for visibility -->
                            @if($notifications && $notifications->count() > 0)
                            <div class="glass-card notifications-card">
                                <div class="card-header">
                                    <h3>Recent Notifications</h3>
                                    <a href="{{ route('outsideuser.notifications') }}" class="small-link">View All</a>
                                </div>
                                <div class="notification-list">
                                    @foreach($notifications->take(3) as $notification)
                                    <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}">
                                        <div class="noti-icon">
                                            @if($notification->type === 'visit_approved')
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--success);"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            @elseif($notification->type === 'visit_rejected')
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--danger);"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                            @else
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--info);"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                            @endif
                                        </div>
                                        <div class="noti-content">
                                            <div class="noti-title">
                                                {{ $notification->title }}
                                                @if(!$notification->is_read)
                                                    <span class="badge badge-new">New</span>
                                                @endif
                                            </div>
                                            <div class="noti-message">{{ Str::limit($notification->message, 50) }}</div>
                                            <div class="noti-time">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Tab -->
                <div id="quick-actions" class="tab-content fade-in">
                    <div class="glass-card feature-card">
                        <div class="feature-icon-large">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <h2>Request a Visit</h2>
                        <p class="description">Submit a visit request to receive a temporary QR pass for campus access.</p>
                        <a href="{{ route('outsideuser.visit.request') }}" class="btn btn-primary btn-lg pulse-hover">Start New Request</a>
                    </div>
                </div>

                <!-- Visit History Tab -->
                <div id="visit-history" class="tab-content fade-in">
                    <div class="glass-card">
                        <div class="card-header">
                            <h2>Visit History</h2>
                            <a href="{{ route('outsideuser.visit.request') }}" class="btn btn-primary btn-sm">+ New Visit</a>
                        </div>

                        <div class="table-responsive">
                            @if($visitRequests->count() > 0)
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Purpose</th>
                                        <th>Meeting With</th>
                                        <th>Status</th>
                                        <th>Admin Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($visitRequests as $request)
                                    <tr>
                                        <td>
                                            <div class="date-cell">
                                                <span class="date">{{ $request->visit_date->format('M d, Y') }}</span>
                                                <span class="time">{{ $request->visit_time->format('h:i A') }}</span>
                                            </div>
                                        </td>
                                        <td class="purpose-cell">{{ $request->purpose }}</td>
                                        <td>{{ $request->person_to_meet }}</td>
                                        <td>
                                            @if($request->status === 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($request->status === 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td class="remarks-cell">
                                            @if($request->admin_remarks)
                                                {{ $request->admin_remarks }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            </div>
                                <p>No visit requests found.</p>
                                <a href="{{ route('outsideuser.visit.request') }}" class="btn btn-outline mt-3">Request your first visit</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Child Connections Tab -->
                <div id="child-connections" class="tab-content fade-in">
                    <div class="actions-header">
                        <div>
                            <h2>Child Connections</h2>
                            <p class="subtitle">Connect with your children to track their campus entry and exit logs.</p>
                        </div>
                        <div class="header-buttons">
                            <a href="{{ route('outsideuser.connections.request') }}" class="btn btn-primary">+ Request Connection</a>
                            <a href="{{ route('outsideuser.connections.history') }}" class="btn btn-outline">View History</a>
                        </div>
                    </div>

                    @if($pendingConnectionCount > 0)
                    <div class="alert alert-warning">
                        <div class="alert-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div class="alert-content">
                            You have <strong>{{ $pendingConnectionCount }}</strong> pending connection request(s) awaiting admin approval.
                        </div>
                    </div>
                    @endif

                    <div class="glass-card mb-4">
                        <h3>Connected Children</h3>
                        <div class="table-responsive">
                            @if($approvedConnections->count() > 0)
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>Student Details</th>
                                        <th>Relationship</th>
                                        <th>QR Status</th>
                                        <th>Connected Since</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedConnections as $connection)
                                    <tr>
                                        <td>
                                            <div class="user-cell">
                                                <span style="color: #000; font-weight: 600;">{{ $connection->insideUser->fullname ?? 'U' }}</span>
                                                <div>
                                                    <div class="full-name">{{ $connection->insideUser->fullname ?? 'N/A' }}</div>
                                                    <div class="email-sub">{{ $connection->insideUser->email ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="relationship-badge">{{ $connection->relationship }}</span></td>
                                        <td>
                                            @if($connection->insideUser && $connection->insideUser->qr_status === 'active')
                                                <span class="badge badge-success pulse">ACTIVE</span>
                                            @else
                                                <span class="badge badge-danger">INACTIVE</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($connection->approved_at)->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                                <p>You haven't connected with any children yet.</p>
                                <a href="{{ route('outsideuser.connections.request') }}" class="btn btn-outline mt-3">Request your first connection</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Children Entry/Exit Logs -->
                    <div class="glass-card">
                        <h3>Recent Entry/Exit Activity</h3>
                        <p class="subtitle mb-3">View your children's campus entry and exit logs in the Child Activity tab.</p>
                        <a href="#" onclick="switchTabTo('child-activity'); return false;" class="btn btn-outline">Go to Child Activity</a>
                    </div>
                </div>

                <!-- Child Activity Tab -->
                <div id="child-activity" class="tab-content fade-in">
                    <div class="actions-header">
                        <div>
                            <h2>Child Activity</h2>
                            <p class="subtitle">Track when your connected children enter or exit the school bounds.</p>
                        </div>
                        <div class="header-buttons">
                            <a href="{{ route('outsideuser.connections.history') }}" class="btn btn-outline">View History</a>
                        </div>
                    </div>

                    @if($approvedConnections->count() > 0)
                    <div class="glass-card mb-4">
                        <h3>Filter by Child</h3>
                        <div class="table-responsive">
                            <select id="childFilter" class="child-filter-select" onchange="filterChildActivity()">
                                <option value="all">All Children</option>
                                @foreach($approvedConnections as $connection)
                                    <option value="{{ $connection->inside_user_id }}">{{ $connection->insideUser->fullname ?? 'Unknown' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="glass-card">
                        <h3>Entry / Exit Logs</h3>
                        <div class="table-responsive">
                            @if(isset($childrenEntryLogs) && count($childrenEntryLogs) > 0)
                            <table class="modern-table logs-table" id="activityTable">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Scan Type</th>
                                        <th>Date & Time</th>
                                        <th>Scanned By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($childrenEntryLogs as $log)
                                    <tr class="activity-row" data-child-id="{{ $log->inside_user_id }}">
                                        <td><strong>{{ $log->insideUser->fullname ?? 'Unknown' }}</strong></td>
                                        <td>
                                            @if($log->scan_type === 'entry')
                                                <span class="scan-badge scan-entry">ENTRY</span>
                                            @elseif($log->scan_type === 'exit')
                                                <span class="scan-badge scan-exit">EXIT</span>
                                            @else
                                                <span class="scan-badge">{{ $log->scan_type ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->scan_at)
                                                <div class="date-cell">
                                                    <span class="date">{{ $log->scan_at->format('M d, Y') }}</span>
                                                    <span class="time">{{ $log->scan_at->format('h:i A') }}</span>
                                                </div>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <span class="guard-badge">{{ $log->securityGuardUser->fullname ?? 'Unknown Guard' }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            </div>
                                <p>No entry/exit activity for your connected children yet.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- QR Code Modal -->
    <div id="qrModal" class="qr-modal" onclick="closeQrModal(event)">
        <div class="qr-modal-content" onclick="event.stopPropagation()">
            <span class="close-modal" onclick="closeQrModal(event)">&times;</span>
            <h3 style="font-size: 1.5rem; margin-bottom: 25px; color: var(--text-main);">Enlarged Digital Pass</h3>
            <div class="qr-code-large">
                @if(auth('outsideuser')->user() && auth('outsideuser')->user()->qr_value)
                    {!! QrCode::size(320)->margin(2)->generate(auth('outsideuser')->user()->qr_value) !!}
                @endif
            </div>
            <p style="margin-top: 20px; font-weight: 500; color: var(--text-muted); font-size: 1.1rem;">Present this directly to the scanner at the entrance.</p>
        </div>
    </div>

    <script>
        function openQrModal() {
            var modal = document.getElementById('qrModal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden'; 
            }
        }

        function closeQrModal(event) {
            if(event) event.preventDefault();
            var modal = document.getElementById('qrModal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function switchTab(tabName) {
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

            // Add active class to clicked button
            var eventTarget = event.currentTarget || event.target;
            if(eventTarget.classList.contains('tab-button')){
                eventTarget.classList.add('active');
            } else {
                eventTarget.closest('.tab-button').classList.add('active');
            }
        }

        function switchTabTo(tabName) {
            switchTab(tabName);
            var buttons = document.getElementsByClassName('tab-button');
            for (var i = 0; i < buttons.length; i++) {
                var onclick = buttons[i].getAttribute('onclick') || '';
                if (onclick.indexOf("switchTab('" + tabName + "')") !== -1) {
                    buttons[i].classList.add('active');
                }
            }
        }

        function filterChildActivity() {
            var filter = document.getElementById('childFilter');
            var selected = filter ? filter.value : 'all';
            var rows = document.querySelectorAll('#activityTable .activity-row');
            for (var i = 0; i < rows.length; i++) {
                var childId = rows[i].getAttribute('data-child-id');
                rows[i].style.display = (selected === 'all' || childId === selected) ? '' : 'none';
            }
        }
        
        // Disable right-click on QR code
        document.addEventListener('DOMContentLoaded', function() {
            const qrContainer = document.querySelector('.qr-code-container');
            if (qrContainer) {
                qrContainer.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    return false;
                });
                
                // Disable drag start on QR image
                const qrImage = qrContainer.querySelector('img');
                if (qrImage) {
                    qrImage.addEventListener('dragstart', function(e) {
                        e.preventDefault();
                        return false;
                    });
                }
            }
        });
    </script>
</body>
</html>
