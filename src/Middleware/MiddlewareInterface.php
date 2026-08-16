<?php

declare(strict_types=1);

namespace Lite\Middleware;

use Closure;
use Lite\Http\Request;
use Lite\Http\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response;
}
