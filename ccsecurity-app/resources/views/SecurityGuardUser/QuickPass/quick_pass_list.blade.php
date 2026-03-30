<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Pass Management - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardUser/securityguard_style_dashboard.css', 'resources/css/SecurityGuardUser/securityguard_style_quickpass.css'])
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
                <!-- Direct linking since we are out of the dashboard SPA flow -->
                <a href="{{ route('security.dashboard') }}" class="tab-button" style="text-decoration: none;">
                    <span class="nav-icon">📊</span> Back to Command
                </a>
                <a href="{{ route('security.scanner.show') }}" class="tab-button" style="text-decoration: none;">
                    <span class="nav-icon">🔍</span> QR Scanner
                </a>
                <a href="{{ route('security.quick-pass.list') }}" class="tab-button active" style="text-decoration: none;">
                    <span class="nav-icon">🚗</span> Quick Pass
                </a>
                <a href="{{ route('security.entry.logs') }}" class="tab-button" style="text-decoration: none;">
                    <span class="nav-icon">📜</span> Entry Logs
                </a>
            </nav>
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('security.logout') }}" style="width: 100%;">
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
                    <h1 class="fade-in">Quick <span class="highlight">Pass</span> Management</h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">History and Status of Temporary Visitor Passes</p>
                </div>
                <div class="header-right quickpass-header-actions fade-in" style="animation-delay: 0.2s;">
                    <a href="{{ route('security.quick-pass.list') }}" class="btn-secondary" title="Refresh to check expiration">↻ Refresh</a>
                    <a href="{{ route('security.quick-pass.create') }}" class="btn-primary">+ New Quick Pass</a>
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

            <form action="{{ route('security.quick-pass.list') }}" method="GET" class="search-glass-form fade-in" style="animation-delay: 0.3s;">
                <input type="text" name="search" class="search-input" placeholder="Search by visitor name, vehicle, purpose, or QR..." value="{{ request('search') }}">
                <button type="submit" class="btn-primary">Search</button>
                @if(request('search'))
                    <a href="{{ route('security.quick-pass.list') }}" class="btn-secondary">Clear</a>
                @endif
            </form>

            <div class="glass-card full-width fade-in" style="animation-delay: 0.4s;">
                <div style="background: rgba(59, 130, 246, 0.1); border-left: 4px solid var(--info); border-radius: 6px; padding: 12px 16px; margin-bottom: 24px; font-size: 0.95rem; color: var(--primary-dark);">
                    <strong>Current Server Time:</strong> {{ \Carbon\Carbon::now()->format('l, F j, Y h:i:s A') }} ({{ config('app.timezone') }} timezone)
                </div>

                @if($quickPasses->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Visitor Name</th>
                                <th>Vehicle Plate</th>
                                <th>Purpose</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th>Expires</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quickPasses as $pass)
                            <tr class="{{ $pass->isExpired() && $pass->status !== 'expired' ? 'status-expired' : '' }}">
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-small bg-primary">{{ substr($pass->visitor_name, 0, 1) }}</div>
                                        <span class="full-name">{{ $pass->visitor_name }}</span>
                                    </div>
                                </td>
                                <td><span style="font-family: monospace; font-weight: 500;">{{ $pass->vehicle_plate ?? '—' }}</span></td>
                                <td>
                                    <span class="purpose-badge" style="background: {{ $pass->purpose_color }};">
                                        {{ $pass->purpose }}
                                    </span>
                                </td>
                                <td>
                                    <span style="color: var(--text-muted); font-size: 0.95rem;">
                                        {{ $pass->created_at?->format('h:i A') ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($pass->status === 'active')
                                        @if($pass->isExpired())
                                            <span class="badge badge-danger">Expired</span>
                                        @else
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    @elseif($pass->status === 'used')
                                        <span class="badge badge-outline">Used</span>
                                    @else
                                        <span class="badge badge-danger">Expired</span>
                                    @endif
                                    
                                    @if($pass->isExpired() && $pass->status !== 'expired')
                                        <br><small style="color: var(--danger); font-size: 0.75rem; font-weight: 600;">(Past expiration)</small>
                                    @endif
                                </td>
                                <td>
                                    <span style="color: {{ $pass->isExpired() ? 'var(--danger)' : 'var(--success)' }}; font-weight: 600; font-size: 0.95rem;">
                                        {{ $pass->expires_at->format('h:i A') }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('security.quick-pass.qr', $pass->id) }}" class="btn-primary btn-sm">View QR</a>
                                        <form action="{{ route('security.quick-pass.delete', $pass->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this Quick Pass?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($quickPasses->hasPages())
                <div style="margin-top: 30px;">
                    {{ $quickPasses->appends(request()->query())->links() }}
                </div>
                @endif
                
                @else
                <div class="empty-state">
                    <div class="empty-icon">🚗</div>
                    <p>No Quick Passes Found</p>
                    <span class="suggestion">No temporary visitor passes have been created yet.</span>
                    <br>
                    <a href="{{ route('security.quick-pass.create') }}" class="btn-primary" style="margin-top: 20px;">+ Create Your First Quick Pass</a>
                </div>
                @endif
            </div>

        </main>
    </div>
</body>
</html>
