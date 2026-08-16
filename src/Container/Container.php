<?php

declare(strict_types=1);

namespace Lite\Container;

use Closure;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

final class Container implements ContainerInterface
{
    /** @var array<string, Closure|string> */
    private array $bindings = [];

    /** @var array<string, bool> */
    private array $singletons = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    /** @var array<string, true> */
    private array $buildStack = [];

    public function bind(string $id, Closure|string $concrete): void
    {
        $this->bindings[$id] = $concrete;
        unset($this->instances[$id], $this->singletons[$id]);
    }

    public function singleton(string $id, Closure|string $concrete): void
    {
        $this->bindings[$id] = $concrete;
        $this->singletons[$id] = true;
        unset($this->instances[$id]);
    }

    public function instance(string $id, mixed $instance): void
    {
        $this->instances[$id] = $instance;
        $this->singletons[$id] = true;
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id])
            || isset($this->bindings[$id])
            || class_exists($id);
    }

    public function get(string $id): mixed
    {
        return $this->make($id);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function make(string $id, array $parameters = []): mixed
    {
        if (isset($this->instances[$id]) && $parameters === []) {
            return $this->instances[$id];
        }

        if (isset($this->buildStack[$id])) {
            throw new RuntimeException("Circular dependency detected while resolving [{$id}].");
        }

        $this->buildStack[$id] = true;

        try {
            $concrete = $this->bindings[$id] ?? $id;
            $object = $concrete instanceof Closure
                ? $concrete($this, $parameters)
                : $this->build($concrete, $parameters);

            if (($this->singletons[$id] ?? false) && $parameters === []) {
                $this->instances[$id] = $object;
            }

            return $object;
        } finally {
            unset($this->buildStack[$id]);
        }
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function build(string $class, array $parameters): object
    {
        if (! class_exists($class)) {
            throw new RuntimeException("Target class [{$class}] does not exist.");
        }

        $reflection = new ReflectionClass($class);

        if (! $reflection->isInstantiable()) {
            throw new RuntimeException("Target class [{$class}] is not instantiable.");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $dependencies = array_map(
            fn (ReflectionParameter $parameter) => $this->resolveParameter($parameter, $parameters),
            $constructor->getParameters(),
        );

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function resolveParameter(ReflectionParameter $parameter, array $parameters): mixed
    {
        $name = $parameter->getName();

        if (array_key_exists($name, $parameters)) {
            return $parameters[$name];
        }

        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
            try {
                return $this->make($type->getName());
            } catch (RuntimeException $exception) {
                if ($parameter->isDefaultValueAvailable()) {
                    return $parameter->getDefaultValue();
                }

                throw $exception;
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new RuntimeException(sprintf(
            'Unable to resolve parameter $%s of %s.',
            $name,
            $parameter->getDeclaringClass()?->getName() ?? 'unknown',
        ));
    }
}
