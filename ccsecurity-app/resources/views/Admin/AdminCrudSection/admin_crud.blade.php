<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inside User Management - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">

    <!-- Sidebar -->
    @include('Admin.partials.sidebar', ['activePage' => 'inside_users'])
    

    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>Inside User <span class="highlight">Management</span></h1>
                <p class="subtitle">Manage students, teachers, and internal staff records</p>
            </div>
            <a href="{{ route('admin.add.user') }}" class="btn-primary">+ Add User</a>
        </div>

        <div class="glass-card fade-in" style="animation-delay:0.1s; padding:0; overflow:hidden;">
            <!-- Toolbar -->
            <div style="padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div class="toolbar-left">
                    <a href="{{ route('admin.add.user') }}" class="btn-primary">Add User+</a>
                    <button type="button" onclick="openPasswordModal('bulk-delete-form')" id="bulk-delete-btn" class="btn-danger" disabled>Bulk Delete</button>
                </div>

                <div class="toolbar-right">
                    <form action="{{ route('admin.show.crudSection') }}" method="GET" class="search-form">
                        <div class="search-input-wrapper">
                            <span class="search-icon"></span>
                            <input type="text" name="search" class="search-input" placeholder="Search by name, email, or role..." value="{{ request('search') }}">
                        </div>
                        <button type="submit" class="btn-secondary">Search</button>
                        @if(request('search'))
                            <a href="{{ route('admin.show.crudSection') }}" class="btn-clear" title="Clear Search">&times;</a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Alerts -->
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Data Table -->
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell"><input type="checkbox" id="select-all" class="custom-checkbox"></th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="actions-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inside_users as $inside_user)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" value="{{ $inside_user->id }}" class="user-checkbox custom-checkbox">
                            </td>
                            <td class="user-name">
                                <div class="avatar-placeholder">{{ substr($inside_user->fullname ?? $inside_user->first_name, 0, 1) }}</div>
                                {{ $inside_user->fullname ?? ($inside_user->first_name . ' ' . $inside_user->last_name) }}
                            </td>
                            <td>{{ $inside_user->email }}</td>
                            <td>
                                <span class="badge role-badge">{{ ucfirst($inside_user->role) }}</span>
                            </td>
                            <td>
                                @if($inside_user->qr_status === 'active')
                                    <span class="badge status-active">Active</span>
                                @else
                                    <span class="badge status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td class="date-cell">{{ \Carbon\Carbon::parse($inside_user->created_at)->format('M d, Y') }}</td>
                            <td class="actions-cell">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.user.details', ['id' => $inside_user->id, 'back_url' => url()->current()]) }}" class="btn-icon btn-view" title="View">View</a>
                                    <a href="{{ route('admin.user.edit.form', ['id' => $inside_user->id, 'back_url' => url()->current()]) }}" class="btn-icon btn-edit" title="Edit">Edit</a>
                                    <button type="button" onclick="openPasswordModal('delete-form-{{ $inside_user->id }}')" class="btn-icon btn-delete" title="Delete">Delete</button>
                                    <form id="delete-form-{{ $inside_user->id }}" action="{{ route('admin.user.delete', $inside_user->id) }}" method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <p>No users found matching your search criteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div><!-- table-container -->

            <!-- Pagination -->
            <div style="padding: 16px 24px;">
                <div class="pagination-container">
                    {{ $inside_users->appends(request()->query())->links() }}
                </div>
            </div>
        </div><!-- glass-card -->
    </main>
</div><!-- dashboard-container -->

    <!-- Hidden Bulk Delete Form -->
    <form id="bulk-delete-form" action="{{ route('admin.user.bulk-delete') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Password Confirmation Modal -->
    <div id="passwordModal" class="modal-overlay">
        <div class="modal-content glass-panel">
            <div class="modal-header">
                <h3>Confirm Your Identity</h3>
                <button type="button" class="close-modal" onclick="closePasswordModal()">&times;</button>
            </div>
            <p class="modal-desc">Please enter your admin password to authorize this deletion.</p>
            <form id="passwordConfirmForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="form-group">
                    <div class="input-wrapper">
                        <span class="input-icon"></span>
                        <input type="password" id="admin_password" name="admin_password" class="form-input" placeholder="Enter password" required>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closePasswordModal()">Cancel</button>
                    <button type="submit" class="btn-danger">Confirm Delete</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
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
            
            const modal = document.getElementById('passwordModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closePasswordModal() {
            const modal = document.getElementById('passwordModal');
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
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
                const allCheckboxes = document.querySelectorAll('.user-checkbox').length;
                const checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked').length;
                document.getElementById('select-all').checked = allCheckboxes === checkedCheckboxes && allCheckboxes > 0;
            }
        });

        function toggleBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            const btn = document.getElementById('bulk-delete-btn');
            btn.disabled = checkedCount === 0;
        }
    </script>
</body>
</html>
