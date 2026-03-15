<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Status Management - Admin</title>
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <h1>QR Status Management</h1>
            <a href="{{ route('admin.dashboard') }}">← Back to Dashboard</a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div style="color: green; margin: 10px 0;">
            {{ session('success') }}
        </div>
        @endif

        <!-- Search and Bulk Actions -->
        <div style="margin-bottom: 20px;">
            <form method="GET" action="{{ route('admin.qr.status.management') }}">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by ID, Name, Email, or QR Value..." 
                    value="{{ request('search') }}"
                    style="width: 300px;"
                >
                <button type="submit">Search</button>
                @if(request('search'))
                <a href="{{ route('admin.qr.status.management') }}">Clear</a>
                @endif
            </form>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 10px;">
            <!-- Bulk Toggle Status Actions -->
            <form id="bulk-toggle-form" method="POST" action="{{ route('admin.qr.status.bulk.toggle') }}">
                @csrf
                <label>Bulk Status:</label>
                <select name="new_status">
                    <option value="active">Activate</option>
                    <option value="inactive">Deactivate</option>
                </select>
                <button type="button" onclick="submitBulkAction('bulk-toggle-form')" class="bulk-btn" disabled>
                    Apply to Selected
                </button>
            </form>

            <!-- Bulk Delete Action -->
            <form id="bulk-delete-form" method="POST" action="{{ route('admin.inside-user.bulk-delete') }}">
                @csrf
                @method('DELETE')
                <button type="button" onclick="submitBulkAction('bulk-delete-form', true)" class="bulk-btn" style="background-color: #fff0f0; color: #dc3545; border: 1px solid #dc3545;" disabled>
                    Bulk Delete Selected
                </button>
            </form>
        </div>

        <!-- Students Table -->
        <div style="margin-top: 30px;">
            <h2>Students</h2>
            <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th>
                            <input type="checkbox" class="select-all" data-target="student-checkbox">
                        </th>
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
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox student-checkbox">
                        </td>
                        <td>{{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if(in_array(strtolower($user->qr_status), ['active']))
                                <span style="color: green;"> Active</span>
                            @else
                                <span style="color: gray;"> Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <form action="{{ route('admin.qr.status.toggle', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    @if(in_array(strtolower($user->qr_status), ['active']))
                                        <button type="submit" onclick="return confirm('Deactivate QR for {{ $user->fullname }}?')">
                                            Deactivate
                                        </button>
                                    @else
                                        <button type="submit" onclick="return confirm('Activate QR for {{ $user->fullname }}?')">
                                            Activate
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No students found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination for Students -->
            @if($students->hasPages())
            <div style="margin-top: 20px;">
                {{ $students->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        <!-- Staff Table -->
        <div style="margin-top: 50px;">
            <h2>Staff</h2>
            <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th>
                            <input type="checkbox" class="select-all" data-target="staff-checkbox">
                        </th>
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
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox staff-checkbox">
                        </td>
                        <td>{{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if(in_array(strtolower($user->qr_status), ['active']))
                                <span style="color: green;"> Active</span>
                            @else
                                <span style="color: gray;"> Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <form action="{{ route('admin.qr.status.toggle', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    @if(in_array(strtolower($user->qr_status), ['active']))
                                        <button type="submit" onclick="return confirm('Deactivate QR for {{ $user->fullname }}?')">
                                            Deactivate
                                        </button>
                                    @else
                                        <button type="submit" onclick="return confirm('Activate QR for {{ $user->fullname }}?')">
                                            Activate
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No staff members found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination for Staff -->
            @if($staff->hasPages())
            <div style="margin-top: 20px;">
                {{ $staff->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        <!-- Stats Summary -->
        <div style="margin-top: 30px; display: flex; gap: 40px;">
            <div>
                <strong>Total Students:</strong> {{ $students->total() }}
            </div>
            <div>
                <strong>Total Staff:</strong> {{ $staff->total() }}
            </div>
        </div>
    </div>

    <script>
        // Select all checkbox functionality
        document.querySelectorAll('.select-all').forEach(selectAll => {
            selectAll.addEventListener('change', function() {
                const targetClass = this.getAttribute('data-target');
                const checkboxes = document.querySelectorAll('.' + targetClass);
                checkboxes.forEach(cb => cb.checked = this.checked);
                toggleBulkButtons();
            });
        });

        // Individual checkbox functionality
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('user-checkbox')) {
                toggleBulkButtons();
            }
        });

        function toggleBulkButtons() {
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            document.querySelectorAll('.bulk-btn').forEach(btn => {
                btn.disabled = checkedCount === 0;
            });
        }

        function submitBulkAction(formId, isDelete = false) {
            const checkedIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
            const form = document.getElementById(formId);
            
            if (isDelete) {
                if (!confirm(`Are you sure you want to delete ${checkedIds.length} selected users?`)) return;
            }

            // Clear existing hidden inputs for user_ids
            form.querySelectorAll('input[name="user_ids[]"]').forEach(el => el.remove());

            // Add selected IDs as hidden inputs to the form
            checkedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            form.submit();
        }
    </script>
</body>
</html>