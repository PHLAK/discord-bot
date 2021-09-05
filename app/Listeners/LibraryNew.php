<?php

namespace App\Listeners;

use App\Events\PlexEventReceived;
use App\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;

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

        if (! in_array($event->payload->Metadata->librarySectionTitle, config('plex.libraries', []))) {
            return;
        }

        if ($event->file instanceof File) {
            $this->request->attach('poster', $event->file->content(), $event->file->name());
        }

        $this->request->post(config('plex.webhook_url'), [
            'content' => sprintf(
                'New content added to %s: **%s**',
                $event->payload->Server->title,
                $event->payload->Metadata->title
            ),
        ]);
    }
}
