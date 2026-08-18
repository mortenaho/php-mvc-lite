# Lite MVC

A lightweight PHP MVC framework with a layered structure, dependency injection, Eloquent, and Blade.

**Docs:** [mortenaho.github.io/php-mvc-lite](https://mortenaho.github.io/php-mvc-lite/)

The core is intentionally small. Heavy lifting for data and templates is left to mature libraries so the framework stays light and production-ready.

## Stack

| Layer | Choice | Why |
| --- | --- | --- |
| HTTP / Routing | Front Controller + FastRoute | Compiled, fast route matching |
| Container | PSR-11 with autowiring | Constructor injection for controllers and middleware |
| ORM | Illuminate Database (Eloquent) | Models, migrations, query builder, relationships |
| View | Illuminate View (Blade) | Laravel Blade syntax, compiled and cached |
| Config | `.env` + PHP files | Environment kept out of code |

## Layout

```
app/                 Application code (Controllers, Models)
bootstrap/           Builds the Application and registers routes
config/              app / database / view settings
database/migrations  Eloquent migrations
public/              Document root and front controller
resources/views      Blade templates (layouts, components, pages)
routes/web.php       Route definitions
src/                 Framework core (Lite)
storage/             Blade cache, logs, sessions, SQLite
```

Request flow:

`public/index.php` → `Kernel` → global middleware → Router → Controller → Response

## Design

- **SRP**: one responsibility per class (Router, Kernel, ViewFactory, Migrator)
- **DIP**: depend on the Container and middleware interface, not a hard-wired implementation
- **OCP**: add middleware, routes, and services without changing the core
- **Thin controllers**: HTTP and validation here; data logic on Eloquent models
- **Safe views**: `{{ $value }}` HTML-escapes by default in Blade

## Install

Create a new project with Composer:

```bash
composer create-project phpmvc/lite my-app
cd my-app
php lite migrate
php lite serve
```

Or clone the repository:

```bash
composer install
cp .env.example .env
php lite key:generate
php lite migrate
php lite serve
```

Then open [http://127.0.0.1:8000](http://127.0.0.1:8000).

Sign in with the seeded user `demo@lite.test` / `password`, or register a new account.

## Authentication

Session auth is included: `/login`, `/register`, `/logout`, `/account`.

Protect routes with `Lite\Middleware\Authenticate`. Guests hitting a protected URL are sent to login. Use `auth()`, `user()`, and `$user` in Blade.

Write operations on posts require login; only the author can edit or delete.

## Shared hosting

If you can change the document root, point it at `public`.

If the host locks the document root (for example `public_html`), upload the whole project there. The root `.htaccess` rewrites every request into `public` and blocks access to `.env`, `vendor`, and `src`. Set `APP_URL` in `.env` to the real site URL.

## CLI

```bash
php lite serve --port=8000
php lite key:generate
php lite migrate
php lite migrate:rollback
php lite migrate:fresh
php lite make:controller ArticleController
php lite make:model Article
php lite make:policy ArticlePolicy
php lite routes
```

## Controller example

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

## Blade template

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

In production, compiled templates are cached in `storage/cache/blade`.

## Database

SQLite is the default (`storage/database/app.sqlite`). For MySQL, set `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=lite
DB_USERNAME=root
DB_PASSWORD=
```

Then run `php lite migrate`.
