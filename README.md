# Lite MVC

فریم‌ورک MVC سبک برای PHP با ساختار لایه‌ای، تزریق وابستگی، Eloquent و Blade.

هسته عمداً کوچک است. کار سنگین داده و قالب به کتابخانه‌های بالغ سپرده شده تا هم سبک بماند، هم در تولید قابل اعتماد باشد.

## پشته

| لایه | انتخاب | دلیل |
| --- | --- | --- |
| HTTP / Routing | Front Controller + FastRoute | مسیرهای کامپایل‌شده و سریع |
| Container | PSR-11 با autowiring | کنترلر و میدل‌ویر با constructor injection |
| ORM | Illuminate Database (Eloquent) | مدل، مهاجرت، Query Builder، رابطه |
| View | Illuminate View (Blade) | همان سینتکس لاراول، کامپایل و کش قالب |
| Config | `.env` + فایل‌های PHP | جداسازی محیط از کد |

## ساختار

```
app/                 کد اپلیکیشن (Controllers, Models)
bootstrap/           ساخت Application و ثبت مسیرها
config/              تنظیمات app / database / view
database/migrations  مهاجرت‌های Eloquent
public/              Document root و Front Controller
resources/views      قالب‌های Blade (layouts, components, pages)
routes/web.php       تعریف مسیرها
src/                 هسته فریم‌ورک (Lite)
storage/             کش Blade، لاگ، سشن، SQLite
```

جریان درخواست:

`public/index.php` → `Kernel` → میدل‌ویر سراسری → Router → کنترلر → Response

## اصول طراحی

- **SRP**: هر کلاس یک مسئولیت (Router, Kernel, ViewFactory, Migrator)
- **DIP**: وابستگی به Container و اینترفیس میدل‌ویر، نه به پیاده‌سازی سفت
- **OCP**: افزودن میدل‌ویر، مسیر و سرویس بدون دستکاری هسته
- **Controller نازک**: اعتبارسنجی و پاسخ HTTP؛ منطق داده در Eloquent Model
- **View امن**: `{{ $value }}` در Blade به‌صورت پیش‌فرض HTML را escape می‌کند

## نصب

```bash
composer install
cp .env.example .env
php lite migrate
php lite serve
```

سپس [http://127.0.0.1:8000](http://127.0.0.1:8000) را باز کنید.

برای Apache، `public` را Document Root بگذارید. `.htaccess` مسیرها را به `index.php` می‌فرستد.

## CLI

```bash
php lite serve --port=8000
php lite migrate
php lite migrate:rollback
php lite migrate:fresh
php lite make:controller ArticleController
php lite make:model Article
php lite routes
```

## نمونه کنترلر

```php
final class PostController extends Controller
{
    public function store(Request $request): Response
    {
        $data = $this->validate($request, [
            'title' => 'required|min:3|max:180',
            'body' => 'required|min:20',
        ]);

        $post = Post::query()->create($data);

        return $this->redirect('/posts/' . $post->id);
    }
}
```

## قالب Blade

```blade
@extends('layouts.app')

@section('content')
  <h1>{{ $post->title }}</h1>
  <form method="post" action="{{ url('/posts') }}">
    @csrf
    <input name="title" value="{{ old('title') }}">
  </form>
@endsection
```

در production قالب‌های کامپایل‌شده در `storage/cache/blade` کش می‌شوند.

## دیتابیس

پیش‌فرض SQLite است (`storage/database/app.sqlite`). برای MySQL در `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=lite
DB_USERNAME=root
DB_PASSWORD=
```

سپس `php lite migrate`.
