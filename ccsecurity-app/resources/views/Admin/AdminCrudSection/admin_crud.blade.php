<div>
    <h1>Inside User Management</h1>

    <div>
        <a href="{{ route('admin.add.user') }}">Add User+</a>
    </div>

    <!-- Search Form -->
    <form action="{{ route('admin.show.crudSection') }}" method="GET" style="margin-top: 20px; margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by name, email, or role..." value="{{ request('search') }}" style="width: 300px;">
        <button type="submit">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.show.crudSection') }}">Clear</a>
        @endif
    </form>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    <div style="margin-bottom: 10px;">
        <button type="button" onclick="submitBulkAction('bulk-delete-form', true)" id="bulk-delete-btn" disabled style="background-color: #fff0f0; color: #dc3545; border: 1px solid #dc3545; cursor: pointer;">Bulk Delete Selected</button>
    </div>

    <!-- Hidden Bulk Delete Form -->
    <form id="bulk-delete-form" action="{{ route('admin.user.bulk-delete') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>QR Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inside_users as $inside_user)
            <tr>
                <td><input type="checkbox" value="{{ $inside_user->id }}" class="user-checkbox"></td>
                <td>{{ $inside_user->fullname ?? ($inside_user->first_name . ' ' . $inside_user->last_name) }}</td>
                <td>{{ $inside_user->email }}</td>
                <td>{{ ucfirst($inside_user->role) }}</td>
                <td>
                    @if($inside_user->qr_status === 'active')
                        <span style="color: green;">Active ✓</span>
                    @else
                        <span style="color: gray;">Inactive ✗</span>
                    @endif
                </td>
                <td>{{ $inside_user->created_at }}</td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <a href="{{ route('admin.user.details', $inside_user->id) }}">View</a>
                        <a href="{{ route('admin.user.edit.form', $inside_user->id) }}">Edit</a>
                        <form action="{{ route('admin.user.delete', $inside_user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $inside_users->appends(request()->query())->links() }}
    </div>

    <br>
    <a href="{{ route('admin.dashboard') }}">Back to Dashboard</a>

    <script>
        // Select All checkboxes
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleBulkDeleteButton();
        });

        // Toggle button state on individual checkbox change
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('user-checkbox')) {
                toggleBulkDeleteButton();
            }
        });

        function toggleBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            document.getElementById('bulk-delete-btn').disabled = checkedCount === 0;
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
</div>