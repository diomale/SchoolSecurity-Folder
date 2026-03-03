<div>
    <!-- I have not failed. I've just found 10,000 ways that won't work. - Thomas Edison -->

    <h2>{{ auth('admin')->user()->name }}</h2>
    <p>{{ auth('admin')->user()->email }}</p>
    
    <div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>

    <a href="{{ route('admin.dashboard') }}">Back</a>
</div>
