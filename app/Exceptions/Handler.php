<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Sentry\Laravel\Integration;
use Throwable;

class Handler extends ExceptionHandler
{
    /** A list of the exception types that are not reported. */
    protected $dontReport = [];

    /** A list of the inputs that are never flashed for validation exceptions. */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /** Register the exception handling callbacks for the application. */
    public function register()
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
