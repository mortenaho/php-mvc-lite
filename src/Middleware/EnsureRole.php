<?php

declare(strict_types=1);

namespace Lite\Middleware;

use Closure;
use Lite\Auth\Auth;
use Lite\Exceptions\HttpException;
use Lite\Http\Request;
use Lite\Http\Response;

final class EnsureRole implements MiddlewareInterface
{
    public function __construct(
        private readonly Auth $auth,
        private readonly string $parameter = '',
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $roles = array_values(array_filter(array_map('trim', explode(',', $this->parameter))));

        if ($roles !== [] && $this->auth->hasRole(...$roles)) {
            return $next($request);
        }

        throw HttpException::forbidden('This action is unauthorized.');
    }
}
