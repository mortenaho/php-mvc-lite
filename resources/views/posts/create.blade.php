@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Form + validation</p>
        <h1>New post</h1>
    </div>
</header>

<form class="panel" method="post" action="{{ url('/posts') }}" novalidate>
    @csrf
    @include('components.post-form')
    <button class="btn btn-primary" type="submit">Save</button>
</form>
@endsection
