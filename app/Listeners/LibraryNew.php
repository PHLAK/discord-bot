<?php

namespace App\Listeners;

use App\Enums\Plex\Event;
use App\Enums\Plex\MetadataType;
use App\Events\PlexEventReceived;
use App\File;
use App\Listeners\Traits\ReportOnFailure;
use App\Listeners\Traits\RetryWithBackoff;
use App\Support\Embed;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LibraryNew implements ShouldQueue
{
    use InteractsWithQueue, ReportOnFailure, RetryWithBackoff;

    public function __construct(
        public Filesystem $storage
    ) {
    }

    public function shouldQueue(PlexEventReceived $event): bool
    {
        return Event::tryFrom($event->payload->event) === Event::LIBRARY_NEW
            && in_array(MetadataType::tryFrom($event->payload->Metadata->type), config('services.plex.enabled_types'))
            && in_array($event->payload->Metadata->librarySectionTitle, config('services.plex.libraries', []));
    }

    public function handle(PlexEventReceived $event): void
    {
        Log::withContext([
            'server' => $event->payload->Server->title,
            'event' => $event->payload->event,
            'type' => $event->payload->Metadata->type,
        ]);

        if ($event->file instanceof File) {
            $title = $event->payload->Metadata->grandparentTitle ?? $event->payload->Metadata->title;
            $fileName = sprintf('posters/%s.%s', sha1($title), $event->file->extension);

            $this->storage->put($fileName, $event->file->content);
        }

        Http::post(config('services.discord.webhook_url'), [
            'content' => sprintf('New %s added to %s', $event->payload->Metadata->type, $event->payload->Server->title),
            'embeds' => $this->embeds($event, $fileName ?? null),
        ]);
    }

    /** Get the middleware the job should pass through. */
    public function middleware(): array
    {
        return [new RateLimited('discord-webhooks')];
    }

    /** Get the embeds for the received PLEX event. */
    private function embeds(object $event, string $fileName = null): array
    {
        $embeds = match (MetadataType::tryFrom($event->payload->Metadata->type)) {
            MetadataType::MOVIE => [
                [
                    'title' => $event->payload->Metadata->title,
                    'description' => $event->payload->Metadata->tagline ?? '',
                    'fields' => [
                        Embed::inlineField('Year', $event->payload->Metadata->year),
                        Embed::inlineField('Rating', $event->payload->Metadata->contentRating ?? 'Not Rated'),
                        Embed::field('Genre', Collection::make($event->payload->Metadata->Genre)->pluck('tag')->implode(', ')),
                        Embed::field('Runtime', CarbonInterval::milliseconds($event->payload->Metadata->duration)->cascade()->forHumans(short: true)),
                    ],
                ],
            ],
            MetadataType::EPISODE => [
                [
                    'title' => $event->payload->Metadata->grandparentTitle ?? $event->payload->Metadata->title,
                    'description' => $event->payload->Metadata->title,
                    'fields' => [
                        Embed::inlineField('Season', $event->payload->Metadata->parentIndex),
                        Embed::inlineField('Episode', $event->payload->Metadata->index),
                    ],
                ],
            ],
            MetadataType::ALBUM => [
                [
                    'title' => $event->payload->Metadata->title,
                    'description' => $event->payload->Metadata->title,
                ],
            ],
            MetadataType::TRACK => [
                [
                    'title' => $event->payload->Metadata->title,
                    'description' => $event->payload->Metadata->title,
                ],
            ],
            default => [
                [
                    'title' => $event->payload->Metadata->title,
                    'description' => $event->payload->Metadata->grandparentTitle ?? null,
                ],
            ]
        };

        if (isset($fileName)) {
            $embeds[0]['image'] = ['url' => url($this->storage->url($fileName))];
        }

        return $embeds;
    }
}
