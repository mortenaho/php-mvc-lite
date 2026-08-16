<?php

declare(strict_types=1);

return [
    'index.html' => [
        'title' => 'Introduction',
        'next' => ['install.html', 'Install'],
        'content' => <<<'HTML'
<section class="hero">
    <p class="eyebrow">PHP 8.2 · MVC · OOP</p>
    <h1 class="hero-title">Lite MVC</h1>
    <p class="lede">A small PHP MVC framework with a layered structure, dependency injection, Eloquent, and Blade. The core stays tiny; data and templates are left to mature libraries.</p>
    <div class="pills">
        <span>Eloquent</span>
        <span>Blade</span>
        <span>FastRoute</span>
        <span>PSR-11</span>
    </div>
</section>

<div class="grid-3">
    <article class="card">
        <h3>SOLID core</h3>
        <p>A PSR-11 container with autowiring, a Kernel, middleware, and a clear split between HTTP and domain code.</p>
    </article>
    <article class="card">
        <h3>Eloquent</h3>
        <p>Laravel’s ORM for models, migrations, the query builder, and relationships — without the rest of Laravel.</p>
    </article>
    <article class="card">
        <h3>Blade</h3>
        <p>Layouts, <code>@csrf</code>, escaped output by default, and compiled template caching.</p>
    </article>
</div>

<h2>What this documentation is</h2>
<p>This site is the framework reference and a short learning path. If you are new, start with <a href="install.html">Install</a>, then follow the <a href="tutorial.html">tutorial</a>. For how the pieces fit together, read <a href="architecture.html">Architecture</a>.</p>

<h2>Request flow</h2>
<pre><code>public/index.php
    → Application::handle()
    → Kernel (global middleware)
    → Router
    → Controller
    → Blade view or JSON
    → Response</code></pre>

<h2>What is not in the core</h2>
<ul>
    <li>Ready-made auth — build it with sessions and a User model</li>
    <li>Queues and jobs — omitted on purpose to stay light</li>
    <li>A hand-rolled ORM — Eloquent is used instead</li>
</ul>
HTML,
    ],

    'install.html' => [
        'title' => 'Install',
        'prev' => ['index.html', 'Introduction'],
        'next' => ['tutorial.html', 'Tutorial'],
        'content' => <<<'HTML'
<p class="eyebrow">Getting started</p>
<h1>Install</h1>
<p class="lede">Requires PHP 8.2+ with PDO SQLite or MySQL, and Composer.</p>

<h2>1. Create a project</h2>
<pre><code>composer create-project phpmvc/lite my-app
cd my-app</code></pre>
<p>Or clone the repository:</p>
<pre><code>git clone git@github.com:mortenaho/php-mvc-lite.git
cd php-mvc-lite
composer install
cp .env.example .env
php lite key:generate</code></pre>

<h2>2. Migrate</h2>
<p>SQLite is the default. The database file is created at <code>storage/database/app.sqlite</code>.</p>
<pre><code>php lite migrate</code></pre>

<h2>3. Development server</h2>
<pre><code>php lite serve</code></pre>
<p>Open <a href="http://127.0.0.1:8000">http://127.0.0.1:8000</a>. To change the port:</p>
<pre><code>php lite serve --port=8080</code></pre>

<h2>Apache</h2>
<p>If you can change the document root, point it at the <code>public</code> directory. <code>public/.htaccess</code> sends all routes to the front controller.</p>

<h2>Hosts that cannot change the document root</h2>
<p>On many shared hosts the document root is locked to <code>public_html</code>. Upload the whole project into that folder.</p>
<p>The root <code>.htaccess</code> rewrites every request into <code>public</code>, serves static files from <code>public/assets</code>, and blocks direct access to <code>.env</code>, <code>vendor</code>, <code>src</code>, and <code>storage</code>. If <code>mod_rewrite</code> is unavailable, the root <code>index.php</code> still boots the home page.</p>
<p>Set the real site URL in <code>.env</code>:</p>
<pre><code>APP_URL=https://example.com</code></pre>
<p>If the project lives in a subdirectory:</p>
<pre><code>APP_URL=https://example.com/my-app</code></pre>

<h2>MySQL</h2>
<p>In <code>.env</code>:</p>
<pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lite
DB_USERNAME=root
DB_PASSWORD=secret</code></pre>
<p>Then run <code>php lite migrate</code> again.</p>

<div class="note">In production set <code>APP_DEBUG=false</code> so error details stay hidden and Blade caches compiled templates.</div>
HTML,
    ],

    'tutorial.html' => [
        'title' => 'Tutorial',
        'prev' => ['install.html', 'Install'],
        'next' => ['architecture.html', 'Architecture'],
        'content' => <<<'HTML'
<p class="eyebrow">Learning path</p>
<h1>Tutorial: a CRUD resource</h1>
<p class="lede">Reuse the posts pattern for your own resource. This walkthrough builds an <code>Article</code> entity.</p>

<h2>Step 1 — Model</h2>
<pre><code>php lite make:model Article</code></pre>
<p>Open <code>app/Models/Article.php</code> and set the fillable fields:</p>
<pre><code>namespace App\Models;

final class Article extends Model
{
    protected $fillable = ['title', 'body'];
}</code></pre>

<h2>Step 2 — Migration</h2>
<p>Create a file such as <code>database/migrations/2024_01_02_000001_create_articles_table.php</code>:</p>
<pre><code>return new class {
    public function up(Capsule $db): void
    {
        $db-&gt;schema()-&gt;create('articles', function (Blueprint $table): void {
            $table-&gt;id();
            $table-&gt;string('title');
            $table-&gt;text('body');
            $table-&gt;timestamps();
        });
    }

    public function down(Capsule $db): void
    {
        $db-&gt;schema()-&gt;dropIfExists('articles');
    }
};</code></pre>
<pre><code>php lite migrate</code></pre>

<h2>Step 3 — Controller</h2>
<pre><code>php lite make:controller ArticleController</code></pre>
<p>List and store can look like this:</p>
<pre><code>public function index(): Response
{
    return $this-&gt;view('articles.index', [
        'title' =&gt; 'Articles',
        'articles' =&gt; Article::query()-&gt;latest()-&gt;get(),
    ]);
}

public function store(Request $request): Response
{
    $data = $this-&gt;validate($request, [
        'title' =&gt; 'required|min:3|max:180',
        'body' =&gt; 'required|min:20',
    ]);

    $article = Article::query()-&gt;create($data);

    return $this-&gt;redirect('/articles/' . $article-&gt;id);
}</code></pre>

<h2>Step 4 — Routes</h2>
<p>In <code>routes/web.php</code>:</p>
<pre><code>$router-&gt;get('/articles', [ArticleController::class, 'index']);
$router-&gt;get('/articles/create', [ArticleController::class, 'create']);
$router-&gt;post('/articles', [ArticleController::class, 'store']);
$router-&gt;get('/articles/{id}', [ArticleController::class, 'show']);</code></pre>

<h2>Step 5 — Blade template</h2>
<p>Create <code>resources/views/articles/index.blade.php</code>:</p>
<pre><code>@extends('layouts.app')

@section('content')
&lt;h1&gt;Articles&lt;/h1&gt;
@foreach($articles as $article)
    &lt;a href="{{ url('/articles/' . $article-&gt;id) }}"&gt;{{ $article-&gt;title }}&lt;/a&gt;
@endforeach
@endsection</code></pre>

<div class="note">Always include <code>@csrf</code> on POST forms, or the CSRF middleware will reject the request with 403.</div>

<h2>Step 6 — Try it</h2>
<pre><code>php lite routes
php lite serve</code></pre>
<p><code>/articles</code> should list records. If you get a 404, check route order: register <code>/articles/create</code> before <code>/articles/{id}</code>.</p>
HTML,
    ],

    'architecture.html' => [
        'title' => 'Architecture',
        'prev' => ['tutorial.html', 'Tutorial'],
        'next' => ['routing.html', 'Routing'],
        'content' => <<<'HTML'
<p class="eyebrow">Core</p>
<h1>Architecture</h1>
<p class="lede">Application code lives in <code>app/</code>. The framework core lives in <code>src/</code>. You can write controllers and models without touching the Kernel.</p>

<h2>Directory layout</h2>
<pre><code>app/                 Controllers, Models
bootstrap/app.php    Builds the Application and registers routes
config/              app, database, view
database/migrations  Eloquent migrations
public/              Document root
resources/views      Blade templates
routes/web.php       Web routes
src/                 Lite core
storage/             cache, logs, sessions, SQLite</code></pre>

<h2>Design rules</h2>
<ul>
    <li><strong>SRP</strong> — the Router only matches routes, ViewFactory only renders templates, Migrator only runs migrations.</li>
    <li><strong>DIP</strong> — middleware depends on <code>MiddlewareInterface</code>, not a concrete class.</li>
    <li><strong>Thin controllers</strong> — HTTP and validation stay here; data logic stays on Eloquent models.</li>
</ul>

<h2>Container</h2>
<p><code>Lite\Container\Container</code> implements PSR-11 and builds constructor dependencies with Reflection. A controller can type-hint <code>__construct(Session $session)</code>; the Kernel <code>make</code>s it.</p>
<pre><code>$app-&gt;make(PostController::class);</code></pre>

<h2>Helpers</h2>
<p>These are always available from <code>src/Support/helpers.php</code>:</p>
<ul>
    <li><code>app()</code> / <code>config()</code> / <code>env()</code></li>
    <li><code>view()</code> / <code>json()</code> / <code>redirect()</code></li>
    <li><code>url()</code> / <code>asset()</code></li>
    <li><code>csrf_token()</code> / <code>csrf_field()</code> / <code>old()</code> / <code>error()</code></li>
</ul>
HTML,
    ],

    'routing.html' => [
        'title' => 'Routing',
        'prev' => ['architecture.html', 'Architecture'],
        'next' => ['controllers.html', 'Controllers'],
        'content' => <<<'HTML'
<p class="eyebrow">Core</p>
<h1>Routing</h1>
<p>Routes are registered in <code>routes/web.php</code>. Matching is handled by <strong>FastRoute</strong>.</p>

<h2>Registering routes</h2>
<pre><code>use App\Controllers\PostController;
use Lite\Routing\Router;

return static function (Router $router): void {
    $router-&gt;get('/', [HomeController::class, 'index'])-&gt;name('home');
    $router-&gt;post('/posts', [PostController::class, 'store']);
    $router-&gt;get('/posts/{id}', [PostController::class, 'show']);
};</code></pre>

<h2>Methods</h2>
<ul>
    <li><code>get</code> <code>post</code> <code>put</code> <code>patch</code> <code>delete</code> <code>any</code></li>
    <li>Path parameters: <code>{id}</code> is injected into the matching controller argument</li>
    <li>Named routes: <code>-&gt;name('posts.show')</code></li>
</ul>

<h2>Groups</h2>
<pre><code>$router-&gt;group(['prefix' =&gt; 'admin', 'middleware' =&gt; AuthMiddleware::class], function (Router $router): void {
    $router-&gt;get('/posts', [PostController::class, 'index']);
});</code></pre>

<h2>Route order</h2>
<p>Register static paths before parameterized ones. Otherwise <code>/posts/create</code> can match <code>/posts/{id}</code> and treat <code>create</code> as an id.</p>

<h2>Closures</h2>
<pre><code>$router-&gt;get('/health', function () {
    return json(['ok' =&gt; true]);
});</code></pre>
HTML,
    ],

    'controllers.html' => [
        'title' => 'Controllers &amp; HTTP',
        'prev' => ['routing.html', 'Routing'],
        'next' => ['middleware.html', 'Middleware'],
        'content' => <<<'HTML'
<p class="eyebrow">Core</p>
<h1>Controllers and HTTP</h1>
<p>Controllers extend <code>Lite\Http\Controller</code> and return a <code>Response</code>.</p>

<h2>Responses</h2>
<pre><code>$this-&gt;view('posts.show', ['post' =&gt; $post]);
$this-&gt;json(['id' =&gt; $post-&gt;id]);
$this-&gt;redirect('/posts');</code></pre>

<h2>Request</h2>
<p>If a controller method type-hints <code>Request $request</code>, the Kernel injects it.</p>
<ul>
    <li><code>method()</code> <code>path()</code> <code>url()</code></li>
    <li><code>input('title')</code> <code>all()</code> <code>only(['title', 'body'])</code></li>
    <li><code>header('Accept')</code> <code>wantsJson()</code> <code>isJson()</code></li>
</ul>
<p>JSON bodies are available through <code>input()</code> when <code>Content-Type</code> is <code>application/json</code>.</p>

<h2>HTTP errors</h2>
<pre><code>use Lite\Exceptions\HttpException;

throw HttpException::notFound('Post not found.');
throw HttpException::forbidden('CSRF token mismatch.');</code></pre>
<p>JSON requests get a JSON error payload. Otherwise the <code>errors.404</code> or <code>errors.500</code> template is rendered.</p>
HTML,
    ],

    'middleware.html' => [
        'title' => 'Middleware',
        'prev' => ['controllers.html', 'Controllers'],
        'next' => ['models.html', 'Models'],
        'content' => <<<'HTML'
<p class="eyebrow">Core</p>
<h1>Middleware</h1>
<p>Every request runs through a pipeline. Global middleware is listed in <code>config/app.php</code>:</p>
<pre><code>'middleware' =&gt; [
    Lite\Middleware\StartSession::class,
    Lite\Middleware\VerifyCsrfToken::class,
],</code></pre>

<h2>Writing middleware</h2>
<pre><code>namespace App\Middleware;

use Closure;
use Lite\Http\Request;
use Lite\Http\Response;
use Lite\Middleware\MiddlewareInterface;

final class RequireJson implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request-&gt;wantsJson()) {
            return Response::json(['message' =&gt; 'JSON only'], 406);
        }

        return $next($request);
    }
}</code></pre>
<p>Attach it to a route group:</p>
<pre><code>$router-&gt;group(['middleware' =&gt; RequireJson::class], function (Router $router): void {
    $router-&gt;get('/api/posts', [PostController::class, 'index']);
});</code></pre>

<h2>Session and CSRF</h2>
<ul>
    <li><code>StartSession</code> opens the session and advances flash data by one request.</li>
    <li><code>VerifyCsrfToken</code> skips GET, HEAD, and OPTIONS. POST requests need a <code>_token</code> field or an <code>X-CSRF-TOKEN</code> header.</li>
</ul>
HTML,
    ],

    'models.html' => [
        'title' => 'Models &amp; database',
        'prev' => ['middleware.html', 'Middleware'],
        'next' => ['views.html', 'Blade'],
        'content' => <<<'HTML'
<p class="eyebrow">Layers</p>
<h1>Models and database</h1>
<p>The data layer is <strong>Illuminate Database</strong> — Laravel’s Eloquent, without the rest of the framework.</p>

<h2>Models</h2>
<pre><code>namespace App\Models;

final class Post extends Model
{
    protected $fillable = ['title', 'excerpt', 'body'];
}</code></pre>
<pre><code>Post::query()-&gt;latest()-&gt;get();
Post::query()-&gt;find($id);
Post::query()-&gt;create($data);
$post-&gt;update($data);
$post-&gt;delete();</code></pre>

<h2>Migrations</h2>
<p>Each file in <code>database/migrations</code> must return an object with <code>up</code> and <code>down</code>.</p>
<pre><code>public function up(Capsule $db): void
{
    $db-&gt;schema()-&gt;create('posts', function (Blueprint $table): void {
        $table-&gt;id();
        $table-&gt;string('title');
        $table-&gt;timestamps();
    });
}</code></pre>
<pre><code>php lite migrate
php lite migrate:rollback
php lite migrate:fresh</code></pre>

<h2>Connections</h2>
<p>Settings live in <code>config/database.php</code> and <code>DB_CONNECTION</code> in <code>.env</code>. SQLite, MySQL, and Postgres are ready to use.</p>
HTML,
    ],

    'views.html' => [
        'title' => 'Blade &amp; views',
        'prev' => ['models.html', 'Models'],
        'next' => ['validation.html', 'Validation'],
        'content' => <<<'HTML'
<p class="eyebrow">Layers</p>
<h1>Blade and views</h1>
<p>The template engine is <code>illuminate/view</code>. View names are dotted: <code>posts.show</code> maps to <code>resources/views/posts/show.blade.php</code>.</p>

<h2>Layouts and sections</h2>
<pre><code>@extends('layouts.app')

@section('content')
    &lt;h1&gt;{{ $post-&gt;title }}&lt;/h1&gt;
    {!! nl2br(e($post-&gt;body)) !!}
@endsection</code></pre>

<h2>Security</h2>
<ul>
    <li><code>{{ $value }}</code> is always escaped.</li>
    <li><code>{!! $html !!}</code> is raw — use it only for HTML you generated yourself.</li>
    <li>Put <code>@csrf</code> on forms.</li>
</ul>

<h2>Shared data</h2>
<p>Every template receives <code>$app_name</code>, <code>$app_debug</code>, <code>$csrf_token</code>, <code>$flash</code>, and <code>$errors</code>.</p>

<h2>Helpers in templates</h2>
<pre><code>{{ url('/posts') }}
{{ asset('css/app.css') }}
{{ old('title', $post-&gt;title ?? '') }}
{{ error('title') }}
@csrf</code></pre>

<div class="note">Compiled templates are stored in <code>storage/cache/blade</code>.</div>
HTML,
    ],

    'validation.html' => [
        'title' => 'Validation',
        'prev' => ['views.html', 'Blade'],
        'next' => ['cli.html', 'CLI'],
        'content' => <<<'HTML'
<p class="eyebrow">Layers</p>
<h1>Validation</h1>
<p>From a controller:</p>
<pre><code>$data = $this-&gt;validate($request, [
    'title' =&gt; 'required|min:3|max:180',
    'email' =&gt; 'nullable|email',
    'body' =&gt; 'required|min:20',
]);</code></pre>

<p>Invalid data throws <code>ValidationException</code>:</p>
<ul>
    <li>JSON request → 422 response with an <code>errors</code> key</li>
    <li>HTML form → redirect back to the Referer with flashed <code>errors</code> and <code>old</code> input</li>
</ul>

<h2>Rules</h2>
<ul>
    <li><code>required</code></li>
    <li><code>nullable</code></li>
    <li><code>email</code></li>
    <li><code>min:n</code> / <code>max:n</code> — length for strings, value for numbers</li>
    <li><code>integer</code> / <code>numeric</code></li>
</ul>

<h2>Showing errors in Blade</h2>
<pre><code>&lt;input name="title" value="{{ old('title') }}"&gt;
@if(error('title'))
    &lt;small&gt;{{ error('title') }}&lt;/small&gt;
@endif</code></pre>
HTML,
    ],

    'cli.html' => [
        'title' => 'CLI',
        'prev' => ['validation.html', 'Validation'],
        'content' => <<<'HTML'
<p class="eyebrow">Layers</p>
<h1>Command line</h1>
<p>The CLI entry point is the <code>lite</code> file in the project root.</p>
<pre><code>php lite help</code></pre>

<h2>Commands</h2>
<pre><code>php lite serve [--host=127.0.0.1] [--port=8000]
php lite key:generate
php lite migrate
php lite migrate:rollback
php lite migrate:fresh
php lite make:controller PostController
php lite make:model Post
php lite routes</code></pre>

<p><code>migrate:fresh</code> drops every table and re-runs migrations. Use it in development, not in production.</p>
HTML,
    ],

    '404.html' => [
        'title' => 'Page not found',
        'content' => <<<'HTML'
<section class="hero">
    <p class="eyebrow">404</p>
    <h1 class="hero-title">This page is not in the docs.</h1>
    <p class="lede">Pick a section from the sidebar, or go back to the introduction.</p>
    <p><a href="index.html">Back to introduction</a></p>
</section>
HTML,
    ],
];
