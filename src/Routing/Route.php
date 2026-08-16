<?php

declare(strict_types=1);

namespace Lite\Routing;

final class Route
{
    /**
     * @param list<string> $methods
     * @param list<string> $middleware
     */
    public function __construct(
        public readonly array $methods,
        public readonly string $uri,
        public readonly mixed $action,
        public array $middleware = [],
        public ?string $name = null,
    ) {
    }

    public function middleware(string|array $middleware): self
    {
        $this->middleware = array_values(array_unique(array_merge(
            $this->middleware,
            is_array($middleware) ? $middleware : [$middleware],
        )));

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
