@extends('layouts.app')

@section('content')
<section class="hero">
    <p class="eyebrow">PHP 8.2 · MVC · OOP</p>
    <h1>یک هسته کوچک،<br>با ابزارهای درست.</h1>
    <p class="lede">
        مسیرها با FastRoute، داده با Eloquent، و ویو با Blade کامپایل‌شده.
        کنترلرها نازک می‌مانند؛ قوانین در سرویس و مدل می‌نشینند.
    </p>
    <div class="hero-actions">
        <a class="btn btn-primary" href="{{ url('/posts') }}">دیدن نوشته‌ها</a>
        <a class="btn btn-ghost" href="{{ url('/posts/create') }}">ساخت اولین نوشته</a>
    </div>
</section>

<section class="grid-3">
    <article class="card">
        <h2>هسته SOLID</h2>
        <p>Container با autowiring، Kernel، میدل‌ویر و جداسازی HTTP از دامنه.</p>
    </article>
    <article class="card">
        <h2>Eloquent ORM</h2>
        <p>مدل، مهاجرت، Query Builder و رابطه — همان Illuminate Database.</p>
    </article>
    <article class="card">
        <h2>Blade</h2>
        <p>وراثت لایه، escape پیش‌فرض، دایرکتیوهای آشنای لاراول و کش قالب کامپایل‌شده.</p>
    </article>
</section>

@if($posts->isNotEmpty())
<section class="stack">
    <header class="section-head">
        <h2>آخرین نوشته‌ها</h2>
        <a href="{{ url('/posts') }}">همه</a>
    </header>
    <div class="post-list">
        @foreach($posts as $post)
            @include('components.post-card')
        @endforeach
    </div>
</section>
@endif
@endsection
