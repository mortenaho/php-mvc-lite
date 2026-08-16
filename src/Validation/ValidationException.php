<?php

declare(strict_types=1);

namespace Lite\Validation;

use Lite\Http\Request;
use Lite\Http\Response;
use Lite\Session\Session;
use RuntimeException;

final class ValidationException extends RuntimeException
{
    /**
     * @param array<string, list<string>> $errors
     */
    public function __construct(
        private readonly array $errors,
        private readonly Request $request,
    ) {
        parent::__construct('The given data was invalid.', 422);
    }

    /**
     * @return array<string, list<string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function toResponse(): Response
    {
        if ($this->request->wantsJson()) {
            return Response::json([
                'message' => $this->getMessage(),
                'errors' => $this->errors,
            ], 422);
        }

        /** @var Session $session */
        $session = app(Session::class);
        $session->flash('errors', $this->errors);
        $session->flash('old', $this->request->all());

        $referer = $this->request->header('Referer');

        return Response::redirect($referer ?: '/');
    }
}
