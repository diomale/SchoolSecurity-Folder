<div>
    <h1>Security Guard Management</h1>

    <div>
        <a href="{{ route('security.user.add.section') }}">Add+</a>
        <a href="{{ route('admin.shift.management') }}">🕐 Manage Shifts</a>
    </div>

    <!-- Search Form -->
    <form action="{{ route('security.user.table.section') }}" method="GET" style="margin-top: 20px; margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search guards..." value="{{ request('search') }}" style="width: 300px;">
        <button type="submit">Search</button>
        @if(request('search'))
            <a href="{{ route('security.user.table.section') }}">Clear</a>
        @endif
    </form>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    <div style="margin-bottom: 10px;">
        <button type="button" onclick="submitBulkAction('bulk-delete-form', true)" id="bulk-delete-btn" disabled style="background-color: #fff0f0; color: #dc3545; border: 1px solid #dc3545; cursor: pointer;">Bulk Delete Selected</button>
    </div>

    <!-- Hidden Bulk Delete Form -->
    <form id="bulk-delete-form" action="{{ route('admin.security.bulk-delete') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($security_guard_users as $security_guard_user)
            <tr>
                <td><input type="checkbox" value="{{ $security_guard_user->id }}" class="user-checkbox"></td>
                <td>{{ $security_guard_user->first_name }} {{ $security_guard_user->last_name }}</td>
                <td>{{ $security_guard_user->email }}</td>
                <td>{{ $security_guard_user->created_at }}</td>
                <td>{{ $security_guard_user->updated_at }}</td>

                <td>
                    <div style="display: flex; gap: 5px;">
                        <a href="{{ route('admin.guard.shifts', $security_guard_user->id) }}">View Shifts</a>
                        <a href="{{ route('security.guard.user.details', $security_guard_user->id) }}">View</a>
                        <a href="{{ route('security.guard.user.edit', $security_guard_user->id) }}">Edit</a>
                        <form action="{{ route('security.guard.user.delete', $security_guard_user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No security guards found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $security_guard_users->appends(request()->query())->links() }}
    </div>

    <br>
    <a href="{{ route('admin.dashboard') }}">Back</a>

    <script>
        // Select All checkboxes
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleBulkDeleteButton();
        });

        // Toggle button state on individual checkbox change
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            cb.addEventListener('change', toggleBulkDeleteButton);
        });

        function toggleBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            document.getElementById('bulk-delete-btn').disabled = checkedCount === 0;
        }

        function submitBulkAction(formId, isDelete = false) {
            const checkedIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
            const form = document.getElementById(formId);
            
            if (isDelete) {
                if (!confirm(`Are you sure you want to delete ${checkedIds.length} selected security guards?`)) return;
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
</div>