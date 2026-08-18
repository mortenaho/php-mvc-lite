<?php

declare(strict_types=1);

namespace Lite\Auth;

use Illuminate\Database\Eloquent\Model;
use Lite\Container\Container;
use Lite\Exceptions\HttpException;

final class Gate
{
    /** @var array<string, callable> */
    private array $abilities = [];

    /** @var array<class-string, class-string> */
    private array $policies = [];

    /** @var list<callable> */
    private array $beforeCallbacks = [];

    public function __construct(
        private readonly Auth $auth,
        private readonly Container $container,
    ) {
    }

    public function define(string $ability, callable $callback): self
    {
        $this->abilities[$ability] = $callback;

        return $this;
    }

    /**
     * @param class-string $model
     * @param class-string $policy
     */
    public function policy(string $model, string $policy): self
    {
        $this->policies[$model] = $policy;

        return $this;
    }

    public function before(callable $callback): self
    {
        $this->beforeCallbacks[] = $callback;

        return $this;
    }

    public function allows(string $ability, mixed $arguments = null): bool
    {
        $user = $this->auth->user();
        $target = $this->target($arguments);
        $parameters = $this->parameters($arguments);

        foreach ($this->beforeCallbacks as $callback) {
            $result = $callback($user, $ability, $parameters);

            if ($result !== null) {
                return (bool) $result;
            }
        }

        if (isset($this->abilities[$ability])) {
            return (bool) ($this->abilities[$ability])($user, ...$parameters);
        }

        $policy = $this->resolvePolicy($target);

        if ($policy === null) {
            return false;
        }

        if (method_exists($policy, 'before')) {
            $result = $policy->before($user, $ability);

            if ($result !== null) {
                return (bool) $result;
            }
        }

        if ($user === null || ! method_exists($policy, $ability)) {
            return false;
        }

        return (bool) $policy->{$ability}($user, ...$parameters);
    }

    public function denies(string $ability, mixed $arguments = null): bool
    {
        return ! $this->allows($ability, $arguments);
    }

    public function authorize(string $ability, mixed $arguments = null): void
    {
        if ($this->denies($ability, $arguments)) {
            throw HttpException::forbidden('This action is unauthorized.');
        }
    }

    private function target(mixed $arguments): mixed
    {
        if (is_array($arguments)) {
            return $arguments[0] ?? null;
        }

        return $arguments;
    }

    /**
     * @return list<mixed>
     */
    private function parameters(mixed $arguments): array
    {
        if ($arguments === null || (is_string($arguments) && class_exists($arguments))) {
            return [];
        }

        if (is_array($arguments)) {
            return array_values(array_filter(
                $arguments,
                static fn (mixed $argument): bool => ! (is_string($argument) && class_exists($argument)),
            ));
        }

        return [$arguments];
    }

    private function resolvePolicy(mixed $target): ?object
    {
        $class = match (true) {
            $target instanceof Model, is_object($target) => $target::class,
            is_string($target) && class_exists($target) => $target,
            default => null,
        };

        if ($class === null || ! isset($this->policies[$class])) {
            return null;
        }

        return $this->container->make($this->policies[$class]);
    }
}
