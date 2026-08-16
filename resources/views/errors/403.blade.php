@extends('layouts.app')

@section('title', '۴۰۳')

@section('content')
<section class="hero">
    <p class="eyebrow">{{ $status }}</p>
    <h1>درخواست رد شد.</h1>
    <p class="lede">{{ $message }}</p>
    <a class="btn btn-primary" href="{{ url('/') }}">بازگشت به خانه</a>
</section>
@endsection
