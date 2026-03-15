<div>
    <h3>User Profile</h3>
    <p><strong>Name:</strong> {{ auth('insideuser')->user()->fullname }}</p>
    <p><strong>Email:</strong> {{ auth('insideuser')->user()->email }}</p>

    <div class="qr-code-section" style="margin: 20px 0;">
        <div class="qr-code-container">
            {!! QrCode::size(200)->margin(1)->generate(auth('insideuser')->user()->qr_value) !!}
        </div>

        <p style="color: #666; font-style: italic; margin-top: 10px;">
            <em>Present this QR code to the security guard at the entrance.</em>
        </p>
    </div>

    <p><strong>QR Status:</strong> 
        @if(auth('insideuser')->user()->qr_status === 'active')
            <span style="color: #4caf50; font-weight: 600;">● ACTIVE</span>
        @else
            <span style="color: #f44336; font-weight: 600;">● INACTIVE</span>
        @endif
    </p>

    <a href="{{ route('insideuser.dashboard') }}">Back</a>
</div>