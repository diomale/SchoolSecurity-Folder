<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outsider Management - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">

    <!-- Sidebar -->
    @include('Admin.partials.sidebar', ['activePage' => 'outsider_management'])

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>Outsider <span class="highlight">Management</span></h1>
                <p class="subtitle">Manage visitor accounts and external passes</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('admin.visit.requests') }}" class="btn-info">Visit Requests</a>
                <a href="{{ route('admin.outsider.add') }}" class="btn-primary">+ Add Walk-in</a>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success fade-in">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info fade-in">{{ session('info') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger fade-in">{{ session('error') }}</div>
        @endif

        <!-- Glass Card -->
        <div class="glass-card fade-in" style="animation-delay: 0.1s; padding:0; overflow:hidden;">

            <!-- Toolbar -->
            <div style="padding: 20px 24px; border-bottom: 1px solid rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div style="display:flex; gap:10px; align-items:center;">
                    <button type="button" onclick="openPasswordModal('bulk-delete-form')"
                        id="bulk-delete-btn" class="btn-danger btn-sm" disabled>
                        Bulk Delete
                    </button>
                </div>
                <form action="{{ route('show.admin.outsider.list') }}" method="GET" class="search-form">
                    <div class="search-input-wrapper">
                        <span class="search-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></span>
                        <input type="text" name="search" class="search-input" placeholder="Search name, email, phone..."
                            value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn-secondary btn-sm">Search</button>
                    @if(request('search'))
                        <a href="{{ route('show.admin.outsider.list') }}" class="btn-clear btn-sm">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="table-container" style="border-radius:0; border:none;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell"><input type="checkbox" id="select-all" class="custom-checkbox"></th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>QR Status</th>
                            <th>Approval</th>
                            <th>Created At</th>
                            <th class="actions-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($outside_users as $outside_user)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" value="{{ $outside_user->id }}" class="user-checkbox custom-checkbox">
                            </td>
                            <td class="user-name">
                                {{ $outside_user->fullname ?? ($outside_user->first_name . ' ' . $outside_user->last_name) }}
                            </td>
                            <td>{{ $outside_user->email }}</td>
                            <td>{{ $outside_user->phone_number ?? 'N/A' }}</td>
                            <td>
                                @if($outside_user->qr_status === 'active')
                                    <span class="badge status-active">Active</span>
                                @else
                                    <span class="badge status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($outside_user->status === \App\Models\outsideuser::STATUS_PENDING)
                                    <span class="badge status-pending">Pending</span>
                                @elseif($outside_user->status === \App\Models\outsideuser::STATUS_APPROVED)
                                    <span class="badge status-approved">Approved</span>
                                @else
                                    <span class="badge status-rejected">Rejected</span>
                                @endif
                            </td>
                            <td class="date-cell">{{ $outside_user->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="actions-cell">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.outsider.edit', ['id' => $outside_user->id, 'back_url' => url()->current()]) }}"
                                        class="btn-icon btn-edit" title="Edit"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>

                                    <button type="button"
                                        onclick="openPasswordModal('delete-form-{{ $outside_user->id }}')"
                                        class="btn-icon btn-delete" title="Delete"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m3 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6h14z"/></svg></button>
                                    <form id="delete-form-{{ $outside_user->id }}"
                                        action="{{ route('admin.outsider.delete', $outside_user->id) }}"
                                        method="POST" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>

                                    @if($outside_user->status === \App\Models\outsideuser::STATUS_PENDING)
                                        <form action="{{ route('admin.approved.user', $outside_user->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-icon btn-view" title="Approve"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></button>
                                        </form>
                                        <form action="{{ route('admin.rejected.user', $outside_user->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-icon btn-delete" title="Reject"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                                    <h3>No users found</h3>
                                    <p>No outsider accounts match your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 24px;">
                <div class="pagination-container">
                    {{ $outside_users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Hidden Bulk Delete Form -->
<form id="bulk-delete-form" action="{{ route('admin.outsider.bulk-delete') }}" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

<!-- Password Confirmation Modal -->
<div id="passwordModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Your Identity</h3>
            <button type="button" class="close-modal" onclick="closePasswordModal()">&times;</button>
        </div>
        <p class="modal-desc">Enter your admin password to authorize this action.</p>
        <form id="passwordConfirmForm" method="POST">
            @csrf @method('DELETE')
            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" id="admin_password" name="admin_password" class="form-input" placeholder="Enter password" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closePasswordModal()">Cancel</button>
                <button type="submit" class="btn-danger">Confirm Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPasswordModal(formId) {
        const sourceForm = document.getElementById(formId);
        const targetForm = document.getElementById('passwordConfirmForm');
        targetForm.action = sourceForm.action;
        targetForm.querySelectorAll('input[type="hidden"]').forEach(i => { if (i.name !== '_token' && i.name !== '_method') i.remove(); });
        if (formId === 'bulk-delete-form') {
            document.querySelectorAll('.user-checkbox:checked').forEach(cb => {
                const h = document.createElement('input'); h.type='hidden'; h.name='user_ids[]'; h.value=cb.value; targetForm.appendChild(h);
            });
        }
        const modal = document.getElementById('passwordModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }
    function closePasswordModal() {
        const modal = document.getElementById('passwordModal');
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }
    window.onclick = e => { if (e.target === document.getElementById('passwordModal')) closePasswordModal(); };

    document.getElementById('select-all').addEventListener('change', function() {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
        toggleBulk();
    });
    document.addEventListener('change', e => { if (e.target.classList.contains('user-checkbox')) toggleBulk(); });
    function toggleBulk() {
        document.getElementById('bulk-delete-btn').disabled = document.querySelectorAll('.user-checkbox:checked').length === 0;
    }
</script>
</body>
</html>
