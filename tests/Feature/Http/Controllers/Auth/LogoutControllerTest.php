<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Http\Controllers\Auth\LogoutController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(LogoutController::class)]
class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_out_the_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    #[Test]
    public function it_redirects_guests_to_the_login_page(): void
    {
        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
    }
}
