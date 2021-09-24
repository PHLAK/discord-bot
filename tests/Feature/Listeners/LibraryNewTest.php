<?php

namespace Tests\Feature\Listeners;

use App\Events\PlexEventReceived;
use App\Listeners\LibraryNew;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** @covers \App\Listeners\LibraryNew */
class LibraryNewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.plex.libraries' => ['Movies', 'Shows', 'Music']]);
    }

    /**
     * @test
     * @dataProvider expectedPaylodDataProvider
     */
    public function it_should_queue_when_it_receieves_expected_payload_data(string $event, string $libraryTitle): void
    {
        $payload = (object) [
            'event' => $event,
            'Metadata' => (object) [
                'librarySectionTitle' => $libraryTitle,
            ],
        ];

        $this->assertTrue(App::make(LibraryNew::class)->shouldQueue(new PlexEventReceived($payload, null)));
    }

    /**
     * @test
     * @dataProvider unexpectedPaylodDataProvider
     */
    public function it_should_not_queue_for_an_unexpected_event_type(string $event, string $libraryTitle): void
    {
        $payload = (object) [
            'event' => $event,
            'Metadata' => (object) [
                'librarySectionTitle' => $libraryTitle,
            ],
        ];

        $this->assertFalse(App::make(LibraryNew::class)->shouldQueue(new PlexEventReceived($payload, null)));
    }

    /** @test */
    public function it_sends_a_webhook_request_for_an_episode(): void
    {
        Http::fake();

        $payload = json_decode(file_get_contents(base_path('tests/_data/events/library.new/episode.json')));

        App::make(LibraryNew::class)->handle(new PlexEventReceived($payload, null));

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://discord.test/api/webhooks/12345/abcdefg'
                && $request['content'] === 'New episode added to Some Server'
                && $request['embeds'] === [
                    [
                        'title' => 'Archer (2009)',
                        'description' => 'Shots',
                    ],
                ];
        });
    }

    public function expectedPaylodDataProvider(): array
    {
        return [
            ['library.new', 'Movies'],
            ['library.new', 'Shows'],
            ['library.new', 'Music'],
        ];
    }

    public function unexpectedPaylodDataProvider(): array
    {
        return [
            ['library.new', 'Other'],
            ['media.play', 'Movies'],
            ['media.pause', 'Shows'],
            ['media.resume', 'Music'],
            ['media.stop', 'Other'],
        ];
    }
}
