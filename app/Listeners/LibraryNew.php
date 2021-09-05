<?php

namespace App\Listeners;

use App\Events\PlexEventReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class LibraryNew implements ShouldQueue
{
    use InteractsWithQueue;

    /** Handle the event. */
    public function handle(PlexEventReceived $event): void
    {
        if ($event->payload->event !== 'library.new') {
            return;
        }

        if (! in_array($event->payload->Metadata->librarySectionTitle, config('plex.libraries', []))) {
            return;
        }

        $response = Http::attach('poster', base64_decode($event->fileContent), $event->fileName)->post(config('plex.webhook_url'), [
            'content' => sprintf(
                'New content added to %s: **%s**',
                $event->payload->Server->title,
                $event->payload->Metadata->title
            ),
        ]);
    }
}
