<?php

namespace App\Listeners;

use App\Events\PlexEventReceived;
use App\File;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LibraryNew implements ShouldQueue
{
    use InteractsWithQueue;

    public function shouldQueue(PlexEventReceived $event): bool
    {
        return $event->payload->event === 'library.new'
            && in_array($event->payload->Metadata->librarySectionTitle, config('services.plex.libraries', []));
    }

    public function handle(PlexEventReceived $event): void
    {
        if ($event->file instanceof File) {
            $title = $event->payload->Metadata->grandparentTitle ?? $event->payload->Metadata->title;
            $fileName = sprintf('posters/%s.%s', sha1($title), $event->file->extension());

            Storage::disk('public')->put($fileName, $event->file->content());
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
        $embeds = match ($event->payload->Metadata->type) {
            'movie' => [
                [
                    'title' => $event->payload->Metadata->title,
                    'description' => $event->payload->Metadata->tagline,
                    'fields' => [
                        $this->inlineField('Year', $event->payload->Metadata->year),
                        $this->inlineField('Rating', $event->payload->Metadata->contentRating),
                        $this->field('Genre', Collection::make($event->payload->Metadata->Genre)->pluck('tag')->implode(', ')),
                        $this->field('Runtime', CarbonInterval::milliseconds($event->payload->Metadata->duration)->cascade()->forHumans(short: true)),
                    ],
                ],
            ],
            'episode' => [
                [
                    'title' => $event->payload->Metadata->grandparentTitle ?? $event->payload->Metadata->title,
                    'description' => $event->payload->Metadata->title,
                    // 'fields' => [
                    //     [
                    //         $this->inlineField('Season', '04'),
                    //         $this->inlineField('Episode', '12'),
                    //     ],
                    // ],
                ],
            ],
            'track' => [
                [

                    'title' => $event->payload->Metadata->title,
                    'description' => $event->payload->Metadata->title,
                ],
            ],
            default => [
                [
                    'title' => $event->payload->Metadata->title,
                    'description' => $event->payload->Metadata->grandparentTitle,
                ],
            ]
        };

        if (isset($fileName)) {
            $embeds[0]['image'] = [
                'url' => Storage::disk('public')->url($fileName),
            ];
        }

        return $embeds;
    }

    /** Build a field array. */
    private function field(string $name, string $value, bool $inline = false): array
    {
        return [
            'name' => $name,
            'value' => $value,
            'inline' => $inline,
        ];
    }

    /** Build an inline field array. */
    private function inlineField(string $name, string $value): array
    {
        return $this->field($name, $value, true);
    }
}
