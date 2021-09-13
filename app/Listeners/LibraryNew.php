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
                'New %s added to %s',
                Str::of($event->payload->Metadata->librarySectionTitle)->lower()->singular(),
                $event->payload->Server->title,
            ),
            'embeds' => $this->embeds($event->payload),
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
        return match ($event->Metadata->librarySectionType) {
            'movie' => [
                [
                    'title' => $event->Metadata->title,
                    'description' => $event->Metadata->tagline,
                    'fields' => [
                        $this->inlineField('Year', '1986'),
                        $this->inlineField('Runtime', '1:23:45'),
                        $this->inlineField('Rating', 'PG-13'),
                    ],
                ],
            ],
            'show' => [
                'title' => $event->Metadata->title,
                'description' => $event->Metadata->title,
                'fields' => [
                    [
                        $this->inlineField('Season', '04'),
                        $this->inlineField('Episode', '12'),
                    ],
                ],
            ],
            'music' => [
                'title' => $event->Metadata->title,
                'description' => $event->Metadata->title,
                'fields' => [
                    [
                        $this->inlineField('Year', '1986'),
                    ],
                ],
            ],
            default => [
                'title' => $event->Metadata->title,
            ]
        };
    }

    /** Get a field array. */
    private function field(string $name, string $value, bool $inline = false): array
    {
        return [
            'name' => $name,
            'value' => $value,
            'inline' => $inline,
        ];
    }

    /** get an inline field array. */
    private function inlineField(string $name, string $value): array
    {
        return $this->field($name, $value, true);
    }
}
