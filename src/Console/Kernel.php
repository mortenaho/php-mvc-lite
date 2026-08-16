<?php

declare(strict_types=1);

namespace Lite\Console;

use Lite\Application;
use Lite\Database\Migrator;
use Lite\Routing\Router;

final class Kernel
{
    public function __construct(private readonly Application $app)
    {
    }

    /**
     * @param list<string> $argv
     */
    public function run(array $argv): int
    {
        $command = $argv[1] ?? 'help';
        $arguments = array_slice($argv, 2);

        return match ($command) {
            'serve' => $this->serve($arguments),
            'key:generate' => $this->generateKey(),
            'migrate' => $this->migrate(),
            'migrate:rollback' => $this->rollback(),
            'migrate:fresh' => $this->fresh(),
            'make:controller' => $this->makeController($arguments[0] ?? null),
            'make:model' => $this->makeModel($arguments[0] ?? null),
            'routes' => $this->routes(),
            'help', '--help', '-h' => $this->help(),
            default => $this->unknown($command),
        };
    }

    /**
     * @param list<string> $arguments
     */
    private function serve(array $arguments): int
    {
        $host = '127.0.0.1';
        $port = '8000';

        foreach ($arguments as $argument) {
            if (str_starts_with($argument, '--host=')) {
                $host = substr($argument, 7);
            }

            if (str_starts_with($argument, '--port=')) {
                $port = substr($argument, 7);
            }
        }

        $public = $this->app->basePath('public');
        $router = $public . DIRECTORY_SEPARATOR . 'router.php';

        fwrite(STDOUT, "Lite MVC development server started: http://{$host}:{$port}\n");

        passthru(sprintf(
            'php -S %s -t %s %s',
            escapeshellarg($host . ':' . $port),
            escapeshellarg($public),
            escapeshellarg($router),
        ), $code);

        return (int) $code;
    }

    private function generateKey(): int
    {
        $envPath = $this->app->basePath('.env');

        if (! is_file($envPath)) {
            $example = $this->app->basePath('.env.example');

            if (! is_file($example)) {
                fwrite(STDERR, ".env.example not found.\n");
                return 1;
            }

            copy($example, $envPath);
        }

        $env = file_get_contents($envPath);

        if ($env === false) {
            fwrite(STDERR, "Unable to read .env.\n");
            return 1;
        }

        $key = 'base64:' . base64_encode(random_bytes(32));

        if (preg_match('/^APP_KEY=.*$/m', $env) === 1) {
            $env = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $env, 1) ?? $env;
        } else {
            $env .= (str_ends_with($env, "\n") ? '' : "\n") . 'APP_KEY=' . $key . "\n";
        }

        if (file_put_contents($envPath, $env) === false) {
            fwrite(STDERR, "Unable to write APP_KEY to .env.\n");
            return 1;
        }

        fwrite(STDOUT, "Application key set successfully.\n");

        return 0;
    }

    private function migrate(): int
    {
        $ran = $this->migrator()->migrate();

        if ($ran === []) {
            fwrite(STDOUT, "Nothing to migrate.\n");
            return 0;
        }

        foreach ($ran as $name) {
            fwrite(STDOUT, "Migrated: {$name}\n");
        }

        return 0;
    }

    private function rollback(): int
    {
        $rolled = $this->migrator()->rollback();

        if ($rolled === []) {
            fwrite(STDOUT, "Nothing to rollback.\n");
            return 0;
        }

        foreach ($rolled as $name) {
            fwrite(STDOUT, "Rolled back: {$name}\n");
        }

        return 0;
    }

    private function fresh(): int
    {
        fwrite(STDOUT, "Dropped all tables.\n");
        $ran = $this->migrator()->fresh();

        foreach ($ran as $name) {
            fwrite(STDOUT, "Migrated: {$name}\n");
        }

        return 0;
    }

    private function makeController(?string $name): int
    {
        if ($name === null || $name === '') {
            fwrite(STDERR, "Usage: php lite make:controller PostController\n");
            return 1;
        }

        $name = str_ends_with($name, 'Controller') ? $name : $name . 'Controller';
        $path = $this->app->basePath('app/Controllers/' . $name . '.php');

        if (is_file($path)) {
            fwrite(STDERR, "Controller already exists: {$name}\n");
            return 1;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\\Controllers;

use Lite\\Http\\Controller;
use Lite\\Http\\Request;
use Lite\\Http\\Response;

final class {$name} extends Controller
{
    public function index(Request \$request): Response
    {
        return \$this->view('home.index', [
            'title' => '{$name}',
        ]);
    }
}

PHP;

        file_put_contents($path, $stub);
        fwrite(STDOUT, "Created app/Controllers/{$name}.php\n");

        return 0;
    }

    private function makeModel(?string $name): int
    {
        if ($name === null || $name === '') {
            fwrite(STDERR, "Usage: php lite make:model Post\n");
            return 1;
        }

        $class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
        $path = $this->app->basePath('app/Models/' . $class . '.php');

        if (is_file($path)) {
            fwrite(STDERR, "Model already exists: {$class}\n");
            return 1;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\\Models;

final class {$class} extends Model
{
    protected \$fillable = [];
}

PHP;

        file_put_contents($path, $stub);
        fwrite(STDOUT, "Created app/Models/{$class}.php\n");

        return 0;
    }

    private function routes(): int
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        fwrite(STDOUT, str_pad('METHOD', 12) . str_pad('URI', 32) . "ACTION\n");
        fwrite(STDOUT, str_repeat('-', 72) . "\n");

        foreach ($router->routes() as $route) {
            $methods = implode('|', $route->methods);
            $action = $route->action instanceof \Closure
                ? 'Closure'
                : (is_array($route->action) ? $route->action[0] . '@' . $route->action[1] : (string) $route->action);

            fwrite(STDOUT, str_pad($methods, 12) . str_pad($route->uri, 32) . $action . "\n");
        }

        return 0;
    }

    private function help(): int
    {
        fwrite(STDOUT, <<<TXT
Lite MVC CLI

Usage:
  php lite <command>

Commands:
  serve [--host=127.0.0.1] [--port=8000]
  key:generate
  migrate
  migrate:rollback
  migrate:fresh
  make:controller Name
  make:model Name
  routes
  help

TXT);

        return 0;
    }

    private function unknown(string $command): int
    {
        fwrite(STDERR, "Unknown command [{$command}]. Run `php lite help`.\n");

        return 1;
    }

    private function migrator(): Migrator
    {
        return new Migrator(
            $this->app->basePath('database/migrations'),
            $this->app->make(\Lite\Database\DatabaseManager::class)->capsule(),
        );
    }
}
