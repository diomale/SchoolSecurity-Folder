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
        
        <p><a href="{{ route('outsider.dashboard') }}">← Back to Dashboard</a></p>

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

        <div>
            <h2> My QR Code Pass</h2>
            <p>Present this QR code to the guard at the entrance.</p>
            
            <div>
                @if($outsideUser->qr_value)
                    <div>
                        {!! QrCode::size(250)->margin(1)->generate($outsideUser->qr_value) !!}
                    </div>
                    <div>
                        <span>
                            {{ $outsideUser->qr_value }}
                        </span>
                    </div>
                    <p>
                        Status:
                        @if($outsideUser->qr_status === 'active')
                            <span>● ACTIVE (You can visit)</span>
                        @else
                            <span>● INACTIVE (Visit request needed)</span>
                        @endif
                    </p>
                @else
                    <p>No QR code generated yet.</p>
                @endif
            </div>

            <div>
                <label>Account Status:</label>
                <p>
                    @if($outsideUser->status == 1)
                        <span>✓ Approved</span>
                    @elseif($outsideUser->status == 2)
                        <span>✗ Rejected</span>
                    @else
                        <span>⏳ Pending Approval</span>
                    @endif
                </p>
            </div>

            <div>
                @if($outsideUser->status == 1)
                    <a href="{{ route('outsideuser.visit.request') }}">Request a Visit</a>
                    @if($outsideUser->qr_status === 'active')
                    <a href="{{ route('outsideuser.reactivate.qr') }}">Request Another Visit</a>
                    @endif
                @endif
            </div>
        </div>

        <div>
            <h2> My Visit Statistics</h2>
            <div>
                <div>
                    <h3>{{ $outsideUser->visitRequests->count() }}</h3>
                    <p>Total Requests</p>
                </div>
                <div>
                    <h3>{{ $outsideUser->visitRequests->where('status', 'approved')->count() }}</h3>
                    <p>Approved</p>
                </div>
                <div>
                    <h3>{{ $outsideUser->visitRequests->where('status', 'pending')->count() }}</h3>
                    <p>Pending</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
