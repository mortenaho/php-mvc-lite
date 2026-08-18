<?php

declare(strict_types=1);

namespace Lite\Auth;

trait HasRoles
{
    public function hasRole(string ...$roles): bool
    {
        if ($roles === []) {
            return false;
        }

        $current = strtolower((string) ($this->getAttribute('role') ?? ''));

        foreach ($roles as $role) {
            if ($current === strtolower($role)) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole((string) config('auth.admin_role', 'admin'));
    }
}
