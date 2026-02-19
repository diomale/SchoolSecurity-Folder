<div>
    <p>Welcome, {{ auth('admin')->user()->name }}</p>

    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

    <a href="{{ route('admin.show.crudSection') }}">ADD USER</a>
    <a href="{{ route('show.admin.outsider.list') }}">Show List</a>

    
</div>
