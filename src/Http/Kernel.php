<?php

declare(strict_types=1);

namespace Lite\Http;

use Closure;
use Lite\Container\Container;
use Lite\Exceptions\Handler;
use Lite\Middleware\Pipeline;
use Lite\Routing\Route;
use Lite\Routing\Router;
use ReflectionMethod;
use RuntimeException;

final class Kernel
{
    /**
     * @param list<class-string> $middleware
     */
    public function __construct(
        private readonly Container $container,
        private readonly Router $router,
        private readonly Handler $handler,
        private readonly array $middleware,
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->container->instance(Request::class, $request);

        try {
            return (new Pipeline($this->container))
                ->send($request)
                ->through($this->middleware)
                ->then(fn (Request $request): Response => $this->dispatch($request));
        } catch (\Throwable $exception) {
            return $this->handler->render($request, $exception);
        }
    }

    private function dispatch(Request $request): Response
    {
        ['route' => $route, 'params' => $params] = $this->router->match($request);

        return (new Pipeline($this->container))
            ->send($request)
            ->through($route->middleware)
            ->then(fn (Request $request): Response => $this->invoke($route, $request, $params));
    }

    /**
     * @param array<string, string> $params
     */
    private function invoke(Route $route, Request $request, array $params): Response
    {
        $action = $route->action;

        if ($action instanceof Closure) {
            $result = $action($request, ...array_values($params));
        } else {
            $result = $this->callController($action, $request, $params);
        }

        return $this->toResponse($result);
    }

    /**
     * @param array<string, string> $params
     */
    private function callController(mixed $action, Request $request, array $params): mixed
    {
        if (is_array($action) && isset($action[0], $action[1])) {
            [$class, $method] = $action;
        } elseif (is_string($action) && str_contains($action, '@')) {
            [$class, $method] = explode('@', $action, 2);
        } elseif (is_string($action) && class_exists($action)) {
            $class = $action;
            $method = '__invoke';
        } else {
            throw new RuntimeException('Invalid route action.');
        }

        $controller = $this->container->make($class);
        $arguments = $this->resolveMethodParameters($controller, $method, $request, $params);

        return $controller->{$method}(...$arguments);
    }

    /**
     * @param array<string, string> $params
     * @return list<mixed>
     */
    private function resolveMethodParameters(object $controller, string $method, Request $request, array $params): array
    {
        $reflection = new ReflectionMethod($controller, $method);
        $arguments = [];

        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            if ($type instanceof \ReflectionNamedType && ! $type->isBuiltin()) {
                $typeName = $type->getName();

                if ($typeName === Request::class || is_subclass_of($typeName, Request::class)) {
                    $arguments[] = $request;
                    continue;
                }

                $arguments[] = $this->container->make($typeName);
                continue;
            }

            if (array_key_exists($name, $params)) {
                $arguments[] = $params[$name];
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }

            throw new RuntimeException("Unable to resolve controller parameter [\${$name}].");
        }

        return $arguments;
    }

    private function toResponse(mixed $result): Response
    {
        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result) || is_object($result)) {
            return Response::json($result);
        }

        return Response::html((string) $result);
    }
}
