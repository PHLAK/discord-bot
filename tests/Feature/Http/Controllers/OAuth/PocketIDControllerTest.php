<?php

namespace Tests\Feature\Http\Controllers\OAuth;

use App\Http\Controllers\OAuth\PocketIDController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as TwoUser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PocketIDController::class)]
class PocketIDControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_redirects_to_pocket_id_for_authorization(): void
    {
        Socialite::fake('pocketid');

        $response = $this->get(route('oauth.pocketid.redirect'));

        $response->assertRedirect('https://socialite.fake/pocketid/authorize');
    }

    #[Test]
    public function it_creates_a_user_and_logs_them_in_after_a_successful_callback(): void
    {
        Socialite::fake('pocketid', $this->makeSocialiteUser());

        $response = $this->get(route('oauth.pocketid.callback'));

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
            'pocketid_id' => 'pocket-id-123',
            'pocketid_token' => 'access-token',
            'pocketid_refresh_token' => 'refresh-token',
        ]);
    }

    #[Test]
    public function it_updates_an_existing_user_after_a_successful_callback(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        Socialite::fake('pocketid', $this->makeSocialiteUser());

        $this->get(route('oauth.pocketid.callback'));

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
            'pocketid_id' => 'pocket-id-123',
            'pocketid_token' => 'access-token',
            'pocketid_refresh_token' => 'refresh-token',
        ]);
    }

    #[Test]
    public function it_redirects_back_to_login_when_the_oauth_state_does_not_match(): void
    {
        Socialite::fake('pocketid', fn (): never => throw new InvalidStateException);

        $response = $this->get(route('oauth.pocketid.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    private function makeSocialiteUser(): TwoUser
    {
        return (new TwoUser)
            ->map([
                'id' => 'pocket-id-123',
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ])
            ->setToken('access-token')
            ->setRefreshToken('refresh-token');
    }
}
