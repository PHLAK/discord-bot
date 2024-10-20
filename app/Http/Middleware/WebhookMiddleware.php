<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        abort_unless($request->query('key') === config('webhooks.key'), Response::HTTP_FORBIDDEN, 'Forbidden');

        return $next($request);
    }
}
