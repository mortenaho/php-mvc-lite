<?php

declare(strict_types=1);

namespace Lite;

use Dotenv\Dotenv;
use Lite\Container\Container;
use Lite\Database\DatabaseManager;
use Lite\Exceptions\Handler;
use Lite\Http\Kernel;
use Lite\Http\Request;
use Lite\Http\Response;
use Lite\Middleware\StartSession;
use Lite\Middleware\VerifyCsrfToken;
use Lite\Routing\Router;
use Lite\Session\Session;
use Lite\Support\Config;
use Lite\View\ViewFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class Application
{
    private static ?self $instance = null;

    private bool $bootstrapped = false;

    public function __construct(
        private readonly string $basePath,
        private readonly Container $container,
    ) {
        self::$instance = $this;
        $this->container->instance(self::class, $this);
        $this->container->instance(Container::class, $this->container);
        $this->container->instance(ContainerInterface::class, $this->container);
    }

    public static function configure(string $basePath): self
    {
        return new self(rtrim($basePath, '/\\'), new Container());
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            throw new RuntimeException('Application has not been configured.');
        }

        return self::$instance;
    }

    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function make(string $abstract, array $parameters = []): mixed
    {
        return $this->container->make($abstract, $parameters);
    }

    public function bootstrap(): self
    {
        if ($this->bootstrapped) {
            return $this;
        }

        $this->loadEnvironment();
        $this->loadConfiguration();
        $this->registerCoreServices();
        $this->container->get(DatabaseManager::class)->boot();
        $this->bootstrapped = true;

        return $this;
    }

    public function handle(Request $request): Response
    {
        $this->bootstrap();

        return $this->container->get(Kernel::class)->handle($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $response->send();
    }

    private function loadEnvironment(): void
    {
        $envFile = $this->basePath('.env');

        if (is_file($envFile)) {
            Dotenv::createImmutable($this->basePath)->safeLoad();
        }
    }

    private function loadConfiguration(): void
    {
        $items = [];

        foreach (glob($this->basePath('config/*.php')) ?: [] as $file) {
            $items[basename($file, '.php')] = require $file;
        }

        $this->container->instance(Config::class, new Config($items));
    }

    private function registerCoreServices(): void
    {
        $this->container->singleton(Session::class, fn () => new Session());
        $this->container->singleton(Router::class, fn () => new Router());
        $this->container->singleton(ViewFactory::class, fn (Container $container) => ViewFactory::create($container));
        $this->container->singleton(DatabaseManager::class, fn (Container $container) => new DatabaseManager($container->get(Config::class)));
        $this->container->singleton(LoggerInterface::class, fn () => Handler::logger());
        $this->container->singleton(Handler::class, fn (Container $container) => new Handler(
            $container->get(Config::class),
            $container->get(ViewFactory::class),
        ));
        $this->container->singleton(Kernel::class, function (Container $container) {
            /** @var Config $config */
            $config = $container->get(Config::class);

            return new Kernel(
                $container,
                $container->get(Router::class),
                $container->get(Handler::class),
                $config->get('app.middleware', [
                    StartSession::class,
                    VerifyCsrfToken::class,
                ]),
            );
        });
    }
}
