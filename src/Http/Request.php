<?php

declare(strict_types=1);

namespace Lite\Http;

final class Request
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $request
     * @param array<string, mixed> $cookies
     * @param array<string, mixed> $server
     * @param array<string, mixed> $files
     */
    public function __construct(
        private readonly array $query,
        private readonly array $request,
        private readonly array $cookies,
        private readonly array $server,
        private readonly array $files,
        private readonly string $rawBody = '',
    ) {
    }

    public static function capture(): self
    {
        return new self($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, file_get_contents('php://input') ?: '');
    }

    public function method(): string
    {
        $spoofed = $this->input('_method');

        if (is_string($spoofed) && $spoofed !== '') {
            return strtoupper($spoofed);
        }

        return strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));
    }

    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }

    public function path(): string
    {
        $uri = (string) ($this->server['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);
        $path = $path === false || $path === null || $path === '' ? '/' : rawurldecode($path);

        $script = str_replace('\\', '/', (string) ($this->server['SCRIPT_NAME'] ?? ''));
        $directory = str_replace('\\', '/', dirname($script));

        if ($directory !== '/' && $directory !== '.' && $directory !== '') {
            if (str_ends_with($directory, '/public')) {
                $directory = substr($directory, 0, -strlen('/public'));
            }

            $directory = rtrim($directory, '/');

            if ($directory !== '' && ($path === $directory || str_starts_with($path, $directory . '/'))) {
                $path = substr($path, strlen($directory)) ?: '/';
            }
        }

        // Hosts that keep the project root as docroot may expose /public/... URLs.
        if ($path === '/public' || str_starts_with($path, '/public/')) {
            $path = substr($path, strlen('/public')) ?: '/';
        }

        // PATH_INFO style: /index.php/health-check
        if (str_starts_with($path, '/index.php/') || $path === '/index.php') {
            $path = substr($path, strlen('/index.php')) ?: '/';
        }

        if (str_starts_with($path, '/public/index.php/') || $path === '/public/index.php') {
            $path = substr($path, strlen('/public/index.php')) ?: '/';
        }

        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }

    public function url(): string
    {
        $scheme = $this->isSecure() ? 'https' : 'http';
        $host = (string) ($this->server['HTTP_HOST'] ?? 'localhost');

        return $scheme . '://' . $host . $this->path();
    }

    public function fullUrl(): string
    {
        $query = (string) ($this->server['QUERY_STRING'] ?? '');

        return $this->url() . ($query !== '' ? '?' . $query : '');
    }

    public function isSecure(): bool
    {
        $https = $this->server['HTTPS'] ?? '';

        return $https !== '' && $https !== 'off';
    }

    public function wantsJson(): bool
    {
        $accept = strtolower($this->header('Accept', ''));
        $requestedWith = strtolower($this->header('X-Requested-With', ''));

        return str_contains($accept, 'application/json') || $requestedWith === 'xmlhttprequest';
    }

    public function isJson(): bool
    {
        return str_contains(strtolower($this->header('Content-Type', '')), 'application/json');
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public function header(string $key, ?string $default = null): ?string
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $key));

        if (isset($this->server[$normalized])) {
            return (string) $this->server[$normalized];
        }

        $special = [
            'CONTENT_TYPE' => 'CONTENT_TYPE',
            'CONTENT_LENGTH' => 'CONTENT_LENGTH',
        ];

        $lookup = $special[strtoupper(str_replace('-', '_', $key))] ?? null;

        return $lookup !== null && isset($this->server[$lookup])
            ? (string) $this->server[$lookup]
            : $default;
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? $this->query : ($this->query[$key] ?? $default);
    }

    public function input(?string $key = null, mixed $default = null): mixed
    {
        $bag = $this->all();

        if ($key === null) {
            return $bag;
        }

        return $bag[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return array_merge($this->query, $this->jsonPayload(), $this->request);
    }

    /**
     * @param list<string> $keys
     * @return array<string, mixed>
     */
    public function only(array $keys): array
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function files(): array
    {
        return $this->files;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;

        return is_array($file) ? $file : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function jsonPayload(): array
    {
        if (! $this->isJson() || $this->rawBody === '') {
            return [];
        }

        $decoded = json_decode($this->rawBody, true);

        return is_array($decoded) ? $decoded : [];
    }
}
