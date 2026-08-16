<?php

declare(strict_types=1);

use Lite\Application;
use Lite\Http\Request;

define('LITE_START', microtime(true));

require dirname(__DIR__) . '/vendor/autoload.php';

/** @var Application $app */
$app = require dirname(__DIR__) . '/bootstrap/app.php';

$request = Request::capture();
$response = $app->handle($request);
$app->terminate($request, $response);
