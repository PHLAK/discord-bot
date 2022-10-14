<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JsonException;
use Laravel\Telescope\Telescope;

class AddTelescopeTags
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->missing('payload')) {
            return $next($request);
        }

        try {
            $payload = json_decode($request->payload, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $next($request);
        }

        Telescope::tag(fn (): array => [$payload->event]);

        return $next($request);
    }
}
