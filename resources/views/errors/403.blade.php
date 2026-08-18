@extends('layouts.app')

@section('title', '403')

@section('content')
<section class="hero">
    <p class="eyebrow">{{ $status }}</p>
    <h1>Request denied.</h1>
    <p class="lede">{{ $message }}</p>
    <a class="btn btn-primary" href="{{ url('/') }}">Back home</a>
</section>
@endsection
