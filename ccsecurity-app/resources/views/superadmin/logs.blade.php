<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Activity Logs - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Superadmin/superadmin_style_dashboard.css','resources/js/app.js'])
</head>
<body>
    <div class="dashboard-container">
        @include('superadmin.partials.sidebar')

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header fade-in">
                <div class="header-left">
                    <h1>Activity <span class="highlight">Logs</span></h1>
                    <p class="subtitle">Audit trail of Super Admin actions</p>
                </div>
                <div class="header-right">
                    <div class="datetime-display">
                        <div class="date">{{ now()->format('l, M j, Y') }}</div>
                        <div class="time">{{ now()->format('h:i A') }}</div>
                    </div>
                </div>
            </header>

            <!-- Summary Statistics -->
            <div class="stats-grid fade-in" style="animation-delay: 0.15s;">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Total Logs</h3>
                        <p class="stat-value">{{ $totalLogs }}</p>
                    </div>
                </div>

                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z"></path><path d="M12 6v6l4 2"></path></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Last 24 Hours</h3>
                        <p class="stat-value">{{ $logs->where('created_at', '>=', now()->subDay())->count() }}</p>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Security Actions</h3>
                        <p class="stat-value">{{ $logs->where('category', 'authentication')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="glass-card fade-in" style="animation-delay: 0.3s; padding: 0; overflow: hidden;">
                <div class="card-header">
                    <h3>Super Admin Activity Log</h3>
                </div>

                @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Date / Time</th>
                                <th>Super Admin</th>
                                <th>Category</th>
                                <th>Action</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                            <tr>
                                <td>
                                    <span style="font-weight: 600; color: #000;">{{ $log->created_at->format('M d, Y') }}</span>
                                    <div class="user-info">
                                        <span class="user-id">{{ $log->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: #000;">{{ $log->superadmin_name }}</span>
                                    <div class="user-info">
                                        <span class="user-id">ID #{{ $log->superadmin_id }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $catColors = ['authentication' => 'badge-info', 'admin_management' => 'badge-success', 'system' => 'badge-primary', 'other' => 'badge-danger'];
                                        $catLabels = ['authentication' => 'Authentication', 'admin_management' => 'Admin Management', 'system' => 'System', 'other' => 'Other'];
                                    @endphp
                                    <span class="badge {{ $catColors[$log->category] ?? 'badge-primary' }}">{{ $catLabels[$log->category] ?? ucfirst($log->category) }}</span>
                                </td>
                                <td>
                                    <span style="font-weight: 600;">{{ $log->action }}</span>
                                </td>
                                <td>
                                    <span class="email-text">{{ $log->description }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="padding: 20px;">
                    {{ $logs->links() }}
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    </div>
                    <p class="empty-title">No Activity Logs Found</p>
                    <p class="empty-desc">Super Admin actions will be recorded here for audit.</p>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <footer class="dashboard-footer fade-in" style="animation-delay: 0.4s;">
                &copy; {{ date('Y') }} KitaKits: Columban College Security System &bull; Super Admin Portal
            </footer>
        </main>
    </div>
</body>
</html>
