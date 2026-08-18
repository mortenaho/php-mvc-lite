@extends('layouts.app')

@section('content')
<article class="article">
    <p class="eyebrow">{{ $post->created_at->format('M j, Y') }}</p>
    <h1>{{ $post->title }}</h1>
    <p class="lede">{{ $post->excerpt }}</p>
    @if($post->user)
        <p class="muted">By {{ $post->user->name }}</p>
    @endif
    <div class="prose">{!! nl2br(e($post->body)) !!}</div>
    @can('update', $post)
    <div class="hero-actions">
        <a class="btn btn-ghost" href="{{ url('/posts/' . $post->id . '/edit') }}">Edit</a>
        <form method="post" action="{{ url('/posts/' . $post->id . '/delete') }}" onsubmit="return confirm('Delete this post?')">
            @csrf
            <button class="btn btn-danger" type="submit">Delete</button>
        </form>
    </div>
    @endcan
</article>
@endsection
