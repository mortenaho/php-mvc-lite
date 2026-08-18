@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Eloquent</p>
        <h1>Posts</h1>
    </div>
    @if($user)
        <a class="btn btn-primary" href="{{ url('/posts/create') }}">New post</a>
    @endif
</header>

@if($posts->isEmpty())
    <div class="empty">
        <p>No posts yet.</p>
        @if($user)
            <a class="btn btn-primary" href="{{ url('/posts/create') }}">Write the first post</a>
        @else
            <a class="btn btn-primary" href="{{ url('/login') }}">Log in to write</a>
        @endif
    </div>
@else
    <div class="post-list">
        @foreach($posts as $post)
            @include('components.post-card')
        @endforeach
    </div>
@endif
@endsection
