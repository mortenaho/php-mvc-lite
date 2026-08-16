<?php

declare(strict_types=1);

namespace Lite\Database;

use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Pagination\Paginator;
use Lite\Support\Config;

final class DatabaseManager
{
    private ?Capsule $capsule = null;

    public function __construct(private readonly Config $config)
    {
    }

    public function boot(): Capsule
    {
        if ($this->capsule instanceof Capsule) {
            return $this->capsule;
        }

        $default = (string) $this->config->get('database.default', 'sqlite');
        $connections = $this->config->get('database.connections', []);
        $connection = $connections[$default] ?? null;

        if (! is_array($connection)) {
            throw new \RuntimeException("Database connection [{$default}] is not configured.");
        }

        if (($connection['driver'] ?? '') === 'sqlite') {
            $this->ensureSqliteFile((string) $connection['database']);
        }

        $capsule = new Capsule();
        $capsule->addConnection($connection);
        $capsule->setEventDispatcher(new Dispatcher(new IlluminateContainer()));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        Paginator::currentPageResolver(static function (): int {
            $page = $_GET['page'] ?? 1;

            return max(1, (int) $page);
        });

        Paginator::currentPathResolver(static function (): string {
            $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
            $path = parse_url($uri, PHP_URL_PATH);

            return is_string($path) && $path !== '' ? $path : '/';
        });

        $this->capsule = $capsule;

        return $capsule;
    }

    public function capsule(): Capsule
    {
        return $this->boot();
    }

    private function ensureSqliteFile(string $path): void
    {
        if ($path === ':memory:') {
            return;
        }

        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        if (! is_file($path)) {
            touch($path);
        }
    }
}
