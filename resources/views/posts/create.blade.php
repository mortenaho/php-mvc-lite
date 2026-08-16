@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">فرم + اعتبارسنجی</p>
        <h1>نوشته تازه</h1>
    </div>
</header>

<form class="panel" method="post" action="{{ url('/posts') }}" novalidate>
    @csrf
    @include('components.post-form')
    <button class="btn btn-primary" type="submit">ذخیره</button>
</form>
@endsection
