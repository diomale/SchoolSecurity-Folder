<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quick Pass - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardUser/securityguard_style_dashboard.css', 'resources/css/SecurityGuardUser/securityguard_style_quickpass.css'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        @include('SecurityGuardUser.partials.sidebar', ['activePage' => 'quick-pass'])

        <!-- Main Content Area -->
        <main class="main-content">
            <a href="{{ route('security.quick-pass.list') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; font-weight: 600; margin-bottom: 20px;">
                &larr; Back to Quick Pass List
            </a>

            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Create <span class="highlight">Quick Pass</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Generate a temporary QR code for same-day visitor access</p>
                </div>
            </header>

            <div class="glass-card fade-in" style="animation-delay: 0.2s; max-width: 600px;">
                <div class="info-box">
                    <h4>Quick Pass Benefits:</h4>
                    <ul>
                        <li>No email or phone required</li>
                        <li>QR code generated instantly</li>
                        <li>Valid until 11:59 PM today (by default)</li>
                        <li>Perfect for visitors in vehicles</li>
                    </ul>
                </div>

                @if ($errors->any())
                <div class="alert alert-error">
                    <div class="alert-icon">!</div>
                    <div class="alert-content">
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin-left: 20px; margin-top: 5px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <form action="{{ route('security.quick-pass.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="visitor_name">Visitor Name <span class="required">*</span></label>
                        <input type="text" id="visitor_name" name="visitor_name" value="{{ old('visitor_name') }}" placeholder="e.g., John Doe" required autofocus>
                        <small>Full name of the visitor</small>
                    </div>

                    <div class="form-group">
                        <label for="vehicle_plate">Vehicle Plate Number</label>
                        <input type="text" id="vehicle_plate" name="vehicle_plate" value="{{ old('vehicle_plate') }}" placeholder="e.g., ABC-123 (optional)" style="text-transform: uppercase;">
                        <small>Leave blank if visitor is on foot</small>
                    </div>

                    <div class="form-group">
                        <label for="purpose">Purpose of Visit <span class="required">*</span></label>
                        <select id="purpose" name="purpose" required>
                            <option value="">Select Purpose</option>
                            <option value="Delivery" {{ old('purpose') === 'Delivery' ? 'selected' : '' }}>Delivery</option>
                            <option value="Meeting" {{ old('purpose') === 'Meeting' ? 'selected' : '' }}>Meeting</option>
                            <option value="Parent" {{ old('purpose') === 'Parent' ? 'selected' : '' }}>Parent/Guardian</option>
                            <option value="Contractor" {{ old('purpose') === 'Contractor' ? 'selected' : '' }}>Contractor</option>
                            <option value="Other" {{ old('purpose') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="expiry_time">Custom Expiration (Today)</label>
                        <input type="time" id="expiry_time" name="expiry_time" value="{{ old('expiry_time') }}">
                        <small>Leave blank for default (11:59 PM today)</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary" style="flex: 1; justify-content: center;">Generate Quick Pass</button>
                        <a href="{{ route('security.quick-pass.list') }}" class="btn-secondary" style="flex: 1; justify-content: center;">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('vehicle_plate').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>
