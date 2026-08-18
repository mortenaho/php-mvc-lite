<?php

declare(strict_types=1);

return [
    'model' => App\Models\User::class,
    'admin_role' => 'admin',
    'policies' => [
        App\Models\Post::class => App\Policies\PostPolicy::class,
    ],
];
