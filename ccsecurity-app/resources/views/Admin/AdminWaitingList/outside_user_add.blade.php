<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Walk-in Visitor - CCSS Admin</title>
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
                <h1>Add <span class="highlight">Walk-in Visitor</span></h1>
                <p class="subtitle">Create a new outsider/visitor account</p>
            </div>
            <a href="{{ route('show.admin.outsider.list') }}" class="btn-secondary">← Back to List</a>
        </div>

        <div class="glass-card fade-in" style="animation-delay: 0.1s; max-width: 680px;">
            <h3>Visitor Information</h3>

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom:20px;">
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.outsider.store') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="first_name" class="form-input" value="{{ old('first_name') }}" required placeholder="Enter first name">
                        @error('first_name')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Last Name <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="last_name" class="form-input" value="{{ old('last_name') }}" required placeholder="Enter last name">
                        @error('last_name')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address <span style="color:var(--danger)">*</span></label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" required autocomplete="off" placeholder="visitor@example.com">
                    @error('email')<span class="error-text">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" class="form-input" value="{{ old('phone_number') }}" placeholder="+63 9xx xxx xxxx">
                    @error('phone_number')<span class="error-text">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Password <span style="color:var(--danger)">*</span></label>
                    <input type="password" name="password" class="form-input" required autocomplete="off" placeholder="Set a secure password">
                    @error('password')<span class="error-text">{{ $message }}</span>@enderror
                </div>

                <div style="display:flex; gap:12px; margin-top:8px;">
                    <button type="submit" class="btn-primary">Create Account</button>
                    <a href="{{ route('show.admin.outsider.list') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
