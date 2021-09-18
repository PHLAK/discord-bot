<?php

namespace App\Listeners;

use App\Events\PlexEventReceived;
use App\File;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibraryNew implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private PendingRequest $request)
    {
    }

    public function shouldQueue(PlexEventReceived $event): bool
    {
        return $event->payload->event === 'library.new'
            && in_array($event->payload->Metadata->librarySectionTitle, config('services.plex.libraries', []));
    }

    public function handle(PlexEventReceived $event): void
    {
        if ($event->file instanceof File) {
            Storage::disk('public')->put($event->file->name(), $event->file->content());
        }

        $this->request->post(config('services.discord.webhook_url'), [
            'content' => sprintf(
                'New %s added to %s',
                Str::of($event->payload->Metadata->librarySectionTitle)->lower()->singular(),
                $event->payload->Server->title,
            ),
            'embeds' => $this->embeds($event),
        ]);
    }

    /** Get the middleware the job should pass through. */
    public function middleware(): array
    {
        return [new RateLimited('discord-webhooks')];
    }

    /** Get the embeds for the received PLEX event. */
    private function embeds(object $event): array
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

        if ($event->file instanceof File) {
            $embeds[0]['image'] = [
                'url' => Storage::disk('public')->url($event->file->name()),
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
