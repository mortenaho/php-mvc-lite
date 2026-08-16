@extends('layouts.app')

@section('title', 'خطا')

@section('content')
<section class="hero">
    <p class="eyebrow">{{ $status }}</p>
    <h1>{{ (int) $status === 403 ? 'دسترسی مجاز نیست.' : 'یک خطای غیرمنتظره رخ داد.' }}</h1>
    <p class="lede">{{ $message }}</p>
    @if($app_debug && isset($exception))
        <pre class="debug">{{ $exception }}</pre>
    @endif
    <a class="btn btn-primary" href="{{ url('/') }}">بازگشت به خانه</a>
</section>
@endsection
