@extends('layouts.app')

@section('title', 'Error')

@section('content')
<section class="hero">
    <p class="eyebrow">{{ $status }}</p>
    <h1>{{ (int) $status === 403 ? 'You are not allowed to do that.' : 'Something went wrong.' }}</h1>
    <p class="lede">{{ $message }}</p>
    @if($app_debug && isset($exception))
        <pre class="debug">{{ $exception }}</pre>
    @endif
    <a class="btn btn-primary" href="{{ url('/') }}">Back home</a>
</section>
@endsection
