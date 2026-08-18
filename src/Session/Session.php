<?php

declare(strict_types=1);

namespace Lite\Session;

final class Session
{
    private bool $started = false;

    public function start(): void
    {
        if ($this->started || session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;
            $this->ensureCsrfToken();

            return;
        }

        $lifetime = (int) env('SESSION_LIFETIME', 120);

        session_set_cookie_params([
            'lifetime' => $lifetime * 60,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_save_path(storage_path('sessions'));
        session_name('lite_session');
        session_start();
        $this->started = true;
        $this->ensureCsrfToken();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash_now'][$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function allFlash(): array
    {
        return $_SESSION['_flash_now'] ?? [];
    }

    public function ageFlash(): void
    {
        $_SESSION['_flash_now'] = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
    }

    public function regenerate(): void
    {
        $this->start();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $this->regenerateToken();
    }

    public function token(): string
    {
        $this->start();
        $this->ensureCsrfToken();

        return (string) $_SESSION['_token'];
    }

    public function regenerateToken(): void
    {
        $_SESSION['_token'] = bin2hex(random_bytes(32));
    }

    private function ensureCsrfToken(): void
    {
        if (! isset($_SESSION['_token']) || ! is_string($_SESSION['_token']) || $_SESSION['_token'] === '') {
            $this->regenerateToken();
        }
    }
}
