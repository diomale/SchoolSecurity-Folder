<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - Admin - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
    <style>
        .log-filters { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .log-filters select, .log-filters input { padding: 8px 12px; border: 1px solid rgba(0,0,0,0.1); border-radius: var(--radius-sm); font-family: 'Outfit', sans-serif; font-size: 0.9rem; }
        .log-filters select { background: white; min-width: 150px; }

        .category-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.3px;
        }
        .cat-authentication { background: #dbeafe; color: #1e40af; }
        .cat-user_management { background: #dcfce7; color: #166534; }
        .cat-event_management { background: #fef3c7; color: #92400e; }
        .cat-qr_management { background: #e0e7ff; color: #3730a3; }
        .cat-shift_management { background: #fce7f3; color: #9d174d; }
        .cat-visit_requests { background: #ccfbf1; color: #0f766e; }
        .cat-connections { background: #f3e8ff; color: #6b21a8; }
        .cat-system { background: #f1f5f9; color: #475569; }
        .cat-other { background: #f1f5f9; color: #64748b; }

        .category-sidebar { list-style: none; padding: 0; margin: 0; }
        .category-sidebar li { margin-bottom: 4px; }
        .category-sidebar a {
            display: flex; justify-content: space-between; align-items: center;
            padding: 8px 12px; border-radius: var(--radius-sm); text-decoration: none;
            color: var(--text-muted); font-size: 0.9rem; font-weight: 500;
            transition: all 0.2s;
        }
        .category-sidebar a:hover { background: rgba(0,0,0,0.04); color: var(--text-main); }
        .category-sidebar a.active { background: rgba(0,0,0,0.06); color: var(--primary); font-weight: 600; }
        .category-sidebar .count { background: rgba(0,0,0,0.06); padding: 2px 8px; border-radius: 10px; font-size: 0.8rem; }
        .category-sidebar a.active .count { background: rgba(var(--primary-rgb, 59,130,246), 0.15); color: var(--primary); }

        .log-timeline { position: relative; }
        .log-entry {
            display: grid; grid-template-columns: auto 1fr auto; gap: 12px;
            padding: 12px 16px; border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: background 0.15s;
        }
        .log-entry:hover { background: rgba(0,0,0,0.02); }
        .log-entry:last-child { border-bottom: none; }

        .log-dot { width: 10px; height: 10px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
        .dot-authentication { background: #3b82f6; }
        .dot-user_management { background: #22c55e; }
        .dot-event_management { background: #f59e0b; }
        .dot-qr_management { background: #6366f1; }
        .dot-shift_management { background: #ec4899; }
        .dot-visit_requests { background: #14b8a6; }
        .dot-connections { background: #a855f7; }
        .dot-system { background: #94a3b8; }
        .dot-other { background: #94a3b8; }

        .log-body { min-width: 0; }
        .log-header { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 3px; }
        .log-action { font-weight: 600; color: var(--text-main); font-size: 0.9rem; }
        .log-admin { color: var(--text-muted); font-size: 0.85rem; }
        .log-description { color: var(--text-muted); font-size: 0.85rem; line-height: 1.4; }
        .log-meta { display: flex; gap: 12px; margin-top: 4px; font-size: 0.8rem; color: var(--text-muted); }

        .log-time { text-align: right; white-space: nowrap; }
        .log-time .date { font-size: 0.8rem; color: var(--text-muted); display: block; }
        .log-time .time { font-size: 0.85rem; font-weight: 600; color: var(--text-main); }

        .empty-logs { text-align: center; padding: 60px 20px; color: var(--text-muted); }

        .date-group-header {
            padding: 8px 16px; background: rgba(0,0,0,0.03); font-weight: 700;
            font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;
            letter-spacing: 0.5px; border-bottom: 1px solid rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    @include('Admin.partials.sidebar', ['activePage' => 'activity_logs'])

    <main class="main-content">
        <header class="top-header">
            <div class="header-left">
                <h1 class="fade-in">Activity <span class="highlight">Logs</span></h1>
                <p class="subtitle fade-in" style="animation-delay: 0.1s;">Monitor all admin actions across the system</p>
            </div>
            <div class="header-right fade-in" style="animation-delay: 0.1s;">
                <form method="POST" action="{{ route('admin.activity-logs.clear-old') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" onclick="return confirm('Clear activity logs older than 30 days?')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        Clear Old Logs
                    </button>
                </form>
            </div>
        </header>

        @if(session('success'))
            <div class="alert alert-success fade-in">
                <div class="alert-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="alert-content">{{ session('success') }}</div>
            </div>
        @endif

        <!-- Stats -->
        <div class="stats-grid fade-in" style="animation-delay: 0.2s;">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $todayCount }}</span>
                    <span class="stat-label">Today's Actions</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value text-success">{{ $weekCount }}</span>
                    <span class="stat-label">This Week</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value text-primary">{{ $totalAdmins }}</span>
                    <span class="stat-label">Active Admins</span>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 220px 1fr; gap: 20px; margin-top: 20px;" class="fade-in" style="animation-delay: 0.3s;">
            <!-- Category Sidebar -->
            <div class="glass-card" style="padding: 15px; height: fit-content; position: sticky; top: 20px;">
                <h4 style="margin: 0 0 12px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">Categories</h4>
                <ul class="category-sidebar">
                    <li>
                        <a href="{{ route('admin.activity-logs.index') }}" class="{{ !request('category') ? 'active' : '' }}">
                            <span>All Activity</span>
                            <span class="count">{{ $categoryCounts->sum() }}</span>
                        </a>
                    </li>
                    @foreach(\App\Services\AdminActivityLogger::CATEGORIES as $key => $label)
                        @if(isset($categoryCounts[$key]) && $categoryCounts[$key] > 0)
                        <li>
                            <a href="{{ route('admin.activity-logs.index', ['category' => $key] + request()->except('category')) }}" class="{{ request('category') === $key ? 'active' : '' }}">
                                <span>{{ $label }}</span>
                                <span class="count">{{ $categoryCounts[$key] }}</span>
                            </a>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <!-- Main Content -->
            <div>
                <!-- Filters -->
                <div class="glass-card fade-in" style="animation-delay: 0.35s; margin-bottom: 15px;">
                    <form method="GET" class="log-filters">
                        <input type="text" name="search" placeholder="Search logs..." value="{{ request('search') }}" style="flex: 1; min-width: 200px;">
                        <select name="admin_id">
                            <option value="">All Admins</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->admin_id }}" {{ request('admin_id') == $admin->admin_id ? 'selected' : '' }}>{{ $admin->admin_name }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        @if(request()->hasAny(['search', 'admin_id', 'date_from', 'date_to', 'category']))
                            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">Clear</a>
                        @endif
                    </form>
                </div>

                <!-- Log Entries -->
                <div class="glass-card fade-in" style="animation-delay: 0.4s;">
                    @if($logs->count() > 0)
                        @php $currentDate = null; @endphp
                        @foreach($logs as $log)
                            @php $logDate = $log->created_at ? $log->created_at->format('M d, Y') : 'Unknown'; @endphp
                            @if($logDate !== $currentDate)
                                @php $currentDate = $logDate; @endphp
                                <div class="date-group-header">{{ $logDate }}</div>
                            @endif
                            <div class="log-entry">
                                <div class="log-dot dot-{{ $log->category }}"></div>
                                <div class="log-body">
                                    <div class="log-header">
                                        <span class="category-badge cat-{{ $log->category }}">{{ \App\Services\AdminActivityLogger::CATEGORIES[$log->category] ?? $log->category }}</span>
                                        <span class="log-action">{{ $log->action }}</span>
                                    </div>
                                    <div class="log-description">{{ $log->description }}</div>
                                    <div class="log-meta">
                                        <span>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                            {{ $log->admin_name }}
                                        </span>
                                        @if($log->ip_address)
                                        <span>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                            {{ $log->ip_address }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="log-time">
                                    <span class="time">{{ $log->created_at ? $log->created_at->format('h:i A') : '' }}</span>
                                </div>
                            </div>
                        @endforeach

                        @if($logs->hasPages())
                        <div style="padding: 15px; text-align: center;">
                            {{ $logs->links() }}
                        </div>
                        @endif
                    @else
                        <div class="empty-logs">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 15px; opacity: 0.4;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <p>No activity logs found matching your filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
