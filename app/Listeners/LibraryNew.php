<?php

namespace App\Listeners;

use App\Events\PlexEventReceived;
use App\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Str;

class LibraryNew implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private PendingRequest $request)
    {
    }

    public function handle(PlexEventReceived $event): void
    {
        if ($event->payload->event !== 'library.new') {
            return;
        }

        if (! in_array($event->payload->Metadata->librarySectionTitle, config('services.plex.libraries', []))) {
            return;
        }

        if ($event->file instanceof File) {
            $this->request->attach('poster', $event->file->content(), $event->file->name());
        }

        $this->request->post(config('services.discord.webhook_url'), [
            'content' => sprintf(
                'New %s added to %s: **%s**',
                Str::of($event->payload->Metadata->librarySectionTitle)->lower()->singular(),
                $event->payload->Server->title,
                $event->payload->Metadata->title
            ),
        ]);
    }

    /** Get the middleware the job should pass through. */
    public function middleware(): array
    {
        return [new RateLimited('discord-webhooks')];
    }
}
