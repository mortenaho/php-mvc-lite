<?php

declare(strict_types=1);

namespace Lite\Middleware;

use Closure;
use Lite\Http\Request;
use Lite\Http\Response;
use Lite\Session\Session;

final class StartSession implements MiddlewareInterface
{
    public function __construct(private readonly Session $session)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->session->start();
        $this->session->ageFlash();

        return $next($request);
    }
}
