<?php

declare(strict_types=1);

namespace Lite\Middleware;

use Closure;
use Lite\Auth\Auth;
use Lite\Http\Request;
use Lite\Http\Response;

final class RedirectIfAuthenticated implements MiddlewareInterface
{
    public function __construct(private readonly Auth $auth)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->auth->guest()) {
            return $next($request);
        }

        if ($request->wantsJson()) {
            return Response::json(['message' => 'Already authenticated.'], 409);
        }

        return Response::redirect('/');
    }
}
