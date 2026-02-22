<div>
    <!-- The biggest battle is the war against ignorance. - Mustafa Kemal Atatürk -->
    <h1>dashboard</h1>
    <p>Welcome, {{ auth('securityguard')->user()->first_name }}</p>

    <form method="POST" action="{{ route('security.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>


</div>
