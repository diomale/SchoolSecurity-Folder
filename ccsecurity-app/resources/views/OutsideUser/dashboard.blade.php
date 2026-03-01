<div>
    <!-- It is never too late to be what you might have been. - George Eliot -->

    <h1>Dashboard</h1>
    <p>Welcome, {{ auth('outsideuser')->user()->first_name }}</p>
     <form method="POST" action="{{ route('outsideuser.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

</div>
