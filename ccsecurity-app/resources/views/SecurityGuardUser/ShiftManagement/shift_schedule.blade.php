<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Schedule - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardUser/securityguard_style_dashboard.css', 'resources/css/SecurityGuardUser/securityguard_style_shift_schedule.css'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        @include('SecurityGuardUser.partials.sidebar', ['activePage' => 'shift'])

        <!-- Main Content Area -->
        <main class="main-content">
            <a href="{{ route('security.shift.management') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; font-weight: 600; margin-bottom: 20px;">
                &larr; Back to Shift Management
            </a>

            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Shift <span class="highlight">Schedule</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">View your assigned upcoming shifts</p>
                </div>
            </header>

            <!-- Legend -->
            <div class="legend fade-in" style="animation-delay: 0.2s;">
                <div class="legend-item">
                    <div class="legend-color today"></div>
                    <span>Today</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color scheduled"></div>
                    <span>Scheduled</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color completed"></div>
                    <span>Completed</span>
                </div>
            </div>

            <!-- Upcoming Shifts -->
            <div class="shifts-container fade-in" style="animation-delay: 0.3s;">
                @if($upcomingShifts->count() > 0)
                    @foreach($upcomingShifts as $shift)
                    <div class="shift-card {{ $shift->shift_date->isToday() ? 'today' : 'scheduled' }}">
                        <div class="shift-info">
                            <div class="shift-date-header">
                                <span class="date">{{ $shift->shift_date->format('l, F d, Y') }}</span>
                                @if($shift->shift_date->isToday())
                                    <span class="badge" style="background: var(--success); color: white;">TODAY</span>
                                @endif
                            </div>
                            <div class="shift-details">
                                <div class="shift-time">
                                    <span style="color: var(--primary);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                                    {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                                </div>
                                <div class="shift-duration">
                                    <span style="vertical-align: middle; margin-right: 4px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    </span>
                                    {{ \Carbon\Carbon::parse($shift->start_time)->diffInHours(\Carbon\Carbon::parse($shift->end_time)) }} hours
                                </div>
                            </div>
                        </div>
                        <div class="shift-status-section">
                            <span class="badge badge-{{ strtolower($shift->status) === 'completed' ? 'completed' : (strtolower($shift->status) === 'scheduled' ? 'warning' : 'outline') }}">
                                {{ ucfirst($shift->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach

                    <!-- Pagination -->
                    @if($upcomingShifts->hasPages())
                    <div class="pagination-wrapper">
                        {{ $upcomingShifts->links() }}
                    </div>
                    @endif
                @else
                    <div class="empty-state" style="padding: 60px;">
                        <div class="empty-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <p style="font-size: 1.2rem; font-weight: 600; color: var(--text-main);">No Upcoming Shifts</p>
                        <span class="suggestion">You don't have any scheduled shifts at the moment.</span>
                    </div>
                @endif
            </div>

        </main>
    </div>
</body>
</html>
