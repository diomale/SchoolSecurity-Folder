<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Superadmin/superadmin_style_dashboard.css', 'resources/css/Superadmin/superadmin_style_add_form.css', 'resources/js/app.js'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        @include('superadmin.partials.sidebar')

        <!-- Main Content Area -->
        <main class="main-content">
            <a href="{{ route('superadmin.dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; font-weight: 600; margin-bottom: 20px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg> Back to Dashboard
            </a>

            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Create <span class="highlight">Administrator</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Register a new system admin account.</p>
                </div>
            </header>

            <div class="form-glass-container fade-in" style="animation-delay: 0.2s;">
                <form action="{{ route('superadmin.storeAdmin') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" placeholder="E.g., Jane Doe" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="admin@example.com" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Assign a secure password" required autocomplete="off">
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('superadmin.dashboard') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary" style="margin: 0;">
                            Create Admin
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
