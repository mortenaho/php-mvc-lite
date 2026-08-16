<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\PostController;
use Lite\Routing\Router;

return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index'])->name('home');

    $router->get('/posts', [PostController::class, 'index'])->name('posts.index');
    $router->get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    $router->post('/posts', [PostController::class, 'store'])->name('posts.store');
    $router->get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
    $router->get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
    $router->post('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
    $router->post('/posts/{id}/delete', [PostController::class, 'destroy'])->name('posts.destroy');
};
