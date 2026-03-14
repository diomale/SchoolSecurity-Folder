@vite(['resources/css/SuperadminStyleFolder/superadmin_style_details.css','resources/js/app.js'])

<div>
    <h1>Admin Details</h1>
    <div class="details-box">
        <ul>
            <li><strong>Name: </strong> {{ $admin->name }}</li>
            <li><strong>Email: </strong>{{ $admin->email }}</li>
        </ul>
    </div>
    <a href="{{ route('superadmin.dashboard') }}">Back</a>
</div>
