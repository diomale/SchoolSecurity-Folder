<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Management - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardUser/securityguard_style_dashboard.css', 'resources/css/SecurityGuardUser/securityguard_style_shift_management.css'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        @include('SecurityGuardUser.partials.sidebar', ['activePage' => 'shift'])

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Shift <span class="highlight">Management</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Track your hours, clock in/out, and view schedules.</p>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success fade-in">
                    <div class="alert-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error fade-in">
                    <div class="alert-icon">!</div>
                    <div class="alert-content">{{ session('error') }}</div>
                </div>
            @endif

            <div class="stats-grid fade-in" style="animation-delay: 0.2s;">
                <div class="stat-card">
                    <div class="stat-icon icon-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Shifts This Week</h3>
                        <p>{{ $totalShiftsThisWeek }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Hours This Week</h3>
                        <p>{{ number_format($totalHoursThisWeek, 1) }}</p>
                    </div>
                </div>
            </div>

            <!-- Current Shift Status -->
            <div class="shift-status-container {{ $currentShiftLog ? 'shift-active' : 'shift-inactive' }} fade-in" style="animation-delay: 0.3s;">
                @if($currentShiftLog)
                    <h2 style="color: var(--primary-dark); margin-bottom: 5px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; color: var(--success);"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Currently On Shift
                    </h2>
                    <div class="shift-time-display">
                        {{ \Carbon\Carbon::now()->format('h:i') }} <span style="font-size: 1.5rem; color: var(--text-muted);">{{ \Carbon\Carbon::now()->format('A') }}</span>
                    </div>
                    <div class="shift-date-display">
                        Clocked in at <strong>{{ $currentShiftLog->clock_in_time->format('h:i A') }}</strong> on {{ $currentShiftLog->clock_in_time->format('M d, Y') }}
                    </div>
                    
                    <div class="actions-row">
                        @if($currentShiftLog->id)
                        <button type="button" class="btn-outline" onclick="document.getElementById('handover-form').style.display='block'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Add Handover Note
                        </button>
                        @endif
                        <form method="POST" action="{{ route('security.clock.out') }}">
                            @csrf
                            <button type="submit" class="btn-clock btn-clock-out" onclick="return confirm('Clock out from your shift?')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                                Clock Out
                            </button>
                        </form>
                    </div>

                    <!-- Handover Note Form -->
                    <div id="handover-form" class="handover-modal">
                        <h3 style="margin-bottom: 15px; color: var(--primary-dark);">Handover Note for Next Guard</h3>
                        <form method="POST" action="{{ route('security.submit.handover') }}">
                            @csrf
                            <input type="hidden" name="shift_log_id" value="{{ $currentShiftLog->id }}">
                            <textarea 
                                name="handover_note" 
                                placeholder="Write any important information for the next guard (e.g., specific instructions, incidents during your shift)..."
                                required
                            ></textarea>
                            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button type="button" class="btn-outline" style="padding: 10px 20px;" onclick="document.getElementById('handover-form').style.display='none'">
                                    Cancel
                                </button>
                                <button type="submit" class="btn-clock btn-clock-in" style="padding: 10px 20px;">
                                    Submit Handover
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <h2 style="color: var(--warning); margin-bottom: 5px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; color: var(--warning);"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                        Not Currently On Shift
                    </h2>
                    <div class="shift-time-display">
                        {{ \Carbon\Carbon::now()->format('h:i') }} <span style="font-size: 1.5rem; color: var(--text-muted);">{{ \Carbon\Carbon::now()->format('A') }}</span>
                    </div>
                    <div class="shift-date-display">
                        Today is <strong>{{ today()->format('l, F d, Y') }}</strong>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <form method="POST" action="{{ route('security.clock.in') }}">
                            @csrf
                            <button type="submit" class="btn-clock btn-clock-in">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                Clock In Now
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: stretch;">
                
                <!-- Today's Scheduled Shift -->
                <div class="glass-card fade-in" style="animation-delay: 0.4s; height: 100%;">
                    <h3 style="margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Today's Schedule
                    </h3>
                    @if($todayShift)
                        <div style="background: rgba(0,0,0,0.02); padding: 15px; border-radius: var(--radius-sm); border: 1px solid rgba(0,0,0,0.05);">
                            <p style="margin-bottom: 10px;"><strong>Date:</strong> <span style="color: var(--text-muted);">{{ $todayShift->shift_date->format('F d, Y') }}</span></p>
                            <p style="margin-bottom: 10px;"><strong>Shift Time:</strong> <span style="font-weight: 600; color: var(--primary);">{{ \Carbon\Carbon::parse($todayShift->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($todayShift->end_time)->format('h:i A') }}</span></p>
                            <p style="margin-bottom: 0;"><strong>Status:</strong> 
                                <span class="badge badge-{{ $todayShift->status }}">
                                    {{ ucfirst($todayShift->status) }}
                                </span>
                            </p>
                        </div>
                    @else
                        <div class="empty-state" style="padding: 20px;">
                            <p>No shift scheduled for today.</p>
                        </div>
                    @endif
                </div>

                <!-- Navigation Links -->
                <div class="nav-cards fade-in" style="animation-delay: 0.5s;">
                    <a href="{{ route('security.shift.schedule') }}" class="nav-card">
                        <div class="nav-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 5px; font-size: 1.1rem; color: var(--primary-dark);">View Schedule</h4>
                            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Check upcoming shifts</p>
                        </div>
                    </a>
                    <a href="{{ route('security.shift.history') }}" class="nav-card">
                        <div class="nav-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 5px; font-size: 1.1rem; color: var(--primary-dark);">Full History</h4>
                            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Review past hours</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Shift History -->
            <div class="glass-card fade-in" style="margin-top: 30px; animation-delay: 0.6s;">
                <h3 style="margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Recent Activity
                </h3>
                @if($recentShiftLogs->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentShiftLogs as $log)
                            <tr>
                                <td style="font-weight: 600;">{{ $log->clock_in_time->format('M d, Y') }}</td>
                                <td><span style="color: var(--success); font-weight: 500;">{{ $log->clock_in_time->format('h:i A') }}</span></td>
                                <td><span style="color: var(--danger); font-weight: 500;">{{ $log->clock_out_time ? $log->clock_out_time->format('h:i A') : '—' }}</span></td>
                                <td>{{ $log->clock_out_time ? $log->clock_in_time->diffInHours($log->clock_out_time) . ' hrs' : '—' }}</td>
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
                @else
                <div class="empty-state" style="padding: 30px;">
                    <div class="empty-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <p>No shift history available yet.</p>
                </div>
                @endif
            </div>

        </main>
    </div>
</body>
</html>
