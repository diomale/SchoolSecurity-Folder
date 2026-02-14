<div>
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->
    <h1>User Details</h1>
    <div>
        <ul>
            <li>First name: {{ $inside_user->first_name }}</li>
            <li>Last name: {{ $inside_user->last_name }}</li>
            <li>Email: {{ $inside_user->email }}</li>
            <li>Role: {{ $inside_user->role }}</li>
        </ul>
    </div>


    <a href="{{ route('admin.show.crudSection') }}">Back</a>
</div>
