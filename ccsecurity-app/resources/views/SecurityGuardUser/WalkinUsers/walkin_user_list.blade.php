<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walk-in Intruders/Visitors - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardStyleFolder/securityguard_style_dashboard.css', 'resources/css/SecurityGuardStyleFolder/securityguard_style_walkin.css'])
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
            <div class="action-header fade-in">
                <div class="header-left">
                    <h1 style="margin:0; font-size: 1.8rem; color: var(--text-main);">Walk-in <span class="highlight">Accounts</span></h1>
                    <p class="subtitle" style="margin: 5px 0 0 0;">Manage un-registered temporary visitors</p>
                </div>
                <div class="header-right" style="display: flex; gap: 15px;">
                    <button type="button" onclick="openPasswordModal('bulk-delete-form')" id="bulk-delete-btn" class="btn-danger" disabled>
                        🗑️ Bulk Delete Selected
                    </button>
                    <a href="{{ route('security.walkin.add') }}" class="btn-primary">
                        📝 Create Walk-in Account
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success fade-in">
                    <div class="alert-icon">✓</div>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error fade-in">
                    <div class="alert-icon">!</div>
                    <div class="alert-content">{{ session('error') }}</div>
                </div>
            @endif

            <!-- Search Form -->
            <div class="search-glass-container fade-in" style="animation-delay: 0.1s;">
                <form action="{{ route('security.walkin.list') }}" method="GET" class="search-form-flex">
                    <div class="search-input-wrapper">
                        <span class="search-icon">🔍</span>
                        <input type="text" name="search" placeholder="Search by name, email, phone, or QR..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn-primary">Search</button>
                    @if(request('search'))
                        <a href="{{ route('security.walkin.list') }}" class="btn-clear">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Hidden Bulk Delete Form -->
            <form id="bulk-delete-form" action="{{ route('security.walkin.bulk-delete') }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>

            <div class="glass-card full-width fade-in" style="animation-delay: 0.2s;">
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all" class="custom-checkbox"></th>
                                <th>Full Name</th>
                                <th>Contact Details</th>
                                <th>QR Status</th>
                                <th>Generated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($outside_users as $outside_user)
                            <tr>
                                <td><input type="checkbox" value="{{ $outside_user->id }}" class="user-checkbox custom-checkbox"></td>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-small" style="background: var(--warning);">{{ substr($outside_user->fullname ?? ($outside_user->first_name . ' ' . $outside_user->last_name), 0, 1) }}</div>
                                        <span class="full-name">{{ $outside_user->fullname ?? ($outside_user->first_name . ' ' . $outside_user->last_name) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <span style="color: var(--text-main); font-size: 0.95rem;">{{ $outside_user->email }}</span>
                                        <span style="color: var(--text-muted); font-size: 0.85rem;">{{ $outside_user->phone_number ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $isExpired = $outside_user->qr_expires_at && \Carbon\Carbon::now()->gt($outside_user->qr_expires_at);
                                    @endphp
                                    @if($isExpired)
                                        <span class="badge badge-danger">Expired</span>
                                    @elseif($outside_user->qr_status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-outline">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <span style="color: var(--text-main); font-size: 0.95rem;">{{ $outside_user->created_at?->format('M d, Y') ?? 'N/A' }}</span>
                                        @if($outside_user->qr_expires_at)
                                            <span style="color: var(--danger); font-size: 0.85rem; font-weight: 500;">Exp: {{ $outside_user->qr_expires_at->format('M d, h:i A') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('security.user.qr', ['id' => $outside_user->id, 'type' => 'outside', 'from' => 'walkin']) }}" class="action-btn btn-view">👁️ View QR</a>
                                        <form action="{{ route('security.qr.status.toggle', ['id' => $outside_user->id, 'type' => 'outside']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="action-btn btn-toggle" onclick="return confirm('Toggle status for this walk-in user?')">🔄 Toggle</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">No walk-in accounts found.</td>
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

            <!-- Password Confirmation Modal -->
            <div id="passwordModal" class="modal-overlay">
                <div class="modal-content">
                    <h3>🔐 Confirm Action</h3>
                    <p>Please enter your password to confirm bulk deletion of selected walk-in accounts.</p>
                    <form id="passwordConfirmForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-input-group">
                            <label for="admin_password">Guard Password</label>
                            <input type="password" id="admin_password" name="admin_password" required placeholder="Enter password to confirm" autocomplete="off">
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn-outline" style="padding: 10px 20px; border: 1px solid rgba(0,0,0,0.2); color: var(--text-muted);" onclick="closePasswordModal()">Cancel</button>
                            <button type="submit" class="btn-danger" style="padding: 10px 20px; border-radius: var(--radius-sm);">Confirm Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function openPasswordModal(formId) {
            const sourceForm = document.getElementById(formId);
            const targetForm = document.getElementById('passwordConfirmForm');
            targetForm.action = sourceForm.action;
            
            targetForm.querySelectorAll('input[type="hidden"]').forEach(input => {
                if (input.name !== '_token' && input.name !== '_method') input.remove();
            });
            
            if (formId === 'bulk-delete-form') {
                document.querySelectorAll('.user-checkbox:checked').forEach(cb => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'user_ids[]';
                    hiddenInput.value = cb.value;
                    targetForm.appendChild(hiddenInput);
                });
            }
            
            document.getElementById('passwordModal').style.display = 'flex';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('passwordModal');
            if (event.target === modal) {
                closePasswordModal();
            }
        }

        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleBulkDeleteButton();
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('user-checkbox')) {
                toggleBulkDeleteButton();
            }
        });

        function toggleBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            document.getElementById('bulk-delete-btn').disabled = checkedCount === 0;
        }
    </script>
</body>
</html>
