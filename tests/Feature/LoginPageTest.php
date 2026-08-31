<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class LoginPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_login_page_for_guests(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Sign in with Pocket ID');
        $response->assertSee(route('oauth.pocketid.redirect'), escape: false);
    }

    #[Test]
    public function it_redirects_authenticated_users_to_the_home_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect('/');
    }
}
