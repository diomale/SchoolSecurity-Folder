<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Visitor</title>
</head>
<body>
    <div>
        <h1>My Profile</h1>
        @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div>
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div>
            <h2>Profile Information</h2>

            <form action="{{ route('outsideuser.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Profile Picture Section -->
                <div>
                    <label>Profile Picture</label>
                    <div class="profile-picture-container">
                        <div class="current-picture">
                            @if($outsideUser->profile_picture)
                                <img src="{{ asset('storage/profiles/' . $outsideUser->profile_picture) }}" alt="Profile Picture" id="profile-preview">
                            @else
                                <img src="{{ asset('storage/profiles/default-avatar.png') }}" alt="Default Avatar" id="profile-preview" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2240%22></text></svg>'">
                            @endif
                        </div>
                        <div class="picture-actions">
                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*" onchange="previewImage(event)">
                            <label for="profile_picture" class="btn-upload">Choose New Picture</label>
                            @if($outsideUser->profile_picture)
                                <button type="button" onclick="removePicture()" class="btn-remove">Remove Picture</button>
                                <input type="hidden" name="remove_profile_picture" id="remove_profile_picture" value="0">
                            @endif
                        </div>
                    </div>
                    <small>Max size: 2MB. Allowed: JPG, PNG, GIF</small>
                </div>

                <div>
                    <label>First Name *</label>
                    <input
                        type="text"
                        name="first_name"
                        value="{{ old('first_name', $outsideUser->first_name) }}"
                        required
                    >
                </div>

                <div>
                    <label>Last Name *</label>
                    <input
                        type="text"
                        name="last_name"
                        value="{{ old('last_name', $outsideUser->last_name) }}"
                        required
                    >
                </div>

                <div>
                    <label>Email</label>
                    <input
                        type="email"
                        value="{{ $outsideUser->email }}"
                        disabled
                    >
                    <small>Email cannot be changed</small>
                </div>

                <div>
                    <label>Phone Number *</label>
                    <input
                        type="text"
                        name="phone_number"
                        value="{{ old('phone_number', $outsideUser->phone_number) }}"
                        required
                    >
                </div>

                <div>
                    <label>New Password (leave blank to keep current)</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Enter new password"
                    >
                </div>

                <div>
                    <label>Confirm New Password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirm new password"
                    >
                </div>

                <div>
                    <label>Current Password (required to change password)</label>
                    <input
                        type="password"
                        name="current_password"
                        placeholder="Enter your current password"
                    >
                    <small>Enter your current password to set a new one</small>
                </div>

                <div>
                    <button type="submit">Update Profile</button>
                </div>
            </form>
        </div>
        <p><a href="{{ route('outsider.dashboard') }}">Cancel</a></p>
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
