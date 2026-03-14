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
    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <div style="margin-bottom: 10px;">
        <button type="button" onclick="openPasswordModal('bulk-delete-form')" id="bulk-delete-btn" disabled style="background-color: #fff0f0; color: #dc3545; border: 1px solid #dc3545; cursor: pointer;">Bulk Delete Selected</button>
    </div>

    <!-- Hidden Bulk Delete Form -->
    <form id="bulk-delete-form" action="{{ route('admin.outsider.bulk-delete') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Password Confirmation Modal -->
    <div id="passwordModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div style="background:white; padding:20px; border-radius:8px; max-width:400px; margin:100px auto;">
            <h3 style="margin-top:0;">🔐 Confirm Your Identity</h3>
            <p>Please enter your password to confirm deletion.</p>
            <form id="passwordConfirmForm" method="POST">
                @csrf
                @method('DELETE')
                <div style="margin-bottom:15px;">
                    <label for="admin_password" style="display:block; margin-bottom:5px;">Password:</label>
                    <input type="password" id="admin_password" name="admin_password" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" onclick="closePasswordModal()" style="padding:8px 16px; background:#6c757d; color:white; border:none; border-radius:4px; cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:8px 16px; background:#dc3545; color:white; border:none; border-radius:4px; cursor:pointer;">Confirm Delete</button>
                </div>
            </form>
        </div>
    </div>

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
                        <a href="{{ route('admin.outsider.edit', ['id' => $outside_user->id, 'back_url' => url()->current()]) }}">Edit</a>

                        <button type="button" onclick="openPasswordModal('delete-form-{{ $outside_user->id }}')" style="background:#dc3545; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">Delete</button>
                        <form id="delete-form-{{ $outside_user->id }}" action="{{ route('admin.outsider.delete', $outside_user->id) }}" method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
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
        // Password Modal Functions
        function openPasswordModal(formId) {
            const sourceForm = document.getElementById(formId);
            const targetForm = document.getElementById('passwordConfirmForm');
            
            // Set form action
            targetForm.action = sourceForm.action;
            
            // Clear existing hidden inputs except CSRF
            targetForm.querySelectorAll('input[type="hidden"]').forEach(input => {
                if (input.name !== '_token' && input.name !== '_method') input.remove();
            });
            
            // Copy user_ids from bulk delete form
            if (formId === 'bulk-delete-form') {
                document.querySelectorAll('.user-checkbox:checked').forEach(cb => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'user_ids[]';
                    hiddenInput.value = cb.value;
                    targetForm.appendChild(hiddenInput);
                });
            }
            
            document.getElementById('passwordModal').style.display = 'block';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('passwordModal');
            if (event.target === modal) {
                closePasswordModal();
            }
        }

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
    </script>
</div>
