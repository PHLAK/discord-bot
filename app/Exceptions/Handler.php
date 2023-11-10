<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Sentry\Laravel\Integration;
use Throwable;

class Handler extends ExceptionHandler
{
    /** The list of the inputs that are never flashed to the session on validation exceptions. */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /** Register the exception handling callbacks for the application. */
    public function register(): void
    {
        $this->reportable(function (Throwable $exception) {
            Integration::captureUnhandledException($exception);
        });
    }

    public function report(Throwable $exception)
    {
        if ($this->container->bound('sentry') && $this->shouldReport($exception)) {
            $this->container->make('sentry')->captureException($exception);
        }

        parent::report($exception);
    }
}
