<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ $csrf_token }}">
    <title>@yield('title', $title ?? $app_name) · {{ $app_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;700;800&family=Fraunces:opsz,wght@9..144,500;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('components.nav')

    <main class="shell">
        @if(!empty($flash['success']))
            <p class="flash flash-ok">{{ $flash['success'] }}</p>
        @endif
        @if(!empty($flash['error']))
            <p class="flash flash-error">{{ $flash['error'] }}</p>
        @endif

        @yield('content')
    </main>

    <footer class="site-foot">
        <span>Lite MVC</span>
        <span>Eloquent · Blade · SOLID</span>
    </footer>
</body>
</html>
