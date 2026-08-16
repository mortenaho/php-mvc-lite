<?php

declare(strict_types=1);

namespace Lite\Exceptions;

use Lite\Http\Request;
use Lite\Http\Response;
use Lite\Support\Config;
use Lite\Validation\ValidationException;
use Lite\View\ViewFactory;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Throwable;

final class Handler
{
    public function __construct(
        private readonly Config $config,
        private readonly ViewFactory $views,
    ) {
    }

    public function render(Request $request, Throwable $exception): Response
    {
        $this->report($exception);

        if ($exception instanceof ValidationException) {
            return $exception->toResponse();
        }

        $status = $exception instanceof HttpException ? $exception->status() : 500;
        $message = $exception instanceof HttpException
            ? $exception->getMessage()
            : ($this->config->get('app.debug') ? $exception->getMessage() : 'Server Error');

        if ($request->wantsJson()) {
            $payload = ['message' => $message, 'status' => $status];

            if ($this->config->get('app.debug')) {
                $payload['exception'] = $exception::class;
                $payload['file'] = $exception->getFile();
                $payload['line'] = $exception->getLine();
                $payload['trace'] = explode("\n", $exception->getTraceAsString());
            }

            $response = Response::json($payload, $status);
        } else {
            $template = $this->views->exists("errors.{$status}") ? "errors.{$status}" : 'errors.500';
            $response = Response::view($template, [
                'status' => $status,
                'message' => $message,
                'exception' => $exception,
            ], $status);
        }

        if ($exception instanceof HttpException) {
            foreach ($exception->headers() as $name => $value) {
                $response = $response->withHeader($name, $value);
            }
        }

        return $response;
    }

    public function report(Throwable $exception): void
    {
        self::logger()->error($exception->getMessage(), [
            'exception' => $exception::class,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }

    public static function logger(): LoggerInterface
    {
        static $logger;

        if ($logger instanceof LoggerInterface) {
            return $logger;
        }

        $path = storage_path('logs/lite.log');
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $logger = new Logger('lite');
        $logger->pushHandler(new StreamHandler($path, Level::Debug));

        return $logger;
    }
}
