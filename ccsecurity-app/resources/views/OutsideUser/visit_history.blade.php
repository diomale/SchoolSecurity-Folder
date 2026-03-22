<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit History - School Security</title>
    @vite(['resources/css/OutsideUSerStyleFolder/outsideuser_style_visit_history.css'])
</head>
<body>
    <div class="history-container">
        <div class="glass-card">
            
            <div class="page-header">
                <div class="header-title">
                    <h1>My Visit History</h1>
                </div>
                <a href="{{ route('outsider.dashboard') }}" class="btn-back">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Back to Dashboard
                </a>
            </div>

            @if($visitRequests->count() > 0)
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Visit Date</th>
                            <th>Time</th>
                            <th>Purpose</th>
                            <th>Person to Meet</th>
                            <th>Status</th>
                            <th>Admin Remarks</th>
                            <th>Requested On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visitRequests as $request)
                        <tr>
                            <td class="date-cell">
                                <span class="date">{{ $request->visit_date->format('M d, Y') }}</span>
                            </td>
                            <td>{{ $request->visit_time->format('h:i A') }}</td>
                            <td style="font-weight: 500; color: var(--text-main);">{{ $request->purpose }}</td>
                            <td>{{ $request->person_to_meet }}</td>
                            <td>
                                @if($request->status === 'approved')
                                    <span class="status-badge status-approved">Approved</span>
                                @elseif($request->status === 'rejected')
                                    <span class="status-badge status-rejected">Rejected</span>
                                @else
                                    <span class="status-badge status-pending">Pending</span>
                                @endif
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.9rem;">
                                    {{ $request->admin_remarks ? $request->admin_remarks : '-' }}
                                </span>
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.9rem;">
                                    {{ $request->created_at->format('M d, Y') }}<br>
                                    <small>{{ $request->created_at->format('h:i A') }}</small>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($visitRequests->hasPages())
            <div class="pagination-wrapper">
                {{ $visitRequests->links() }}
            </div>
            @endif

            @else
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-light);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <p>You haven't made any visit requests yet.</p>
                <a href="{{ route('outsideuser.visit.request') }}" class="btn btn-primary">Request a Visit</a>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
