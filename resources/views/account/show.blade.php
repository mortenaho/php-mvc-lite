@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Session</p>
        <h1>Account</h1>
    </div>
</header>

<section class="panel auth-panel">
    <p><strong>Name:</strong> {{ $account->name }}</p>
    <p><strong>Email:</strong> {{ $account->email }}</p>
    <form method="post" action="{{ url('/logout') }}">
        @csrf
        <button class="btn btn-ghost" type="submit">Log out</button>
    </form>
</section>
@endsection
