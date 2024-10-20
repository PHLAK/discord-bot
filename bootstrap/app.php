<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Integration;

return Application::configure(
    basePath: dirname(__DIR__)
)->withRouting(
    web: __DIR__ . '/../routes/web.php',
    commands: __DIR__ . '/../routes/console.php',
    then: function (): void {
        Route::name('webhooks.')->prefix('webhooks')->middleware([
            App\Http\Middleware\WebhookMiddleware::class,
            App\Http\Middleware\AddTelescopeTags::class,
        ])->group(
            base_path('routes/webhooks.php')
        );
    }
)->withMiddleware(function (Middleware $middleware) {
    // ...
})->withExceptions(function (Exceptions $exceptions) {
    Integration::handles($exceptions);
})->create();
