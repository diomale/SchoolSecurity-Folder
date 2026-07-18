<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Status Management - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardUser/securityguard_style_dashboard.css', 'resources/css/SecurityGuardUser/securityguard_style_qr_status_management.css'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        @include('SecurityGuardUser.partials.sidebar', ['activePage' => 'qr-status'])

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">QR Status <span class="highlight">Management</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Activate or deactivate QR code access for all users</p>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success fade-in">
                    <div class="alert-icon">✓</div>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
            @endif

            <!-- Summary Statistics -->
            <div class="summary-stats-container fade-in" style="animation-delay: 0.2s;">
                <div class="summary-card">
                    <div class="summary-icon icon-students">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <div class="summary-info">
                        <h3>Total Students</h3>
                        <p>{{ $students->total() }}</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon icon-staff">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </div>
                    <div class="summary-info">
                        <h3>Total Staff</h3>
                        <p>{{ $staff->total() }}</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon icon-visitors">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="summary-info">
                        <h3>Total Visitors</h3>
                        <p>{{ $outside_users->total() }}</p>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="search-glass-container fade-in" style="animation-delay: 0.3s;">
                <form method="GET" action="{{ route('security.qr.status.management') }}" class="search-form-flex">
                    <div class="search-input-wrapper">
                        <span class="search-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                        <input type="text" name="search" placeholder="Search by ID, Name, Email, or QR Value..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn-primary">Search</button>
                    @if(request('search'))
                        <a href="{{ route('security.qr.status.management') }}" class="btn-clear">Clear Filter</a>
                    @endif
                </form>
            </div>

            <!-- Students Table -->
            <div class="glass-card full-width fade-in" style="animation-delay: 0.4s; margin-bottom: 30px;">
                <h2 class="section-title">Students Data</h2>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>QR Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $user)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-small bg-primary">{{ substr($user->fullname ?? ($user->first_name . ' ' . $user->last_name), 0, 1) }}</div>
                                        <span class="full-name">{{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}</span>
                                    </div>
                                </td>
                                <td><span style="color: var(--text-muted); font-size: 0.95rem;">{{ $user->email }}</span></td>
                                <td>
                                    @if(in_array(strtolower($user->qr_status), ['active']))
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-outline">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('security.user.qr', ['id' => $user->id, 'type' => 'inside', 'from' => 'qr-status']) }}" class="action-btn btn-view"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 2px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> View QR</a>
                                        <form action="{{ route('security.qr.status.toggle', ['id' => $user->id, 'type' => 'inside']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            @if(in_array(strtolower($user->qr_status), ['active']))
                                                <button type="submit" class="action-btn btn-deactivate" onclick="return confirm('Deactivate QR for {{ $user->fullname ?? $user->first_name }}?')">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 2px;"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="submit" class="action-btn btn-activate" onclick="return confirm('Activate QR for {{ $user->fullname ?? $user->first_name }}?')">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                    Activate
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">No students found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($students->hasPages())
                <div class="pagination-wrapper">
                    {{ $students->appends(request()->query())->links() }}
                </div>
                @endif
            </div>

            <!-- Staff Table -->
            <div class="glass-card full-width fade-in" style="animation-delay: 0.5s; margin-bottom: 30px;">
                <h2 class="section-title">Staff Data</h2>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>QR Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staff as $user)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-small" style="background: var(--success);">{{ substr($user->fullname ?? ($user->first_name . ' ' . $user->last_name), 0, 1) }}</div>
                                        <span class="full-name">{{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}</span>
                                    </div>
                                </td>
                                <td><span style="color: var(--text-muted); font-size: 0.95rem;">{{ $user->email }}</span></td>
                                <td>
                                    @if(in_array(strtolower($user->qr_status), ['active']))
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-outline">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('security.user.qr', ['id' => $user->id, 'type' => 'inside', 'from' => 'qr-status']) }}" class="action-btn btn-view"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 2px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> View QR</a>
                                        <form action="{{ route('security.qr.status.toggle', ['id' => $user->id, 'type' => 'inside']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            @if(in_array(strtolower($user->qr_status), ['active']))
                                                <button type="submit" class="action-btn btn-deactivate" onclick="return confirm('Deactivate QR for {{ $user->fullname ?? $user->first_name }}?')">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 2px;"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="submit" class="action-btn btn-activate" onclick="return confirm('Activate QR for {{ $user->fullname ?? $user->first_name }}?')">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                    Activate
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">No staff members found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($staff->hasPages())
                <div class="pagination-wrapper">
                    {{ $staff->appends(request()->query())->links() }}
                </div>
                @endif
            </div>

            <!-- Outside Users Table -->
            <div class="glass-card full-width fade-in" style="animation-delay: 0.6s;">
                <h2 class="section-title">Visitors Data</h2>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Contact</th>
                                <th>QR Status</th>
                                <th>Account Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($outside_users as $user)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-small" style="background: var(--warning);">{{ substr($user->fullname ?? ($user->first_name . ' ' . $user->last_name), 0, 1) }}</div>
                                        <span class="full-name">{{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <span style="color: var(--text-main); font-size: 0.95rem;">{{ $user->email }}</span>
                                        <span style="color: var(--text-muted); font-size: 0.85rem;">{{ $user->phone_number ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if(in_array(strtolower($user->qr_status), ['active']))
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-outline">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->status == \App\Models\OutsideUser::STATUS_APPROVED)
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($user->status == \App\Models\OutsideUser::STATUS_REJECTED)
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('security.user.qr', ['id' => $user->id, 'type' => 'outside', 'from' => 'qr-status']) }}" class="action-btn btn-view"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 2px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> View QR</a>
                                        <form action="{{ route('security.qr.status.toggle', ['id' => $user->id, 'type' => 'outside']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            @if(in_array(strtolower($user->qr_status), ['active']))
                                                <button type="submit" class="action-btn btn-deactivate" onclick="return confirm('Deactivate QR for {{ $user->fullname ?? $user->first_name }}?')">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 2px;"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="submit" class="action-btn btn-activate" onclick="return confirm('Activate QR for {{ $user->fullname ?? $user->first_name }}?')">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                    Activate
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">No visitor users found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($outside_users->hasPages())
                <div class="pagination-wrapper">
                    {{ $outside_users->appends(request()->query())->links() }}
                </div>
                @endif
            </div>

        </main>
    </div>
</body>
</html>
