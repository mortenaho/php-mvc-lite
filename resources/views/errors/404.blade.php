@extends('layouts.app')

@section('title', '404')

@section('content')
<section class="hero">
    <p class="eyebrow">{{ $status }}</p>
    <h1>Page not found.</h1>
    <p class="lede">{{ $message }}</p>
    <a class="btn btn-primary" href="{{ url('/') }}">Back home</a>
</section>
@endsection
