<?php

declare(strict_types=1);

namespace Lite\Auth;

use Illuminate\Database\Eloquent\Model;
use Lite\Session\Session;
use Lite\Support\Config;

final class Auth
{
    private ?Model $user = null;

    private bool $resolved = false;

    public function __construct(
        private readonly Session $session,
        private readonly Config $config,
    ) {
    }

    public function attempt(string $email, string $password): bool
    {
        $class = $this->modelClass();
        $user = $class::query()->where('email', $email)->first();

        if ($user === null || ! password_verify($password, (string) $user->password)) {
            return false;
        }

        $this->login($user);

        return true;
    }

    public function login(Model $user): void
    {
        $this->session->regenerate();
        $this->session->put('auth_id', $user->getKey());
        $this->user = $user;
        $this->resolved = true;
    }

    public function logout(): void
    {
        $this->session->forget('auth_id');
        $this->session->forget('url.intended');
        $this->session->regenerate();
        $this->user = null;
        $this->resolved = true;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function guest(): bool
    {
        return ! $this->check();
    }

    public function id(): int|string|null
    {
        return $this->user()?->getKey();
    }

    public function hasRole(string ...$roles): bool
    {
        $user = $this->user();

        if ($user === null || $roles === []) {
            return false;
        }

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole(...$roles);
        }

        $current = strtolower((string) ($user->getAttribute('role') ?? ''));

        foreach ($roles as $role) {
            if ($current === strtolower($role)) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole((string) $this->config->get('auth.admin_role', 'admin'));
    }

    public function user(): ?Model
    {
        if ($this->resolved) {
            return $this->user;
        }

        $this->resolved = true;
        $id = $this->session->get('auth_id');

        if ($id === null || $id === '') {
            return $this->user = null;
        }

        $class = $this->modelClass();
        $this->user = $class::query()->find($id);

        if ($this->user === null) {
            $this->session->forget('auth_id');
        }

        return $this->user;
    }

    /**
     * @return class-string<Model>
     */
    public function modelClass(): string
    {
        /** @var class-string<Model> $class */
        $class = (string) $this->config->get('auth.model', \App\Models\User::class);

        return $class;
    }
}
