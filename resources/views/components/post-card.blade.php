<article class="post-card">
    <a href="{{ url('/posts/' . $post->id) }}">
        <time>{{ $post->created_at->format('M j, Y') }}</time>
        <h3>{{ $post->title }}</h3>
        <p>{{ $post->excerpt }}</p>
    </a>
</article>
