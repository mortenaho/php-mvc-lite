@extends('layouts.app')

@section('content')
<section class="hero">
    <p class="eyebrow">PHP 8.2 · MVC · OOP</p>
    <h1>A small core,<br>with the right tools.</h1>
    <p class="lede">
        Routes with FastRoute, data with Eloquent, views with compiled Blade.
        Controllers stay thin; rules live in services and models.
    </p>
    <div class="hero-actions">
        <a class="btn btn-primary" href="{{ url('/posts') }}">Browse posts</a>
        @if($user)
            <a class="btn btn-ghost" href="{{ url('/posts/create') }}">Write a post</a>
        @else
            <a class="btn btn-ghost" href="{{ url('/register') }}">Register</a>
        @endif
    </div>
</section>

<section class="grid-3">
    <article class="card">
        <h2>SOLID core</h2>
        <p>A container with autowiring, a Kernel, middleware, and a split between HTTP and domain code.</p>
    </article>
    <article class="card">
        <h2>Eloquent ORM</h2>
        <p>Models, migrations, the query builder, and relationships — Illuminate Database.</p>
    </article>
    <article class="card">
        <h2>Blade</h2>
        <p>Layouts, escaped output by default, familiar Laravel directives, and compiled template caching.</p>
    </article>
</section>

@if($posts->isNotEmpty())
<section class="stack">
    <header class="section-head">
        <h2>Latest posts</h2>
        <a href="{{ url('/posts') }}">View all</a>
    </header>
    <div class="post-list">
        @foreach($posts as $post)
            @include('components.post-card')
        @endforeach
    </div>
</section>
@endif
@endsection
