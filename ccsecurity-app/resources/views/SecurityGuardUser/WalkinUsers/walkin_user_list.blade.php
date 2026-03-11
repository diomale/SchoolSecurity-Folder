<div>
    <h1>Walk-in Visitor Accounts</h1>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('security.dashboard') }}">← Back to Dashboard</a> |
        <a href="{{ route('security.walkin.add') }}">Create New Walk-in Account</a>
    </div>

    <!-- Search Form -->
    <form action="{{ route('security.walkin.list') }}" method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by name, email, phone, or QR..." value="{{ request('search') }}" style="width: 300px;">
        <button type="submit">Search</button>
        @if(request('search'))
            <a href="{{ route('security.walkin.list') }}">Clear</a>
        @endif
    </form>

    @if(session('success'))
        <div style="color: green; margin-bottom: 10px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div style="color: red; margin-bottom: 10px;">{{ session('error') }}</div>
    @endif

    <div style="margin-bottom: 10px;">
        <button type="button" onclick="openPasswordModal('bulk-delete-form')" id="bulk-delete-btn" disabled style="background-color: #fff0f0; color: #dc3545; border: 1px solid #dc3545; cursor: pointer;">Bulk Delete Selected</button>
    </div>

    <!-- Hidden Bulk Delete Form -->
    <form id="bulk-delete-form" action="{{ route('security.walkin.bulk-delete') }}" method="POST" style="display:none;">
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

    <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>QR Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($outside_users as $outside_user)
            <tr>
                <td><input type="checkbox" value="{{ $outside_user->id }}" class="user-checkbox"></td>
                <td>{{ $outside_user->fullname ?? ($outside_user->first_name . ' ' . $outside_user->last_name) }}</td>
                <td>{{ $outside_user->email }}</td>
                <td>{{ $outside_user->phone_number ?? 'N/A' }}</td>
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
                        <a href="{{ route('security.user.qr', $outside_user->id) }}" style="background-color: #e7f3ff; padding: 5px 10px; text-decoration: none; border-radius: 4px; border: 1px solid #007bff; color: #007bff;">View QR</a>

                        <form action="{{ route('security.qr.status.toggle', $outside_user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" style="cursor: pointer;">Toggle Status</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No walk-in accounts found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $outside_users->appends(request()->query())->links() }}
    </div>

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
