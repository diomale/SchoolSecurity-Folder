<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $guard->first_name }}'s Schedule - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">
    @include('Admin.partials.sidebar', ['activePage' => 'shift_management'])

    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>Guard <span class="highlight">Schedule</span></h1>
                <p class="subtitle">Individual shift listing for security personnel</p>
            </div>
            <a href="{{ $backUrl }}" class="btn-secondary">Back</a>
        </div>

        <!-- Guard Profile Header -->
        <div class="glass-card fade-in" style="animation-delay:0.05s; padding:24px 30px; border-left:4px solid var(--primary);">
            <div style="display:flex; align-items:center; gap:20px;">
                <div class="avatar-placeholder" style="width:64px; height:64px; font-size:1.8rem; margin:0;">
                    {{ substr($guard->first_name, 0, 1) }}
                </div>
                <div>
                    <h2 style="margin:0; border:0; padding:0; font-size:1.6rem;">{{ $guard->first_name }} {{ $guard->last_name }}</h2>
                    <p style="color:var(--text-muted); margin:4px 0 0 0;">{{ $guard->email }} | <span style="font-weight:600; color:var(--text-main);">ID: #{{ $guard->id }}</span></p>
                </div>
            </div>
        </div>

        <div class="fade-in" style="animation-delay:0.1s;">
            <h3 style="margin-bottom:20px; font-size:1.1rem; font-weight:700; color:var(--text-main); display:flex; align-items:center; gap:8px;">
                Detailed Shift Listing ({{ $shifts->total() }})
            </h3>

            @if($shifts->count() > 0)
                <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap:16px; margin-bottom:24px;">
                    @foreach($shifts as $shift)
                    @php
                        $isToday = $shift->shift_date->isToday();
                        $isPast = $shift->shift_date->isPast();
                        $cardBorder = $isToday ? 'var(--success)' : ($isPast ? 'var(--text-light)' : 'var(--primary)');
                        $cardBg = $isToday ? 'rgba(16, 185, 129, 0.03)' : 'var(--bg-glass-strong)';
                    @endphp
                    <div class="glass-card" style="margin:0; padding:20px; border-left:4px solid {{ $cardBorder }}; background:{{ $cardBg }};">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px;">
                            <div>
                                <div style="font-size:1.1rem; font-weight:700; color:var(--text-main);">{{ $shift->shift_date->format('l, F d, Y') }}</div>
                                @if($isToday)
                                    <span class="badge status-active" style="margin-top:4px;">Today</span>
                                @endif
                            </div>
                            @if(strtolower($shift->status) === 'scheduled')
                                <span class="badge status-pending">Scheduled</span>
                            @elseif(strtolower($shift->status) === 'completed')
                                <span class="badge status-approved">Completed</span>
                            @else
                                <span class="badge status-inactive">{{ ucfirst($shift->status) }}</span>
                            @endif
                        </div>

                        <div style="display:flex; flex-direction:column; gap:8px; background:var(--bg-main); padding:14px; border-radius:var(--radius-sm);">
                            <div style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:1rem;">
                                <span style="font-size:1.2rem;"></span>
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                            </div>
                            <div style="display:flex; align-items:center; gap:8px; color:var(--text-muted); font-size:0.9rem;">
                                <span></span>
                                Duration: <strong>{{ \Carbon\Carbon::parse($shift->start_time)->diffInHours(\Carbon\Carbon::parse($shift->end_time)) }} hours</strong>
                            </div>
                        </div>

                        <div style="margin-top:16px; display:flex; justify-content:flex-end;">
                            <form method="POST" action="{{ route('admin.shift.delete', $shift->id) }}" onsubmit="return confirm('Delete this shift?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Delete Shift</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($shifts->hasPages())
                <div class="pagination-container fade-in" style="margin-top:0;">
                    {{ $shifts->links() }}
                </div>
                @endif
            @else
                <div class="glass-card">
                    <div class="empty-state" style="padding:40px 20px;">
                        <div class="empty-icon"></div>
                        <h3>No Shifts Assigned</h3>
                        <p>This guard doesn't have any scheduled shifts yet.</p>
                        <a href="{{ route('admin.shift.management') }}" class="btn-primary" style="margin-top:16px;">Assign a Shift</a>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>
</body>
</html>
