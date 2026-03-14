<div>
    <!-- Nothing in life is to be feared, it is only to be understood. Now is the time to understand more, so that we may fear less. - Maria Skłodowska-Curie -->
    <h1>User Details</h1>
    <div>
        <ul>
            <li>First name: {{ $security_guard_user->first_name }}</li>
            <li>Last name: {{ $security_guard_user->last_name }}</li>
            <li>Email: {{ $security_guard_user->email }}</li>
        </ul>
    </div>

    <a href="{{ $backUrl }}">Back</a>
</div>
