<?php

declare(strict_types=1);

namespace Lite\Middleware;

use Closure;
use Lite\Auth\Auth;
use Lite\Http\Request;
use Lite\Http\Response;
use Lite\Session\Session;

final class Authenticate implements MiddlewareInterface
{
    public function __construct(
        private readonly Auth $auth,
        private readonly Session $session,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->auth->check()) {
            return $next($request);
        }

        if ($request->wantsJson()) {
            return Response::json(['message' => 'Unauthenticated.'], 401);
        }

        $this->session->put('url.intended', $request->path());

        return Response::redirect('/login');
    }
}
