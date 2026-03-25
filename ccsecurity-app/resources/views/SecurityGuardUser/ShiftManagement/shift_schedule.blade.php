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
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-circle">CCSS</div>
                <h2 style="font-size:1.1rem; line-height:1.2;">Columban College<br><small style="font-weight: 500; font-size: 0.85rem; color: var(--text-muted);">Security System</small></h2>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('security.dashboard') }}" class="tab-button" style="text-decoration: none;">
                    <span class="nav-icon">📊</span> Back to Command
                </a>
                <a href="{{ route('security.shift.management') }}" class="tab-button active" style="text-decoration: none;">
                    <span class="nav-icon">⏱️</span> Shift Management
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
                                    <span style="font-size: 1.2rem; color: var(--primary);">🕒</span>
                                    {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                                </div>
                                <div class="shift-duration">
                                    <span style="font-size: 1.2rem;">⏳</span>
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
                        <div class="empty-icon">📅</div>
                        <p style="font-size: 1.2rem; font-weight: 600; color: var(--text-main);">No Upcoming Shifts</p>
                        <span class="suggestion">You don't have any scheduled shifts at the moment.</span>
                    </div>
                @endif
            </div>

        </main>
    </div>
</body>
</html>
