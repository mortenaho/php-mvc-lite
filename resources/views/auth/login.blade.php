@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Authentication</p>
        <h1>Log in</h1>
    </div>
</header>

<form class="panel auth-panel" method="post" action="{{ url('/login') }}" novalidate>
    @csrf
    <label class="field {{ error('email') ? 'is-invalid' : '' }}">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
        @if(error('email'))<small>{{ error('email') }}</small>@endif
    </label>
    <label class="field {{ error('password') ? 'is-invalid' : '' }}">
        <span>Password</span>
        <input type="password" name="password" required autocomplete="current-password">
        @if(error('password'))<small>{{ error('password') }}</small>@endif
    </label>
    <button class="btn btn-primary" type="submit">Log in</button>
    <p class="auth-switch">No account? <a href="{{ url('/register') }}">Register</a></p>
    <p class="auth-hint">Demo: <code>demo@lite.test</code> / <code>password</code></p>
</form>
@endsection
