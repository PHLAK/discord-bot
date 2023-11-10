<?php

namespace Tests\Feature\Listeners;

use App\Enums\Plex\MetadataType;
use App\Events\PlexEventReceived;
use App\Listeners\LibraryNew;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
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
        config(['services.plex.enabled_types' => [MetadataType::ALBUM, MetadataType::MOVIE, MetadataType::SHOW]]);
    }

    public static function expectedPaylodDataProvider(): array
    {
        return [
            ['library.new', 'Movies', 'movie'],
            ['library.new', 'Shows', 'show'],
            ['library.new', 'Music', 'album'],
        ];
    }

    public static function unexpectedPaylodDataProvider(): array
    {
        return [
            ['library.new', 'Other', 'movie'],
            ['library.new', 'Movies', 'other'],
            ['media.play', 'Movies', 'movie'],
            ['media.pause', 'Shows', 'show'],
            ['media.resume', 'Music', 'album'],
            ['media.stop', 'Other', 'other'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider expectedPaylodDataProvider
     */
    public function it_should_queue_when_it_receieves_expected_payload_data(string $event, string $libraryTitle, string $metadataType): void
    {
        $payload = (object) [
            'event' => $event,
            'Metadata' => (object) [
                'librarySectionTitle' => $libraryTitle,
                'type' => $metadataType,
            ],
        ];

        $this->assertTrue(App::make(LibraryNew::class)->shouldQueue(new PlexEventReceived($payload, null)));
    }

    /**
     * @test
     *
     * @dataProvider unexpectedPaylodDataProvider
     */
    public function it_should_not_queue_for_an_unexpected_event_type(string $event, string $libraryTitle, string $metadataType): void
    {
        $payload = (object) [
            'event' => $event,
            'Metadata' => (object) [
                'librarySectionTitle' => $libraryTitle,
                'type' => $metadataType,
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
                        'fields' => [
                            ['name' => 'Season', 'value' => '12', 'inline' => true],
                            ['name' => 'Episode', 'value' => '5', 'inline' => true],
                        ],
                    ],
                ];
        });
    }

    /** @test */
    public function it_sends_a_webhook_request_for_a_movie_with_an_image(): void
    {
        Http::fake();

        $payload = json_decode((string) file_get_contents(base_path('tests/_data/events/library.new/movie.json')));
        $file = UploadedFile::fake()->image('test.png');

        App::make(LibraryNew::class)->handle(new PlexEventReceived($payload, $file));

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://discord.test/api/webhooks/12345/abcdefg'
                && $request['content'] === 'New movie added to Some Server'
                && $request['embeds'] === [
                    [
                        'title' => 'The Matrix',
                        'description' => 'Welcome to the Real World.',
                        'fields' => [
                            ['name' => 'Year', 'value' => '1999', 'inline' => true],
                            ['name' => 'Rating', 'value' => 'R', 'inline' => true],
                            ['name' => 'Genre', 'value' => 'Action, Science Fiction', 'inline' => false],
                            ['name' => 'Runtime', 'value' => '2h 16m', 'inline' => false],
                        ],
                        'image' => [
                            'url' => url('storage/posters/802a278701a98768802a20e76602f713ca05b68e.png'),
                        ],
                    ],
                ];
        });
    }
}
