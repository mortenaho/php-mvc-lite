<?php

declare(strict_types=1);

use Lite\Application;
use Lite\Routing\Router;

$app = Application::configure(dirname(__DIR__))->bootstrap();

/** @var Router $router */
$router = $app->make(Router::class);

(require dirname(__DIR__) . '/routes/web.php')($router);

return $app;
