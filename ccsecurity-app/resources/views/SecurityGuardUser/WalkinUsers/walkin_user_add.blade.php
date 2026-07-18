<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Walk-in Account - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardUser/securityguard_style_dashboard.css', 'resources/css/SecurityGuardUser/securityguard_style_walkin.css'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        @include('SecurityGuardUser.partials.sidebar', ['activePage' => 'walkin'])

        <!-- Main Content Area -->
        <main class="main-content">
            <a href="{{ route('security.walkin.list') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; font-weight: 600; margin-bottom: 20px;">
                &larr; Back to Walk-in List
            </a>

            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Create <span class="highlight">Walk-in Account</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Register a new temporary visitor</p>
                </div>
            </header>

            @if ($errors->any())
                <div class="alert alert-error fade-in">
                    <div class="alert-icon">!</div>
                    <div class="alert-content">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="form-glass-container fade-in" style="animation-delay: 0.2s;">
                <form action="{{ route('security.walkin.store') }}" method="POST">
                    @csrf

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="e.g. John">
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="e.g. Doe">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="off" placeholder="john.doe@example.com">
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="+63 9...">
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required autocomplete="off" placeholder="Assign a secure password">
                        <small>Visitor will need this to log in later via the Outside User portal.</small>
                    </div>

                    <div class="form-group">
                        <label>Purpose of Visit</label>
                        <input type="text" name="purpose_of_visit" value="{{ old('purpose_of_visit') }}" required placeholder="e.g. Meeting with Registrar">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-success">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            Create Account &amp; Activate QR
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
