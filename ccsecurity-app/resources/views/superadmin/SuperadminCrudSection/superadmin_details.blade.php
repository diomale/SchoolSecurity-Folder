<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Details - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Superadmin/superadmin_style_dashboard.css', 'resources/css/Superadmin/superadmin_style_details.css', 'resources/js/app.js'])
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
                    <h1 class="fade-in">Administrator <span class="highlight">Details</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Viewing information for Admin ID #{{ $admin->id }}</p>
                </div>
            </header>

            <div class="details-glass-container fade-in" style="animation-delay: 0.2s;">
                <div class="details-header">
                    <span style="color: #000; font-weight: 600;">{{ $admin->name }}</span>
                    <div>
                        <h2 style="margin: 0; font-size: 1.8rem; color: var(--primary-dark);">{{ $admin->name }}</h2>
                        <span style="color: var(--success); font-weight: 600; display: inline-flex; align-items: center; gap: 5px; margin-top: 5px;">
                            <span style="display: block; width: 8px; height: 8px; border-radius: 50%; background: var(--success);"></span> Active System Admin
                        </span>
                    </div>
                </div>

                <ul class="details-list">
                    <li>
                        <strong>Full Name</strong>
                        <span>{{ $admin->name }}</span>
                    </li>
                    <li>
                        <strong>Email Address</strong>
                        <span>{{ $admin->email }}</span>
                    </li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
