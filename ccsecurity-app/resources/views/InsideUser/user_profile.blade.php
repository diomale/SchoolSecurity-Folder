<div>
    <h3>User Profile</h3>
    <p><strong>Name:</strong> {{ auth('insideuser')->user()->fullname }}</p>
    <p><strong>Email:</strong> {{ auth('insideuser')->user()->email }}</p>
    
    <div class="qr-code-section" style="margin: 20px 0;">
        {!! QrCode::size(200)->margin(1)->generate(auth('insideuser')->user()->qr_value) !!}
        
        <p style="font-family: monospace; color: #666;">
            {{ auth('insideuser')->user()->qr_value }}
        </p>
    </div>

    <p><strong>Status:</strong> {{ auth('insideuser')->user()->qr_status }}</p>

    <a href="{{ route('insideuser.dashboard') }}">Back</a>
</div>