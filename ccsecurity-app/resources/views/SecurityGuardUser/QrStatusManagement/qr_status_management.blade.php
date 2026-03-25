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
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-circle">CCSS</div>
                <h2 style="font-size:1.1rem; line-height:1.2;">Columban College<br><small style="font-weight: 500; font-size: 0.85rem; color: var(--text-muted);">Security System</small></h2>
            </div>
            <nav class="sidebar-nav">
                <!-- Direct linking -->
                <a href="{{ route('security.dashboard') }}" class="tab-button active" style="text-decoration: none;">
                    <span class="nav-icon">📊</span> Back to Command
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
                    <div class="summary-icon icon-students">🎓</div>
                    <div class="summary-info">
                        <h3>Total Students</h3>
                        <p>{{ $students->total() }}</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon icon-staff">💼</div>
                    <div class="summary-info">
                        <h3>Total Staff</h3>
                        <p>{{ $staff->total() }}</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon icon-visitors">👤</div>
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
                        <span class="search-icon">🔍</span>
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
                <h2 class="section-title">🎓 Students Data</h2>
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
                                        <a href="{{ route('security.user.qr', ['id' => $user->id, 'type' => 'inside', 'from' => 'qr-status']) }}" class="action-btn btn-view">👁️ View QR</a>
                                        <form action="{{ route('security.qr.status.toggle', ['id' => $user->id, 'type' => 'inside']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            @if(in_array(strtolower($user->qr_status), ['active']))
                                                <button type="submit" class="action-btn btn-deactivate" onclick="return confirm('Deactivate QR for {{ $user->fullname ?? $user->first_name }}?')">🚫 Deactivate</button>
                                            @else
                                                <button type="submit" class="action-btn btn-activate" onclick="return confirm('Activate QR for {{ $user->fullname ?? $user->first_name }}?')">✅ Activate</button>
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
                <h2 class="section-title">💼 Staff Data</h2>
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
                                        <a href="{{ route('security.user.qr', ['id' => $user->id, 'type' => 'inside', 'from' => 'qr-status']) }}" class="action-btn btn-view">👁️ View QR</a>
                                        <form action="{{ route('security.qr.status.toggle', ['id' => $user->id, 'type' => 'inside']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            @if(in_array(strtolower($user->qr_status), ['active']))
                                                <button type="submit" class="action-btn btn-deactivate" onclick="return confirm('Deactivate QR for {{ $user->fullname ?? $user->first_name }}?')">🚫 Deactivate</button>
                                            @else
                                                <button type="submit" class="action-btn btn-activate" onclick="return confirm('Activate QR for {{ $user->fullname ?? $user->first_name }}?')">✅ Activate</button>
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
                <h2 class="section-title">👤 Visitors Data</h2>
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
                                        <a href="{{ route('security.user.qr', ['id' => $user->id, 'type' => 'outside', 'from' => 'qr-status']) }}" class="action-btn btn-view">👁️ View QR</a>
                                        <form action="{{ route('security.qr.status.toggle', ['id' => $user->id, 'type' => 'outside']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            @if(in_array(strtolower($user->qr_status), ['active']))
                                                <button type="submit" class="action-btn btn-deactivate" onclick="return confirm('Deactivate QR for {{ $user->fullname ?? $user->first_name }}?')">🚫 Deactivate</button>
                                            @else
                                                <button type="submit" class="action-btn btn-activate" onclick="return confirm('Activate QR for {{ $user->fullname ?? $user->first_name }}?')">✅ Activate</button>
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
