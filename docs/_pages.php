<?php

declare(strict_types=1);

return [
    'index.html' => [
        'title' => 'معرفی',
        'next' => ['install.html', 'نصب'],
        'content' => <<<'HTML'
<section class="hero">
    <p class="eyebrow">PHP 8.2 · MVC · OOP</p>
    <h1 class="hero-title">Lite MVC</h1>
    <p class="lede">فریم‌ورک MVC سبک برای PHP با ساختار درست، تزریق وابستگی، Eloquent و Blade. هسته کوچک است؛ کار سنگین داده و قالب به کتابخانه‌های بالغ سپرده شده.</p>
    <div class="pills">
        <span>Eloquent</span>
        <span>Blade</span>
        <span>FastRoute</span>
        <span>PSR-11</span>
    </div>
</section>

<div class="grid-3">
    <article class="card">
        <h3>هسته SOLID</h3>
        <p>Container با autowiring، Kernel، میدل‌ویر و جداسازی HTTP از دامنه.</p>
    </article>
    <article class="card">
        <h3>Eloquent</h3>
        <p>همان ORM لاراول برای مدل، مهاجرت، Query Builder و رابطه.</p>
    </article>
    <article class="card">
        <h3>Blade</h3>
        <p>وراثت لایه، @csrf، escape پیش‌فرض و کش قالب کامپایل‌شده.</p>
    </article>
</div>

<h2>این مستندات چیست؟</h2>
<p>این سایت مرجع فریم‌ورک و یک مسیر آموزشی است. اگر تازه‌واردید از <a href="install.html">نصب</a> شروع کنید و بعد <a href="tutorial.html">آموزش عملی</a> را بروید. اگر ساختار را می‌خواهید، <a href="architecture.html">معماری</a> را بخوانید.</p>

<h2>جریان یک درخواست</h2>
<pre><code>public/index.php
    → Application::handle()
    → Kernel (میدل‌ویر سراسری)
    → Router
    → Controller
    → Blade View یا JSON
    → Response</code></pre>

<h2>چه چیزی داخل هسته نیست؟</h2>
<ul>
    <li>احراز هویت آماده — خودتان با سشن و مدل User می‌سازید</li>
    <li>صف‌کار و Job — عمداً حذف شده تا سبک بماند</li>
    <li>ORM دست‌نویس — به‌جایش Eloquent آمده</li>
</ul>
HTML,
    ],

    'install.html' => [
        'title' => 'نصب و راه‌اندازی',
        'prev' => ['index.html', 'معرفی'],
        'next' => ['tutorial.html', 'آموزش عملی'],
        'content' => <<<'HTML'
<p class="eyebrow">شروع</p>
<h1>نصب و راه‌اندازی</h1>
<p class="lede">پیش‌نیاز: PHP 8.2 به‌بعد با PDO SQLite یا MySQL، و Composer.</p>

<h2>۱. کلون و وابستگی‌ها</h2>
<pre><code>git clone git@github.com:mortenaho/php-mvc-lite.git
cd php-mvc-lite
composer install
cp .env.example .env</code></pre>

<h2>۲. مهاجرت</h2>
<p>پیش‌فرض SQLite است و فایل دیتابیس در <code>storage/database/app.sqlite</code> ساخته می‌شود.</p>
<pre><code>php lite migrate</code></pre>

<h2>۳. سرور توسعه</h2>
<pre><code>php lite serve</code></pre>
<p>مرورگر را روی <a href="http://127.0.0.1:8000">http://127.0.0.1:8000</a> باز کنید. پورت را می‌توانید عوض کنید:</p>
<pre><code>php lite serve --port=8080</code></pre>

<h2>Apache</h2>
<p>Document Root را روی پوشه <code>public</code> بگذارید. فایل <code>public/.htaccess</code> همه مسیرها را به Front Controller می‌فرستد.</p>

<h2>MySQL</h2>
<p>در <code>.env</code>:</p>
<pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lite
DB_USERNAME=root
DB_PASSWORD=secret</code></pre>
<p>سپس دوباره <code>php lite migrate</code>.</p>

<div class="note">در production مقدار <code>APP_DEBUG=false</code> بگذارید تا جزئیات خطا نشان داده نشود و Blade قالب را کش کند.</div>
HTML,
    ],

    'tutorial.html' => [
        'title' => 'آموزش عملی',
        'prev' => ['install.html', 'نصب'],
        'next' => ['architecture.html', 'معماری'],
        'content' => <<<'HTML'
<p class="eyebrow">مسیر یادگیری</p>
<h1>آموزش عملی: یک منبع CRUD</h1>
<p class="lede">همین الگوی نوشته‌ها را برای منبع خودتان تکرار کنید. اینجا یک موجودیت <code>Article</code> می‌سازیم.</p>

<h2>گام ۱ — مدل</h2>
<pre><code>php lite make:model Article</code></pre>
<p>فایل <code>app/Models/Article.php</code> را باز کنید و فیلدهای قابل‌نوشتن را مشخص کنید:</p>
<pre><code>namespace App\Models;

final class Article extends Model
{
    protected $fillable = ['title', 'body'];
}</code></pre>

<h2>گام ۲ — مهاجرت</h2>
<p>فایلی شبیه <code>database/migrations/2024_01_02_000001_create_articles_table.php</code> بسازید:</p>
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

<h2>گام ۳ — کنترلر</h2>
<pre><code>php lite make:controller ArticleController</code></pre>
<p>لیست و ذخیره را این‌طور بنویسید:</p>
<pre><code>public function index(): Response
{
    return $this-&gt;view('articles.index', [
        'title' =&gt; 'مقاله‌ها',
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

<h2>گام ۴ — مسیر</h2>
<p>در <code>routes/web.php</code>:</p>
<pre><code>$router-&gt;get('/articles', [ArticleController::class, 'index']);
$router-&gt;get('/articles/create', [ArticleController::class, 'create']);
$router-&gt;post('/articles', [ArticleController::class, 'store']);
$router-&gt;get('/articles/{id}', [ArticleController::class, 'show']);</code></pre>

<h2>گام ۵ — قالب Blade</h2>
<p>فایل <code>resources/views/articles/index.blade.php</code>:</p>
<pre><code>@extends('layouts.app')

@section('content')
&lt;h1&gt;مقاله‌ها&lt;/h1&gt;
@foreach($articles as $article)
    &lt;a href="{{ url('/articles/' . $article-&gt;id) }}"&gt;{{ $article-&gt;title }}&lt;/a&gt;
@endforeach
@endsection</code></pre>

<div class="note">برای فرم POST حتماً <code>@csrf</code> بگذارید؛ وگرنه میدل‌ویر CSRF درخواست را با ۴۰۳ رد می‌کند.</div>

<h2>گام ۶ — تست</h2>
<pre><code>php lite routes
php lite serve</code></pre>
<p>مسیر <code>/articles</code> باید لیست را نشان بدهد. اگر ۴۰۴ دیدید، ترتیب مسیرها را چک کنید: <code>/articles/create</code> باید قبل از <code>/articles/{id}</code> ثبت شود.</p>
HTML,
    ],

    'architecture.html' => [
        'title' => 'معماری',
        'prev' => ['tutorial.html', 'آموزش عملی'],
        'next' => ['routing.html', 'مسیریابی'],
        'content' => <<<'HTML'
<p class="eyebrow">هسته</p>
<h1>معماری</h1>
<p class="lede">کد اپلیکیشن در <code>app/</code> است و هسته فریم‌ورک در <code>src/</code>. این جدایی یعنی می‌توانید کنترلر و مدل بنویسید بدون دست زدن به Kernel.</p>

<h2>ساختار پوشه‌ها</h2>
<pre><code>app/                 Controllers, Models
bootstrap/app.php    ساخت Application و ثبت مسیرها
config/              app, database, view
database/migrations  مهاجرت Eloquent
public/              Document root
resources/views      قالب‌های Blade
routes/web.php       مسیرهای وب
src/                 هسته Lite
storage/             کش، لاگ، سشن، SQLite</code></pre>

<h2>اصول</h2>
<ul>
    <li><strong>SRP</strong> — Router فقط مسیر می‌شناسد، ViewFactory فقط قالب، Migrator فقط مهاجرت.</li>
    <li><strong>DIP</strong> — میدل‌ویر به <code>MiddlewareInterface</code> وابسته است، نه به یک کلاس مشخص.</li>
    <li><strong>کنترلر نازک</strong> — HTTP و اعتبارسنجی اینجاست؛ منطق داده در مدل Eloquent می‌ماند.</li>
</ul>

<h2>Container</h2>
<p>کلاس <code>Lite\Container\Container</code> قرارداد PSR-11 را پیاده می‌کند و با Reflection وابستگی سازنده را می‌سازد. کنترلر با <code>__construct(Session $session)</code> سشن را می‌گیرد؛ Kernel آن را <code>make</code> می‌کند.</p>
<pre><code>$app-&gt;make(PostController::class);</code></pre>

<h2>کمک‌تابع‌ها</h2>
<p>از <code>src/Support/helpers.php</code> در همه جا در دسترس‌اند:</p>
<ul>
    <li><code>app()</code> / <code>config()</code> / <code>env()</code></li>
    <li><code>view()</code> / <code>json()</code> / <code>redirect()</code></li>
    <li><code>url()</code> / <code>asset()</code></li>
    <li><code>csrf_token()</code> / <code>csrf_field()</code> / <code>old()</code> / <code>error()</code></li>
</ul>
HTML,
    ],

    'routing.html' => [
        'title' => 'مسیریابی',
        'prev' => ['architecture.html', 'معماری'],
        'next' => ['controllers.html', 'کنترلر'],
        'content' => <<<'HTML'
<p class="eyebrow">هسته</p>
<h1>مسیریابی</h1>
<p>مسیرها در <code>routes/web.php</code> ثبت می‌شوند. موتور تطبیق <strong>FastRoute</strong> است.</p>

<h2>ثبت مسیر</h2>
<pre><code>use App\Controllers\PostController;
use Lite\Routing\Router;

return static function (Router $router): void {
    $router-&gt;get('/', [HomeController::class, 'index'])-&gt;name('home');
    $router-&gt;post('/posts', [PostController::class, 'store']);
    $router-&gt;get('/posts/{id}', [PostController::class, 'show']);
};</code></pre>

<h2>متدها</h2>
<ul>
    <li><code>get</code> <code>post</code> <code>put</code> <code>patch</code> <code>delete</code> <code>any</code></li>
    <li>پارامتر مسیر: <code>{id}</code> به آرگومان هم‌نام متد کنترلر تزریق می‌شود</li>
    <li>نام مسیر: <code>-&gt;name('posts.show')</code></li>
</ul>

<h2>گروه</h2>
<pre><code>$router-&gt;group(['prefix' =&gt; 'admin', 'middleware' =&gt; AuthMiddleware::class], function (Router $router): void {
    $router-&gt;get('/posts', [PostController::class, 'index']);
});</code></pre>

<h2>ترتیب مسیرها</h2>
<p>مسیر ثابت را قبل از پارامتردار بگذارید. وگرنه <code>/posts/create</code> ممکن است با <code>/posts/{id}</code> قاطی شود و <code>create</code> به‌عنوان id خوانده شود.</p>

<h2>Closure</h2>
<pre><code>$router-&gt;get('/health', function () {
    return json(['ok' =&gt; true]);
});</code></pre>
HTML,
    ],

    'controllers.html' => [
        'title' => 'کنترلر و HTTP',
        'prev' => ['routing.html', 'مسیریابی'],
        'next' => ['middleware.html', 'میدل‌ویر'],
        'content' => <<<'HTML'
<p class="eyebrow">هسته</p>
<h1>کنترلر و HTTP</h1>
<p>کنترلرها از <code>Lite\Http\Controller</code> ارث می‌برند و پاسخ <code>Response</code> برمی‌گردانند.</p>

<h2>پاسخ‌ها</h2>
<pre><code>$this-&gt;view('posts.show', ['post' =&gt; $post]);
$this-&gt;json(['id' =&gt; $post-&gt;id]);
$this-&gt;redirect('/posts');</code></pre>

<h2>Request</h2>
<p>اگر متد کنترلر <code>Request $request</code> بخواهد، Kernel آن را تزریق می‌کند.</p>
<ul>
    <li><code>method()</code> <code>path()</code> <code>url()</code></li>
    <li><code>input('title')</code> <code>all()</code> <code>only(['title', 'body'])</code></li>
    <li><code>header('Accept')</code> <code>wantsJson()</code> <code>isJson()</code></li>
</ul>
<p>بدنه JSON اگر <code>Content-Type: application/json</code> باشد با <code>input()</code> خوانده می‌شود.</p>

<h2>خطاهای HTTP</h2>
<pre><code>use Lite\Exceptions\HttpException;

throw HttpException::notFound('نوشته پیدا نشد.');
throw HttpException::forbidden('CSRF token mismatch.');</code></pre>
<p>اگر درخواست JSON بخواهد، پاسخ JSON است؛ وگرنه قالب <code>errors.404</code> یا <code>errors.500</code>.</p>
HTML,
    ],

    'middleware.html' => [
        'title' => 'میدل‌ویر',
        'prev' => ['controllers.html', 'کنترلر'],
        'next' => ['models.html', 'مدل'],
        'content' => <<<'HTML'
<p class="eyebrow">هسته</p>
<h1>میدل‌ویر</h1>
<p>هر درخواست از یک Pipeline رد می‌شود. میدل‌ویر سراسری در <code>config/app.php</code> است:</p>
<pre><code>'middleware' =&gt; [
    Lite\Middleware\StartSession::class,
    Lite\Middleware\VerifyCsrfToken::class,
],</code></pre>

<h2>نوشتن میدل‌ویر</h2>
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
<p>بعد آن را روی گروه مسیر بگذارید:</p>
<pre><code>$router-&gt;group(['middleware' =&gt; RequireJson::class], function (Router $router): void {
    $router-&gt;get('/api/posts', [PostController::class, 'index']);
});</code></pre>

<h2>سشن و CSRF</h2>
<ul>
    <li><code>StartSession</code> سشن را باز می‌کند و flash را یک درخواست جلو می‌برد.</li>
    <li><code>VerifyCsrfToken</code> روی GET/HEAD/OPTIONS کار نمی‌کند. برای POST باید فیلد <code>_token</code> یا هدر <code>X-CSRF-TOKEN</code> باشد.</li>
</ul>
HTML,
    ],

    'models.html' => [
        'title' => 'مدل و دیتابیس',
        'prev' => ['middleware.html', 'میدل‌ویر'],
        'next' => ['views.html', 'Blade'],
        'content' => <<<'HTML'
<p class="eyebrow">لایه‌ها</p>
<h1>مدل و دیتابیس</h1>
<p>لایه داده <strong>Illuminate Database</strong> است؛ همان Eloquent لاراول، بدون خود فریم‌ورک.</p>

<h2>مدل</h2>
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

<h2>مهاجرت</h2>
<p>هر فایل در <code>database/migrations</code> باید یک شیء با <code>up</code> و <code>down</code> برگرداند.</p>
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

<h2>اتصال</h2>
<p>تنظیمات در <code>config/database.php</code> و مقدار <code>DB_CONNECTION</code> در <code>.env</code> است. SQLite، MySQL و Postgres آماده‌اند.</p>
HTML,
    ],

    'views.html' => [
        'title' => 'Blade و ویو',
        'prev' => ['models.html', 'مدل'],
        'next' => ['validation.html', 'اعتبارسنجی'],
        'content' => <<<'HTML'
<p class="eyebrow">لایه‌ها</p>
<h1>Blade و ویو</h1>
<p>موتور قالب <code>illuminate/view</code> است. نام ویو نقطه‌ای است: <code>posts.show</code> یعنی <code>resources/views/posts/show.blade.php</code>.</p>

<h2>لایه و بخش</h2>
<pre><code>@extends('layouts.app')

@section('content')
    &lt;h1&gt;{{ $post-&gt;title }}&lt;/h1&gt;
    {!! nl2br(e($post-&gt;body)) !!}
@endsection</code></pre>

<h2>امنیت</h2>
<ul>
    <li><code>{{ $value }}</code> همیشه escape می‌شود.</li>
    <li><code>{!! $html !!}</code> خام است؛ فقط وقتی محتوا را خودتان ساخته‌اید استفاده کنید.</li>
    <li>در فرم‌ها <code>@csrf</code> بگذارید.</li>
</ul>

<h2>داده مشترک</h2>
<p>در همه قالب‌ها این‌ها موجودند: <code>$app_name</code>، <code>$app_debug</code>، <code>$csrf_token</code>، <code>$flash</code>، <code>$errors</code>.</p>

<h2>توابع کمکی داخل قالب</h2>
<pre><code>{{ url('/posts') }}
{{ asset('css/app.css') }}
{{ old('title', $post-&gt;title ?? '') }}
{{ error('title') }}
@csrf</code></pre>

<div class="note">قالب‌های کامپایل‌شده در <code>storage/cache/blade</code> ذخیره می‌شوند.</div>
HTML,
    ],

    'validation.html' => [
        'title' => 'اعتبارسنجی',
        'prev' => ['views.html', 'Blade'],
        'next' => ['cli.html', 'CLI'],
        'content' => <<<'HTML'
<p class="eyebrow">لایه‌ها</p>
<h1>اعتبارسنجی</h1>
<p>از کنترلر:</p>
<pre><code>$data = $this-&gt;validate($request, [
    'title' =&gt; 'required|min:3|max:180',
    'email' =&gt; 'nullable|email',
    'body' =&gt; 'required|min:20',
]);</code></pre>

<p>اگر داده نامعتبر باشد <code>ValidationException</code> پرتاب می‌شود:</p>
<ul>
    <li>درخواست JSON → پاسخ ۴۲۲ با کلید <code>errors</code></li>
    <li>فرم HTML → redirect به Referer با flash از <code>errors</code> و <code>old</code></li>
</ul>

<h2>قوانین</h2>
<ul>
    <li><code>required</code></li>
    <li><code>nullable</code></li>
    <li><code>email</code></li>
    <li><code>min:n</code> / <code>max:n</code> — برای رشته طول، برای عدد مقدار</li>
    <li><code>integer</code> / <code>numeric</code></li>
</ul>

<h2>نمایش خطا در Blade</h2>
<pre><code>&lt;input name="title" value="{{ old('title') }}"&gt;
@if(error('title'))
    &lt;small&gt;{{ error('title') }}&lt;/small&gt;
@endif</code></pre>
HTML,
    ],

    'cli.html' => [
        'title' => 'خط فرمان',
        'prev' => ['validation.html', 'اعتبارسنجی'],
        'content' => <<<'HTML'
<p class="eyebrow">لایه‌ها</p>
<h1>خط فرمان</h1>
<p>ورود CLI فایل <code>lite</code> در ریشه پروژه است.</p>
<pre><code>php lite help</code></pre>

<h2>دستورها</h2>
<pre><code>php lite serve [--host=127.0.0.1] [--port=8000]
php lite migrate
php lite migrate:rollback
php lite migrate:fresh
php lite make:controller PostController
php lite make:model Post
php lite routes</code></pre>

<p><code>migrate:fresh</code> همه جدول‌ها را حذف می‌کند و مهاجرت‌ها را از نو اجرا می‌کند. برای محیط توسعه است، نه production.</p>
HTML,
    ],

    '404.html' => [
        'title' => 'صفحه پیدا نشد',
        'content' => <<<'HTML'
<section class="hero">
    <p class="eyebrow">۴۰۴</p>
    <h1 class="hero-title">این صفحه در مستندات نیست.</h1>
    <p class="lede">از فهرست کنار صفحه یک بخش را انتخاب کنید یا به معرفی برگردید.</p>
    <p><a href="index.html">بازگشت به معرفی</a></p>
</section>
HTML,
    ],
];
