<div>
    <p>Welcome, {{ auth('admin')->user()->name }}</p>

    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

    <a href="{{ route('admin.show.crudSection') }}">Add Inside User</a>
    <a href="{{ route('show.admin.outsider.list') }}">Show Waiting List</a>
    <a href="{{ route('security.user.table.section') }}">Add Security User</a>
    
</div>
