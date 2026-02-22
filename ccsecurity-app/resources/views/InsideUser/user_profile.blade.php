<div>
    <!-- Let all your things have their places; let each part of your business have its time. - Benjamin Franklin -->
    <p> {{ auth('insideuser')->user()->fullname }}</p>
    <p>{{ auth('insideuser')->user()->role }}</p>
    <p>{{ auth('insideuser')->user()->email }}</p>
    <p>{{ auth('insideuser')->user()->qr_value }}</p>
    <p>{{ auth('insideuser')->user()->qr_status }}</p>

    <a href="{{ route('insideuser.dashboard') }}">Back</a>
</div>
