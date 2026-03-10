<div>
    <h1>Add Walk-in Visitor Account</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.outsider.store') }}" method="POST">
        @csrf

        <div>
            <label>First Name:</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required>
        </div>
        <div>
            <label>Last Name:</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="off">
        </div>
        <div>
            <label>Phone Number:</label>
            <input type="text" name="phone_number" value="{{ old('phone_number') }}">
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required autocomplete="off">
        </div>
        <div>
            <label>Purpose of Visit:</label>
            <input type="text" name="purpose_of_visit" value="{{ old('purpose_of_visit') }}" required>
        </div>

        <button type="submit">Create Account</button>
    </form>

    <br>
    <a href="{{ route('show.admin.outsider.list') }}">Back to List</a>
</div>