<div>
    <h1>Create Walk-in Visitor Account</h1>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('security.walkin.list') }}">← Back to List</a>
    </div>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 20px; border: 1px solid red; padding: 10px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('security.walkin.store') }}" method="POST" style="max-width: 500px;">
        @csrf

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">First Name:</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Last Name:</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Email:</label>
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="off" style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Phone Number:</label>
            <input type="text" name="phone_number" value="{{ old('phone_number') }}" style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Password:</label>
            <input type="password" name="password" required autocomplete="off" style="width: 100%; padding: 8px;">
            <small style="color: gray;">Visitor will need this to log in later.</small>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Purpose of Visit:</label>
            <input type="text" name="purpose_of_visit" value="{{ old('purpose_of_visit') }}" required style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Create Account & Activate QR</button>
        </div>
    </form>
</div>