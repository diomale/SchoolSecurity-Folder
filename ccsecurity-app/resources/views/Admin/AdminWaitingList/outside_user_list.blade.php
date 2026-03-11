<div>
    <h1>Visitor Accounts - Approval List</h1>

    <div>
        <p><a href="{{ route('admin.visit.requests') }}">View Visit Requests</a></p>
        <p><a href="{{ route('admin.outsider.add') }}">Create Walk-in Account</a></p>
    </div>

    <!-- Search Form -->
    <form action="{{ route('show.admin.outsider.list') }}" method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by name, email, phone, or QR..." value="{{ request('search') }}" style="width: 300px;">
        <button type="submit">Search</button>
        @if(request('search'))
            <a href="{{ route('show.admin.outsider.list') }}">Clear</a>
        @endif
    </form>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div style="color: blue;">{{ session('info') }}</div>
    @endif

    <div style="margin-bottom: 10px;">
        <button type="button" onclick="submitBulkAction('bulk-delete-form', true)" id="bulk-delete-btn" disabled style="background-color: #fff0f0; color: #dc3545; border: 1px solid #dc3545; cursor: pointer;">Bulk Delete Selected</button>
    </div>

    <!-- Hidden Bulk Delete Form -->
    <form id="bulk-delete-form" action="{{ route('admin.outsider.bulk-delete') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>QR Value</th>
                <th>QR Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($outside_users as $outside_user)
            <tr>
                <td><input type="checkbox" value="{{ $outside_user->id }}" class="user-checkbox"></td>
                <td>{{ $outside_user->id }}</td>
                <td>{{ $outside_user->fullname ?? ($outside_user->first_name . ' ' . $outside_user->last_name) }}</td>
                <td>{{ $outside_user->email }}</td>
                <td>{{ $outside_user->phone_number ?? 'N/A' }}</td>
                <td>{{ $outside_user->qr_value ?? 'N/A' }}</td>
                <td>
                    @if($outside_user->qr_status === 'active')
                        <span style="color: green;">Active ✓</span>
                    @else
                        <span style="color: gray;">Inactive ✗</span>
                    @endif
                </td>
                <td>{{ $outside_user->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>

                <td>
                    <div style="display: flex; gap: 5px;">
                        <a href="{{ route('admin.outsider.edit', $outside_user->id) }}">Edit</a>
                        
                        <form action="{{ route('admin.outsider.delete', $outside_user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>

                        @if($outside_user->status === \App\Models\OutsideUser::STATUS_PENDING)
                            <form action="{{ route('admin.approved.user', $outside_user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="background-color: #e7f3ff; color: #007bff; border: 1px solid #007bff; cursor: pointer;">Approve</button>
                            </form>

                            <form action="{{ route('admin.rejected.user', $outside_user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="background-color: #fff0f0; color: #dc3545; border: 1px solid #dc3545; cursor: pointer;">Reject</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center;">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $outside_users->appends(request()->query())->links() }}
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