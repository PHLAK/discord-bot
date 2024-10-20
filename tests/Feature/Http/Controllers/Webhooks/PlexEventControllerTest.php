<?php

namespace Tests\Feature\Http\Controllers\Webhooks;

use App\Events\PlexEventReceived;
use App\Http\Controllers\Webhooks\PlexEventController;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PlexEventController::class)]
class PlexEventControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([PlexEventReceived::class]);
    }

    #[Test]
    public function test_it_dispatches_an_event_when_a_plex_event_is_receieved(): void
    {
        $response = $this->postJson(route('webhooks.plex-event', [
            'key' => config('webhooks.key'),
        ]), [
            'payload' => json_encode(['event' => 'library.new']),
        ]);

        $response->assertSuccessful();

        Event::assertDispatched(function (PlexEventReceived $event): bool {
            return $event->payload->event === 'library.new' && $event->file === null;
        });
    }

    #[Test]
    public function it_does_not_dispatch_an_event_when_an_incorrect_plex_event_is_received(): void
    {
        $response = $this->postJson(route('webhooks.plex-event', [
            'key' => config('webhooks.key'),
        ]), [
            'payload' => json_encode(['event' => 'media.play']),
        ]);

        $response->assertSuccessful();

        Event::assertNotDispatched(PlexEventReceived::class);
    }

    #[Test]
    public function it_returns_an_error_when_receiving_a_malformed_payload(): void
    {
        $response = $this->postJson(route('webhooks.plex-event', [
            'key' => config('webhooks.key'),
        ]), ['payload' => 'INVALID_JSON']);

        $response->assertUnprocessable();

        Event::assertNotDispatched(PlexEventReceived::class);
    }
}
