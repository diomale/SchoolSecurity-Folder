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

    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <div style="margin-bottom: 10px;">
        <button type="button" onclick="openPasswordModal('bulk-delete-form')" id="bulk-delete-btn" disabled style="background-color: #fff0f0; color: #dc3545; border: 1px solid #dc3545; cursor: pointer;">Bulk Delete Selected</button>
    </div>

    <!-- Hidden Bulk Delete Form -->
    <form id="bulk-delete-form" action="{{ route('admin.security.bulk-delete') }}" method="POST" style="display:none;">
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
                        <a href="{{ route('admin.guard.shifts', ['id' => $security_guard_user->id, 'back_url' => url()->current()]) }}">View Shifts</a>
                        <a href="{{ route('security.guard.user.details', ['id' => $security_guard_user->id, 'back_url' => url()->current()]) }}">View</a>
                        <a href="{{ route('security.guard.user.edit', ['id' => $security_guard_user->id, 'back_url' => url()->current()]) }}">Edit</a>
                        <button type="button" onclick="openPasswordModal('delete-form-{{ $security_guard_user->id }}')" style="background:#dc3545; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">Delete</button>
                        <form id="delete-form-{{ $security_guard_user->id }}" action="{{ route('security.guard.user.delete', $security_guard_user->id) }}" method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
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
