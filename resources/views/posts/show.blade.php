@extends('layouts.app')

@section('content')
<article class="article">
    <p class="eyebrow">{{ $post->created_at->format('Y/m/d') }}</p>
    <h1>{{ $post->title }}</h1>
    <p class="lede">{{ $post->excerpt }}</p>
    <div class="prose">{!! nl2br(e($post->body)) !!}</div>
    <div class="hero-actions">
        <a class="btn btn-ghost" href="{{ url('/posts/' . $post->id . '/edit') }}">ویرایش</a>
        <form method="post" action="{{ url('/posts/' . $post->id . '/delete') }}" onsubmit="return confirm('حذف شود؟')">
            @csrf
            <button class="btn btn-danger" type="submit">حذف</button>
        </form>
    </div>
</article>
@endsection
