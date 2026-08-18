@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Edit</p>
        <h1>{{ $post->title }}</h1>
    </div>
</header>

<form class="panel" method="post" action="{{ url('/posts/' . $post->id) }}" novalidate>
    @csrf
    @include('components.post-form')
    <button class="btn btn-primary" type="submit">Update</button>
</form>
@endsection
