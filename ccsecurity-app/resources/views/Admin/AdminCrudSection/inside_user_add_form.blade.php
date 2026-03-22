
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/AdminStyleFolder/admin_style_inside_user_add_form.css', 'resources/js/app.js'])
</head>
<body>
    <div class="bg-animation">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div class="form-container">
        <div class="glass-panel">
            <div class="form-header">
                <div class="logo-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
                </div>
                <h1>Add New User</h1>
                <p>Register a new system member</p>
            </div>

            @if ($errors->any())
                <div class="error-alert">
                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 500;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        Please fix the following errors:
                    </div>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.add.user.accept') }}" method="POST" class="user-form">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <div class="input-wrapper">
                            <span class="input-icon">✎</span>
                            <input type="text" name="first_name" class="form-input" placeholder="e.g. John" value="{{ old('first_name') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <div class="input-wrapper">
                            <span class="input-icon">✎</span>
                            <input type="text" name="last_name" class="form-input" placeholder="e.g. Doe" value="{{ old('last_name') }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">✉</span>
                        <input type="email" name="email" class="form-input" placeholder="john.doe@example.com" value="{{ old('email') }}" required autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" class="form-input" placeholder="Enter password" required autocomplete="off">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="role">User Role</label>
                        <div class="select-wrapper">
                            <span class="input-icon" style="z-index: 2; top: 12px;">👤</span>
                            <select name="role" id="role" class="form-select">
                                <option value="student">Student</option>
                                <option value="staff">Staff</option>
                                <option value="security_guard">Security Guard</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="qr_status">QR Status</label>
                        <div class="select-wrapper">
                            <span class="input-icon" style="z-index: 2; top: 12px;">⏺</span>
                            <select name="qr_status" id="qr_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        Submit Registration
                    </button>
                    <a href="{{ route('admin.show.crudSection') }}" class="back-link">
                        &larr; Return to User Management
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>