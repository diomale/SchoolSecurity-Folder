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
    @vite(['resources/css/SecurityGuardStyleFolder/securityguard_style_dashboard.css', 'resources/css/SecurityGuardStyleFolder/securityguard_style_walkin.css'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-circle">CCSS</div>
                <h2 style="font-size:1.1rem; line-height:1.2;">Columban College<br><small style="font-weight: 500; font-size: 0.85rem; color: var(--text-muted);">Security System</small></h2>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('security.dashboard') }}" class="tab-button" style="text-decoration: none;">
                    <span class="nav-icon">📊</span> Back to Command
                </a>
            </nav>
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('security.logout') }}" style="width: 100%;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="nav-icon">🚪</span> Logout
                    </button>
                </form>
            </div>
        </aside>

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
                            📝 Create Account & Activate QR
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>