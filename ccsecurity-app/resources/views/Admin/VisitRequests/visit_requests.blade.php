<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Requests - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">

    <!-- Sidebar -->
    @include('Admin.partials.sidebar', ['activePage' => 'visit_requests'])

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>Visit <span class="highlight">Requests</span></h1>
                <p class="subtitle">Review and approve pending campus visit requests</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success fade-in">{{ session('success') }}</div>
        @endif

        <div class="glass-card fade-in" style="animation-delay: 0.1s; padding:0; overflow:hidden;">
            <div style="padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.05);">
                <h3 style="margin:0; border:none; padding:0;">Incoming Visit Requests</h3>
            </div>

            @if($visitRequests->count() > 0)
                <div class="table-container" style="border-radius:0; border:none;">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Visitor</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Visit Date & Time</th>
                                <th>Purpose</th>
                                <th>Person to Meet</th>
                                <th>Status</th>
                                <th>Requested On</th>
                                <th class="actions-cell">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visitRequests as $request)
                            <tr>
                                <td>
                                    <div class="user-name">
                                        <div class="avatar-placeholder">{{ substr($request->outsideUser->fullname ?? '?', 0, 1) }}</div>
                                        {{ $request->outsideUser->fullname ?? 'N/A' }}
                                    </div>
                                </td>
                                <td>{{ $request->outsideUser->email ?? 'N/A' }}</td>
                                <td>{{ $request->outsideUser->phone_number ?? 'N/A' }}</td>
                                <td class="date-cell">
                                    {{ $request->visit_date->format('M d, Y') }}<br>
                                    <small>{{ $request->visit_time->format('h:i A') }}</small>
                                </td>
                                <td>{{ $request->purpose }}</td>
                                <td>{{ $request->person_to_meet }}</td>
                                <td>
                                    @if($request->status === 'approved')
                                        <span class="badge status-approved">Approved</span>
                                    @elseif($request->status === 'rejected')
                                        <span class="badge status-rejected">Rejected</span>
                                    @else
                                        <span class="badge status-pending">Pending</span>
                                    @endif
                                </td>
                                <td class="date-cell">{{ $request->created_at->format('M d, Y') }}</td>
                                <td class="actions-cell">
                                    @if($request->status === 'pending')
                                        <div class="action-buttons">
                                            <form action="{{ route('admin.visit.approve', $request->id) }}" method="POST" style="display:inline;">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn-icon btn-view" title="Approve">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.visit.reject', $request->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Reject this visit request?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn-icon btn-delete" title="Reject">Reject</button>
                                            </form>
                                        </div>
                                    @else
                                        <span style="color:var(--text-muted); font-size:0.9rem;">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($visitRequests->hasPages())
                    <div style="padding: 16px 24px;">
                        <div class="pagination-container">
                            {{ $visitRequests->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon"></div>
                    <h3>No Visit Requests</h3>
                    <p>There are no visit requests to review at this time.</p>
                </div>
            @endif
        </div>
    </main>
</div>
</body>
</html>
