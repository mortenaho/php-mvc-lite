<?php

declare(strict_types=1);

namespace Lite\Routing;

use Closure;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Lite\Exceptions\HttpException;
use Lite\Http\Request;
use function FastRoute\simpleDispatcher;

final class Router
{
    /** @var list<Route> */
    private array $routes = [];

    /** @var list<string> */
    private array $groupMiddleware = [];

    private string $groupPrefix = '';

    public function get(string $uri, mixed $action): Route
    {
        return $this->add(['GET'], $uri, $action);
    }

    public function post(string $uri, mixed $action): Route
    {
        return $this->add(['POST'], $uri, $action);
    }

    public function put(string $uri, mixed $action): Route
    {
        return $this->add(['PUT'], $uri, $action);
    }

    public function patch(string $uri, mixed $action): Route
    {
        return $this->add(['PATCH'], $uri, $action);
    }

    public function delete(string $uri, mixed $action): Route
    {
        return $this->add(['DELETE'], $uri, $action);
    }

    public function any(string $uri, mixed $action): Route
    {
        return $this->add(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], $uri, $action);
    }

    /**
     * @param array{prefix?: string, middleware?: string|list<string>} $attributes
     */
    public function group(array $attributes, Closure $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->groupPrefix = $previousPrefix . '/' . trim((string) ($attributes['prefix'] ?? ''), '/');
        $middleware = $attributes['middleware'] ?? [];
        $this->groupMiddleware = array_merge(
            $previousMiddleware,
            is_array($middleware) ? $middleware : [$middleware],
        );

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    /**
     * @return array{route: Route, params: array<string, string>}
     */
    public function match(Request $request): array
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $collector): void {
            foreach ($this->routes as $index => $route) {
                $collector->addRoute($route->methods, $this->compileUri($route->uri), $index);
            }
        });

        $routeInfo = $dispatcher->dispatch($request->method(), $this->normalize($request->path()));

        return match ($routeInfo[0]) {
            Dispatcher::NOT_FOUND => throw HttpException::notFound('Route not found.'),
            Dispatcher::METHOD_NOT_ALLOWED => throw HttpException::methodNotAllowed(
                'Method ' . $request->method() . ' is not allowed.',
            ),
            Dispatcher::FOUND => [
                'route' => $this->routes[$routeInfo[1]],
                'params' => $routeInfo[2],
            ],
            default => throw HttpException::notFound('Route not found.'),
        };
    }

    public function route(string $name, array $params = []): string
    {
        $route = null;

        foreach ($this->routes as $candidate) {
            if ($candidate->name === $name) {
                $route = $candidate;
                break;
            }
        }

        $route ??= throw new \InvalidArgumentException("Named route [{$name}] not found.");
        $uri = $route->uri;

        foreach ($params as $key => $value) {
            $uri = preg_replace('#\{' . preg_quote((string) $key, '#') . '\??\}#', (string) $value, $uri) ?? $uri;
        }

        $uri = preg_replace('#\{[^}]+\?\}#', '', $uri) ?? $uri;

        return '/' . trim($uri, '/');
    }

    /**
     * @return list<Route>
     */
    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * @param list<string> $methods
     */
    private function add(array $methods, string $uri, mixed $action): Route
    {
        $uri = $this->normalize($this->groupPrefix . '/' . ltrim($uri, '/'));
        $route = new Route($methods, $uri, $action, $this->groupMiddleware);
        $this->routes[] = $route;

        return $route;
    }

    private function compileUri(string $uri): string
    {
        $compiled = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\?\}#', '{$1}', $uri) ?? $uri;

        return $compiled === '' ? '/' : $compiled;
    }

    private function normalize(string $uri): string
    {
        $uri = '/' . trim($uri, '/');

        return $uri === '/' ? '/' : rtrim($uri, '/');
    }
}
