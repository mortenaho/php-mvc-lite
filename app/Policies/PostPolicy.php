<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

final class PostPolicy
{
    public function before(?User $user, string $ability): ?bool
    {
        if ($user?->isAdmin()) {
            return true;
        }

        return null;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Post $post): bool
    {
        return (string) $user->id === (string) $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }
}
