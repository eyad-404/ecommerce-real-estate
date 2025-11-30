<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetLinkControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function forgot_password_page_loads()
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('myauth.forgot-password');
    }

    /** @test */
    public function can_request_password_reset_link()
    {
        // Create a test user
        $user = User::factory()->create();

        // Fake notifications
        Notification::fake();

        // Request password reset
        $response = $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        // Assert a ResetPassword notification was sent to the user
        Notification::assertSentTo($user, ResetPassword::class);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    /** @test */
    public function requesting_password_reset_for_invalid_email_fails()
    {
        Notification::fake();

        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        // Laravel returns back with errors if email not found
        $response->assertRedirect();
        $response->assertSessionHasErrors('email');

        Notification::assertNothingSent();
    }
}
