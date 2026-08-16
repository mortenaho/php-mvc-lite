<?php

declare(strict_types=1);

namespace Lite\Middleware;

use Closure;
use Lite\Exceptions\HttpException;
use Lite\Http\Request;
use Lite\Http\Response;
use Lite\Session\Session;

final class VerifyCsrfToken implements MiddlewareInterface
{
    /** @var list<string> */
    private array $except = [];

    public function __construct(private readonly Session $session)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldVerify($request) && ! $this->tokensMatch($request)) {
            throw HttpException::forbidden('CSRF token mismatch.');
        }

        return $next($request);
    }

    private function shouldVerify(Request $request): bool
    {
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return false;
        }

        foreach ($this->except as $pattern) {
            if ($request->path() === $pattern) {
                return false;
            }
        }

        return true;
    }

    private function tokensMatch(Request $request): bool
    {
        $token = (string) (
            $request->input('_token')
            ?? $request->header('X-CSRF-TOKEN')
            ?? $request->header('X-XSRF-TOKEN')
            ?? ''
        );

        $sessionToken = $this->session->token();

        return $token !== '' && hash_equals($sessionToken, $token);
    }
}
