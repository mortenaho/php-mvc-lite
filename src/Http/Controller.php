<?php

declare(strict_types=1);

namespace Lite\Http;

use Lite\Http\Request;
use Lite\Http\Response;
use Lite\Validation\Validator;
use Lite\Validation\ValidationException;

abstract class Controller
{
    protected function view(string $template, array $data = [], int $status = 200): Response
    {
        return Response::view($template, $data, $status);
    }

    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $to, int $status = 302): Response
    {
        return Response::redirect($to, $status);
    }

    /**
     * @param array<string, string> $rules
     * @return array<string, mixed>
     */
    protected function validate(Request $request, array $rules): array
    {
        $validator = new Validator($request->all(), $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors(), $request);
        }

        return $validator->validated();
    }
}
