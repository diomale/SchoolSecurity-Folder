<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entry/Exit Logs - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardUser/securityguard_style_dashboard.css', 'resources/css/SecurityGuardUser/securityguard_style_entrylogs.css'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        @include('SecurityGuardUser.partials.sidebar', ['activePage' => 'entry-logs'])

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Entry/Exit <span class="highlight">Logs</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Real-time campus movement overview</p>
                </div>
            </header>

            <!-- Statistics Cards -->
            <div class="stats-container fade-in" style="animation-delay: 0.2s;">
                <div class="stat-card stat-entries">
                    <h3>Entries Today</h3>
                    <p>{{ $totalEntriesToday }}</p>
                </div>
                <div class="stat-card stat-exits">
                    <h3>Exits Today</h3>
                    <p>{{ $totalExitsToday }}</p>
                </div>
                <div class="stat-card stat-inside">
                    <h3>Currently Inside</h3>
                    <p>{{ $currentlyInsideCount }}</p>
                </div>
            </div>

            <!-- Currently Inside Section -->
            <div class="currently-inside-section fade-in" style="animation-delay: 0.3s;">
                <h2>
                    People Currently Inside School
                    @if($currentlyInsidePeople->count() > 0)
                        <span class="badge-count">{{ $currentlyInsidePeople->count() }}</span>
                    @endif
                </h2>
                
                @if($currentlyInsidePeople->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table" style="background: white; border-radius: var(--radius-sm); overflow: hidden;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Entry Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currentlyInsidePeople as $person)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <span style="color: #000; font-weight: 600;">{{ $person['fullname'] ?? 'U' }}</span>
                                        <span class="full-name">{{ $person['fullname'] ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td><span style="color: var(--text-muted); font-size: 0.9rem;">{{ $person['email'] ?? 'N/A' }}</span></td>
                                <td>
                                    @if(isset($person['role']) && $person['role'])
                                        <span class="badge badge-outline">{{ ucfirst($person['role']) }}</span>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.9rem;">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="scan-badge scan-entry" style="margin-right: 8px;">Entry</span>
                                    <span style="font-weight: 500;">{{ $person['scan_at'] ? \Carbon\Carbon::parse($person['scan_at'])->format('M d, Y h:i A') : 'N/A' }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="no-inside">
                    <span style="display: block; margin-bottom: 10px;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </span>
                    No one is currently inside the school premises.
                </div>
                @endif
            </div>

            <!-- Filters -->
            <div class="filters-glass fade-in" style="animation-delay: 0.4s;">
                <form method="GET" action="{{ route('security.entry.logs') }}">
                    <div class="filter-group">
                        <label for="search">Search</label>
                        <input type="text" id="search" name="search" placeholder="Name or Email..." value="{{ request('search') }}">
                    </div>
                    <div class="filter-group">
                        <label for="scan_type">Filter by Type</label>
                        <select id="scan_type" name="scan_type">
                            <option value="">All Types</option>
                            <option value="entry" {{ request('scan_type') == 'entry' ? 'selected' : '' }}>Entry</option>
                            <option value="exit" {{ request('scan_type') == 'exit' ? 'selected' : '' }}>Exit</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="date">Filter by Date</label>
                        <input type="date" id="date" name="date" value="{{ request('date') }}">
                    </div>
                    <div>
                        <button type="submit" class="btn-primary" style="height: 45px; margin-top: 26px;">Apply Filters</button>
                    </div>
                    @if(request('search') || request('scan_type') || request('date'))
                    <div style="display: flex; align-items: flex-end; padding-bottom: 3px;">
                        <a href="{{ route('security.entry.logs') }}" class="clear-btn">Clear Filters</a>
                    </div>
                    @endif
                </form>
            </div>

            <!-- Logs Table -->
            <div class="glass-card fade-in" style="animation-delay: 0.5s;">
                <h3 style="margin-bottom: 24px; font-size: 1.3rem;">Recent Entry/Exit Logs</h3>
                
                @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Person Name</th>
                                <th>Scan Type</th>
                                <th>Scan Time</th>
                                <th>Scanned By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        @php
                                            $uName = 'Unknown User';
                                            $uRoleType = '';
                                            $roleColor = 'var(--text-muted)';
                                            
                                            if($log->eventRegistration) {
                                                $uName = $log->eventRegistration->fullname;
                                                $uRoleType = 'Event Attendee';
                                                $roleColor = 'var(--purple)';
                                            } elseif($log->quickPass) {
                                                $uName = $log->quickPass->visitor_name;
                                                $uRoleType = 'Quick Pass';
                                                $roleColor = 'var(--info)';
                                            } elseif($log->insideUser) {
                                                $uName = $log->insideUser->fullname;
                                                $uRoleType = 'Staff/Student';
                                                $roleColor = 'var(--success)';
                                            } elseif($log->outsideUser) {
                                                $uName = $log->outsideUser->fullname;
                                                $uRoleType = 'Visitor';
                                                $roleColor = 'var(--warning)';
                                            } elseif(str_starts_with($log->qr_value, 'EVT')) {
                                                $uRoleType = 'Event Attendee';
                                                $roleColor = 'var(--purple)';
                                            }
                                        @endphp
                                        <span style="color: #000; font-weight: 600;">{{ $uName }}</span>
                                        <div>
                                            <div class="full-name">{{ $uName }}</div>
                                            <div style="font-size: 0.8rem; font-weight: 600; color: {{ $roleColor }};">{{ $uRoleType }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($log->eventRegistration)
                                        @if($log->scan_type === 'entry')
                                            <span class="badge" style="background: var(--purple-light); color: var(--purple);">EVENT CHECK-IN</span>
                                        @elseif($log->scan_type === 'exit')
                                            <span class="badge" style="background: var(--purple-light); color: var(--purple);">EVENT CHECK-OUT</span>
                                        @endif
                                    @elseif($log->scan_type === 'entry')
                                        <span class="scan-badge scan-entry">Entry</span>
                                    @elseif($log->scan_type === 'exit')
                                        <span class="scan-badge scan-exit">Exit</span>
                                    @else
                                        <span class="badge badge-outline">{{ $log->scan_type }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="date-cell">
                                        <span class="date">{{ $log->scan_at ? \Carbon\Carbon::parse($log->scan_at)->format('M d, Y') : 'N/A' }}</span>
                                        <span class="time">{{ $log->scan_at ? \Carbon\Carbon::parse($log->scan_at)->format('h:i A') : '' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="guard-badge">
                                        @if($log->securityGuardUser)
                                            {{ $log->securityGuardUser->fullname ?? ($log->securityGuardUser->first_name . ' ' . $log->securityGuardUser->last_name) }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                <div class="pagination-wrapper">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
                @endif
                
                @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <p style="font-size: 1.1rem; color: var(--text-main); font-weight: 600;">No entry/exit logs found</p>
                    <span class="suggestion">Adjust your filters to see more results.</span>
                </div>
                @endif
            </div>

        </main>
    </div>
</body>
</html>
