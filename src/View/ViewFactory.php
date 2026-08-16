<?php

declare(strict_types=1);

namespace Lite\View;

use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Lite\Container\Container;
use Lite\Session\Session;
use Lite\Support\Config;

final class ViewFactory
{
    public function __construct(
        private readonly Factory $factory,
        private readonly Session $session,
        private readonly Config $config,
    ) {
    }

    public static function create(Container $container): self
    {
        /** @var Config $config */
        $config = $container->get(Config::class);
        $cachePath = (string) $config->get('view.cache', storage_path('cache/blade'));

        if (! is_dir($cachePath)) {
            mkdir($cachePath, 0775, true);
        }

        $files = new Filesystem();
        $compiler = new BladeCompiler($files, $cachePath);

        $resolver = new EngineResolver();
        $resolver->register('blade', static fn (): CompilerEngine => new CompilerEngine($compiler, $files));
        $resolver->register('php', static fn (): PhpEngine => new PhpEngine($files));

        $finder = new FileViewFinder($files, [
            (string) $config->get('view.path', resource_path('views')),
        ]);

        $illuminate = new IlluminateContainer();
        $factory = new Factory($resolver, $finder, new Dispatcher($illuminate));
        $factory->setContainer($illuminate);

        return new self(
            $factory,
            $container->get(Session::class),
            $config,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $this->shareGlobals();

        return $this->factory->make($this->normalize($template), $data)->render();
    }

    public function exists(string $template): bool
    {
        return $this->factory->exists($this->normalize($template));
    }

    public function engine(): Factory
    {
        return $this->factory;
    }

    private function shareGlobals(): void
    {
        $flash = $this->session->allFlash();

        $this->factory->share([
            'app_name' => $this->config->get('app.name', 'Lite MVC'),
            'app_debug' => (bool) $this->config->get('app.debug', false),
            'csrf_token' => $this->session->token(),
            'flash' => $flash,
            'errors' => is_array($flash['errors'] ?? null) ? $flash['errors'] : [],
        ]);
    }

    private function normalize(string $template): string
    {
        $template = preg_replace('/\.(blade\.php|php|twig)$/', '', $template) ?? $template;

        return str_replace(['/', '\\'], '.', $template);
    }
}
