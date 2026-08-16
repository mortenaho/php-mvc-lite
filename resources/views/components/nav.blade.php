<header class="site-head">
    <a class="brand" href="{{ url('/') }}">
        <span class="brand-mark">L</span>
        <span class="brand-copy">
            <strong>{{ $app_name }}</strong>
            <small>فریم‌ورک PHP سبک</small>
        </span>
    </a>
    <nav class="nav">
        <a href="{{ url('/') }}">خانه</a>
        <a href="{{ url('/posts') }}">نوشته‌ها</a>
        <a class="nav-cta" href="{{ url('/posts/create') }}">نوشته تازه</a>
    </nav>
</header>
