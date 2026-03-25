<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Visitor</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Load custom profile CSS via Vite -->
    @vite(['resources/css/OutsideUser/outsideuser_style_profile.css'])
</head>
<body>
    <div class="profile-container">
        <!-- Navigation/Header Bar -->
        <nav class="top-nav">
            <a href="{{ route('outsider.dashboard') }}" class="back-link">
                <span class="icon">⟵</span> Back to Dashboard
            </a>
            <div class="nav-right">
                <span class="nav-text">Columban College School Security</span>
                <div class="logo-circle">CCSS</div>
            </div>
        </nav>

        <main class="main-content">
            <div class="glass-card profile-card fade-in">
                <div class="card-header">
                    <h1>My Profile</h1>
                    <p class="subtitle">Update your personal information and profile picture.</p>
                </div>

                @if(session('success'))
                <div class="alert alert-success">
                    <div class="alert-icon">✓</div>
                    <div class="alert-content">
                        {{ session('success') }}
                    </div>
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-error">
                    <div class="alert-icon">!</div>
                    <div class="alert-content">
                        <strong>Please fix the following errors:</strong>
                        <ul class="error-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <form action="{{ route('outsideuser.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
                    @csrf

                    <!-- Profile Section Grid -->
                    <div class="form-grid">
                        
                        <!-- Left Column: Picture -->
                        <div class="picture-section">
                            <label class="section-label">Profile Picture</label>
                            <div class="profile-picture-container">
                                <div class="current-picture">
                                    @if($outsideUser->profile_picture)
                                        <img src="{{ asset('storage/profiles/' . $outsideUser->profile_picture) }}" alt="Profile Picture" id="profile-preview">
                                    @else
                                        <img src="{{ asset('storage/profiles/default-avatar.png') }}" alt="Default Avatar" id="profile-preview" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2240%22></text></svg>'">
                                    @endif
                                </div>
                                <div class="picture-actions">
                                    <input type="file" name="profile_picture" id="profile_picture" class="file-input" accept="image/*" onchange="previewImage(event)">
                                    <label for="profile_picture" class="btn btn-outline btn-sm">Choose New Picture</label>
                                    
                                    @if($outsideUser->profile_picture)
                                        <button type="button" onclick="removePicture()" class="btn btn-danger-outline btn-sm mt-3">Remove Picture</button>
                                        <input type="hidden" name="remove_profile_picture" id="remove_profile_picture" value="0">
                                    @endif
                                </div>
                            </div>
                            <small class="helper-text text-center block mt-3">Max size: 2MB. Allowed: JPG, PNG, GIF</small>
                        </div>

                        <!-- Right Column: Details -->
                        <div class="details-section">
                            <h3 class="section-title">Personal Details</h3>
                            
                            <div class="input-row">
                                <div class="form-group">
                                    <label>First Name <span class="required">*</span></label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $outsideUser->first_name) }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Last Name <span class="required">*</span></label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $outsideUser->last_name) }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" class="form-control input-disabled" value="{{ $outsideUser->email }}" disabled>
                                <small class="helper-text">Email cannot be changed</small>
                            </div>

                            <div class="form-group">
                                <label>Phone Number <span class="required">*</span></label>
                                <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $outsideUser->phone_number) }}" required>
                            </div>

                            <div class="divider"></div>

                            <h3 class="section-title">Security</h3>
                            <p class="section-desc">Leave password fields blank if you don't want to change it.</p>

                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter new password">
                            </div>

                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                            </div>

                            <div class="form-group highlight-group">
                                <label>Current Password</label>
                                <input type="password" name="current_password" class="form-control" placeholder="Enter your current password">
                                <small class="helper-text text-warning">Required only if changing your password</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('outsider.dashboard') }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary pulse-hover">Save Changes</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('profile-preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removePicture() {
            const removeInput = document.getElementById('remove_profile_picture');
            const preview = document.getElementById('profile-preview');
            const fileInput = document.getElementById('profile_picture');
            
            if (confirm('Are you sure you want to remove your profile picture?')) {
                removeInput.value = '1';
                preview.src = 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2240%22></text></svg>';
                fileInput.value = '';
            }
        }
    </script>
</body>
</html>
