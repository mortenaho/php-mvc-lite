<?php

declare(strict_types=1);

use Illuminate\Support\HtmlString;
use Lite\Application;
use Lite\Http\Response;
use Lite\Session\Session;
use Lite\Support\Config;

if (! function_exists('app')) {
    function app(?string $abstract = null): mixed
    {
        $application = Application::getInstance();

        return $abstract === null ? $application : $application->make($abstract);
    }
}

if (! function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return app()->basePath($path);
    }
}

if (! function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return base_path('config' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}

if (! function_exists('resource_path')) {
    function resource_path(string $path = ''): string
    {
        return base_path('resources' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}

if (! function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}

if (! function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        return base_path('public' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}

if (! function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        return match (strtolower((string) $value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            'empty', '(empty)' => '',
            default => $value,
        };
    }
}

if (! function_exists('config')) {
    function config(?string $key = null, mixed $default = null): mixed
    {
        /** @var Config $config */
        $config = app(Config::class);

        if ($key === null) {
            return $config;
        }

        return $config->get($key, $default);
    }
}

if (! function_exists('view')) {
    function view(string $template, array $data = [], int $status = 200): Response
    {
        return Response::view($template, $data, $status);
    }
}

if (! function_exists('json')) {
    function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }
}

if (! function_exists('redirect')) {
    function redirect(string $to, int $status = 302): Response
    {
        return Response::redirect($to, $status);
    }
}

if (! function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim((string) config('app.url', ''), '/');
        $path = ltrim($path, '/');

        return $path === '' ? $base : $base . '/' . $path;
    }
}

if (! function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (! function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (! function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return app(Session::class)->token();
    }
}

if (! function_exists('csrf_field')) {
    function csrf_field(): HtmlString
    {
        return new HtmlString('<input type="hidden" name="_token" value="' . e(csrf_token()) . '">');
    }
}

if (! function_exists('method_field')) {
    function method_field(string $method): HtmlString
    {
        return new HtmlString('<input type="hidden" name="_method" value="' . e(strtoupper($method)) . '">');
    }
}

if (! function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        $old = app(Session::class)->getFlash('old', []);

        return is_array($old) ? ($old[$key] ?? $default) : $default;
    }
}

if (! function_exists('error')) {
    function error(string $key): ?string
    {
        $errors = app(Session::class)->getFlash('errors', []);

        if (! is_array($errors) || ! isset($errors[$key])) {
            return null;
        }

        $messages = $errors[$key];

        return is_array($messages) ? (string) ($messages[0] ?? '') : (string) $messages;
    }
}
