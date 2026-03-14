<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Visitor</title>
</head>
<body>
    <div>
        <h1> My Profile</h1>
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
            
            <form action="{{ route('outsideuser.profile.update') }}" method="POST">
                @csrf
                
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
</body>
</html>
