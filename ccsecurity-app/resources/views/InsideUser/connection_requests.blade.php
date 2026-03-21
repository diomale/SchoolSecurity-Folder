<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection Requests - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/InsideUserStyleFolder/insideuser_dashboard_style.css', 'resources/css/InsideUserStyleFolder/insideuser_style_connections.css'])
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
                <a href="{{ route('insideuser.dashboard') }}" class="nav-link">
                    <span class="nav-icon">📊</span> Overview
                </a>
                <a href="{{ route('insideuser.profile.show') }}" class="nav-link">
                    <span class="nav-icon">👤</span> Profile
                </a>
                <a href="{{ route('insideuser.events.dashboard') }}" class="nav-link">
                    <span class="nav-icon">🎉</span> My Events
                </a>
                <a href="{{ route('insideuser.connection.requests') }}" class="nav-link active">
                    <span class="nav-icon">🤝</span> Connection Requests
                    @if($pendingCount > 0)
                        <span class="notification-badge">{{ $pendingCount }}</span>
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
            
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Connection <span class="highlight">Requests</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Approve or reject requests from parents/guardians trying to connect to your account.</p>
                </div>
            </header>

            <div class="fade-in" style="animation-delay: 0.2s;">
                @if(session('success'))
                    <div class="alert alert-success" style="margin-bottom: 24px;">
                        <div class="alert-icon">✓</div>
                        <div class="alert-content">
                            <h3>Success</h3>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                
                <!-- Statistics Cards -->
                <div class="stats-container">
                    <div class="stat-card stat-pending">
                        <div class="stat-value">{{ $pendingCount }}</div>
                        <div class="stat-label">Pending Approval</div>
                    </div>
                    <div class="stat-card stat-accepted">
                        <div class="stat-value">{{ $acceptedCount }}</div>
                        <div class="stat-label">Accepted by You</div>
                    </div>
                    <div class="stat-card stat-rejected">
                        <div class="stat-value">{{ $rejectedCount }}</div>
                        <div class="stat-label">Rejected by You</div>
                    </div>
                </div>

                <div class="glass-card">
                    <h3 class="section-title mb-4">Request History</h3>
                    
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Parent Name</th>
                                    <th>Email</th>
                                    <th>Relationship</th>
                                    <th>Decision</th>
                                    <th>Requested On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($connectionRequests as $request)
                                <tr>
                                    <td><strong>{{ $request->outsideUser->fullname ?? 'N/A' }}</strong></td>
                                    <td>{{ $request->outsideUser->email ?? 'N/A' }}</td>
                                    <td><span class="relationship-badge">{{ $request->relationship }}</span></td>
                                    <td>
                                        <span class="status-badge status-{{ $request->inside_user_approval }}">
                                            {{ ucfirst($request->inside_user_approval) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        @if($request->inside_user_approval === 'pending')
                                            <form action="{{ route('insideuser.connection.accept', $request->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-action-accept" onclick="return confirm('Accept this connection request?')">Accept</button>
                                            </form>
                                            
                                            <button class="btn-action-reject" onclick="openRejectModal({{ $request->id }})">Reject</button>
                                            
                                            <!-- Reject Modal -->
                                            <div id="rejectModal{{ $request->id }}" class="custom-modal">
                                                <div class="custom-modal-content">
                                                    <div class="custom-modal-header">
                                                        <h3>Reject Connection Request</h3>
                                                        <p>Are you sure you want to reject this request from <strong>{{ $request->outsideUser->fullname }}</strong>?</p>
                                                    </div>
                                                    <form action="{{ route('insideuser.connection.reject', $request->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="form-group">
                                                            <label>Reason (Optional):</label>
                                                            <textarea name="remarks" placeholder="Enter reason for rejection..."></textarea>
                                                        </div>
                                                        <div class="custom-modal-actions">
                                                            <button type="button" class="btn btn-sm" onclick="closeRejectModal({{ $request->id }})" style="background: rgba(0,0,0,0.05); color: var(--text-main);">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm" style="background: var(--danger);">Confirm Reject</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @elseif($request->inside_user_approval === 'accepted')
                                            <span style="color: var(--success); font-weight: 600;">✓ Accepted</span>
                                        @elseif($request->inside_user_approval === 'rejected')
                                            <span style="color: var(--text-muted); font-weight: 600;">✕ Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center" style="padding: 40px;">
                                        <div class="empty-state" style="border:none; background:transparent;">
                                            <div class="empty-icon">📭</div>
                                            <h4>No Requests Found</h4>
                                            <p>There are no connection requests in your history.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Layer -->
                    @if($connectionRequests->hasPages())
                    <div style="margin-top: 20px;">
                        {{ $connectionRequests->links() }}
                    </div>
                    @endif
                </div>
            </div>

        </main>
    </div>

    <script>
        function openRejectModal(id) {
            document.getElementById('rejectModal' + id).classList.add('active');
        }

        function closeRejectModal(id) {
            document.getElementById('rejectModal' + id).classList.remove('active');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('custom-modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>
