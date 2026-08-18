<header class="site-head">
    <a class="brand" href="{{ url('/') }}">
        <span class="brand-mark">L</span>
        <span class="brand-copy">
            <strong>{{ $app_name }}</strong>
            <small>A lightweight PHP framework</small>
        </span>
    </a>
    <nav class="nav">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ url('/posts') }}">Posts</a>
        @if($user)
            <a href="{{ url('/posts/create') }}">New post</a>
            <a href="{{ url('/account') }}">{{ $user->name }}</a>
            <form method="post" action="{{ url('/logout') }}">
                @csrf
                <button type="submit">Log out</button>
            </form>
        @else
            <a href="{{ url('/login') }}">Log in</a>
            <a class="nav-cta" href="{{ url('/register') }}">Register</a>
        @endif
    </nav>
</header>
