<?php

namespace App\Http\Controllers\Webhooks;

use App\Enums\Plex\Event;
use App\Events\PlexEventReceived;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlexEventRequest;
use Illuminate\Http\Response;
use JsonException;

class PlexEventController extends Controller
{
    /** Handle the incoming request. */
    public function __invoke(PlexEventRequest $request): Response
    {
        try {
            $payload = json_decode($request->payload, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid JSON');
        }

        if (Event::tryFrom($payload->event) === Event::LIBRARY_NEW) {
            PlexEventReceived::dispatch($payload, $request->thumb);
        }

        return response()->noContent();
    }
}
