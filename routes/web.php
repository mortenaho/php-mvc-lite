<?php

declare(strict_types=1);

use App\Controllers\AccountController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use Lite\Middleware\Authenticate;
use Lite\Middleware\RedirectIfAuthenticated;
use Lite\Routing\Router;

return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index'])->name('home');

    $router->group(['middleware' => RedirectIfAuthenticated::class], function (Router $router): void {
        $router->get('/login', [AuthController::class, 'showLogin'])->name('login');
        $router->post('/login', [AuthController::class, 'login']);
        $router->get('/register', [AuthController::class, 'showRegister'])->name('register');
        $router->post('/register', [AuthController::class, 'register']);
    });

    $router->group(['middleware' => Authenticate::class], function (Router $router): void {
        $router->post('/logout', [AuthController::class, 'logout'])->name('logout');
        $router->get('/account', [AccountController::class, 'show'])->name('account');
        $router->get('/posts/create', [PostController::class, 'create'])->name('posts.create');
        $router->post('/posts', [PostController::class, 'store'])->name('posts.store');
        $router->get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
        $router->post('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
        $router->post('/posts/{id}/delete', [PostController::class, 'destroy'])->name('posts.destroy');
    });

    $router->get('/posts', [PostController::class, 'index'])->name('posts.index');
    $router->get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
};
