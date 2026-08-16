@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Eloquent</p>
        <h1>نوشته‌ها</h1>
    </div>
    <a class="btn btn-primary" href="{{ url('/posts/create') }}">نوشته تازه</a>
</header>

@if($posts->isEmpty())
    <div class="empty">
        <p>هنوز نوشته‌ای نیست.</p>
        <a class="btn btn-primary" href="{{ url('/posts/create') }}">اولین نوشته را بساز</a>
    </div>
@else
    <div class="post-list">
        @foreach($posts as $post)
            @include('components.post-card')
        @endforeach
    </div>
@endif
@endsection
