@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Authentication</p>
        <h1>Register</h1>
    </div>
</header>

<form class="panel auth-panel" method="post" action="{{ url('/register') }}" novalidate>
    @csrf
    <label class="field {{ error('name') ? 'is-invalid' : '' }}">
        <span>Name</span>
        <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name">
        @if(error('name'))<small>{{ error('name') }}</small>@endif
    </label>
    <label class="field {{ error('email') ? 'is-invalid' : '' }}">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
        @if(error('email'))<small>{{ error('email') }}</small>@endif
    </label>
    <label class="field {{ error('password') ? 'is-invalid' : '' }}">
        <span>Password</span>
        <input type="password" name="password" required autocomplete="new-password">
        @if(error('password'))<small>{{ error('password') }}</small>@endif
    </label>
    <label class="field">
        <span>Confirm password</span>
        <input type="password" name="password_confirmation" required autocomplete="new-password">
    </label>
    <button class="btn btn-primary" type="submit">Create account</button>
    <p class="auth-switch">Already registered? <a href="{{ url('/login') }}">Log in</a></p>
</form>
@endsection
