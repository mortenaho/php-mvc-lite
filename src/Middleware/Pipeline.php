<?php

declare(strict_types=1);

namespace Lite\Middleware;

use Closure;
use Lite\Container\Container;
use Lite\Http\Request;
use Lite\Http\Response;
use RuntimeException;

final class Pipeline
{
    private Request $request;

    /** @var list<class-string|string|callable> */
    private array $pipes = [];

    public function __construct(private readonly Container $container)
    {
    }

    public function send(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @param list<class-string|string|callable> $pipes
     */
    public function through(array $pipes): self
    {
        $this->pipes = array_values(array_filter($pipes));

        return $this;
    }

    public function then(Closure $destination): Response
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            fn (Closure $next, mixed $pipe): Closure => function (Request $request) use ($next, $pipe): Response {
                $middleware = $this->resolve($pipe);

                if ($middleware instanceof MiddlewareInterface) {
                    return $middleware->handle($request, $next);
                }

                if (is_callable($middleware)) {
                    return $middleware($request, $next);
                }

                throw new RuntimeException('Invalid middleware [' . (is_object($pipe) ? $pipe::class : (string) $pipe) . '].');
            },
            $destination,
        );

        return $pipeline($this->request);
    }

    private function resolve(mixed $pipe): mixed
    {
        if (is_callable($pipe) && ! is_string($pipe)) {
            return $pipe;
        }

        if (! is_string($pipe)) {
            return $this->container->make($pipe);
        }

        if (! str_contains($pipe, ':')) {
            return $this->container->make($pipe);
        }

        [$class, $parameter] = explode(':', $pipe, 2);

        return $this->container->make($class, ['parameter' => $parameter]);
    }
}
