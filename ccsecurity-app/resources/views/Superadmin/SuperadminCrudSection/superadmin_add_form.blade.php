<div>
    <h1>Add Admin Form</h1>

    <form action="{{ route('superadmin.storeAdmin' ) }}" method="POST">
        @csrf
        
        <input type="text" name="name" placeholder="Name: " required>
        <input type="email" name="email" placeholder="Email: " required>
        <input type="password" name="password" placeholder="Password: " required>

        <button type="submit">Submit</button>

    </form>
</div>
