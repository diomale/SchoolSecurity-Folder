<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift History - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardUser/securityguard_style_dashboard.css', 'resources/css/SecurityGuardUser/securityguard_style_shift_history.css'])
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
                    <h1 class="fade-in">Shift <span class="highlight">History</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">A comprehensive record of your past shifts and hours.</p>
                </div>
            </header>

            <div class="summary-banner fade-in" style="animation-delay: 0.2s;">
                <div>
                    <h3>Total Hours Worked</h3>
                    <p style="margin: 5px 0 0 0; font-size: 0.9rem; opacity: 0.8;">(Selected Period)</p>
                </div>
                <p class="number">{{ number_format($totalHours, 1) }}</p>
            </div>

            <div class="filters-glass fade-in" style="animation-delay: 0.3s;">
                <form method="GET" action="{{ route('security.shift.history') }}">
                    <div class="filter-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="filter-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="filter-group" style="flex: 0 0 auto;">
                        <button type="submit" class="btn-primary">Apply Filters</button>
                    </div>
                    @if(request('start_date') || request('end_date'))
                    <div class="filter-group" style="flex: 0 0 auto;">
                        <a href="{{ route('security.shift.history') }}" class="btn-clear">Clear Filters</a>
                    </div>
                    @endif
                </form>
            </div>

            <div class="glass-card full-width fade-in" style="animation-delay: 0.4s;">
                @if($shiftHistory->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                                <th>Duration (hrs)</th>
                                <th>Handover Note</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shiftHistory as $log)
                            <tr>
                                <td><span style="font-weight: 600;">{{ $log->clock_in_time->format('M d, Y') }}</span></td>
                                <td><span style="color: var(--success); font-weight: 500;">{{ $log->clock_in_time->format('h:i A') }}</span></td>
                                <td><span style="color: var(--danger); font-weight: 500;">{{ $log->clock_out_time ? $log->clock_out_time->format('h:i A') : '—' }}</span></td>
                                <td><strong style="color: var(--primary);">{{ $log->clock_out_time ? number_format($log->clock_in_time->diffInHours($log->clock_out_time), 1) : '—' }}</strong></td>
                                <td class="handover-note">
                                    @if($log->handover_note)
                                        <div style="background: rgba(0,0,0,0.02); padding: 8px 12px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.05);">
                                            {{ Str::limit($log->handover_note, 60) }}
                                        </div>
                                    @else
                                        <em>No handover note</em>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $log->clock_out_time ? 'badge-completed' : 'badge-active' }}" style="{{ $log->clock_out_time ? 'background: #d1ecf1; color: #0c5460;' : 'background: var(--success-light); color: var(--success);' }}">
                                        {{ $log->clock_out_time ? 'Completed' : 'Active' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($shiftHistory->hasPages())
                <div class="pagination-wrapper">
                    {{ $shiftHistory->appends(request()->query())->links() }}
                </div>
                @endif
                
                @else
                <div class="empty-state">
                    <div class="empty-icon">📜</div>
                    <p style="font-size: 1.1rem; color: var(--text-main); font-weight: 600;">No shift history found</p>
                    <span class="suggestion">Adjust your filters or start clocking in.</span>
                </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
