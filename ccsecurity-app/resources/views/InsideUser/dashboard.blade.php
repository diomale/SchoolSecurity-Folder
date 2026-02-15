<div>
    <!-- The biggest battle is the war against ignorance. - Mustafa Kemal Atatürk -->
    <h1>dashboard</h1>
    <p>Welcome, {{ auth('insideuser')->user()->fullname }}</p>
    {{-- <p>Role {{ auth('insideuser')->user()->role }}</p> --}}

    <form method="POST" action="{{ route('insideuser.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

    <a href="{{ route('insideuser.profile.show') }}">Profile</a>

</div>
