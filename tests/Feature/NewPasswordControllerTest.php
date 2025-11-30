<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tests\TestCase;

class NewPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function reset_password_page_loads()
    {
        $user = User::factory()->create();

        $response = $this->get(route('password.reset', [
            'token' => 'dummy',
            'email' => $user->email
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('myauth.reset-password');
        $response->assertViewHas('request');
    }

    /** @test */
    public function can_reset_password_successfully()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $newPassword = 'newpassword123';

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check(
            hash_hmac('sha256', $newPassword, env('PASSWORD_HMAC_KEY')),
            $user->fresh()->password
        ));
    }

    /** @test */
    public function reset_password_fails_with_invalid_token()
    {
        $user = User::factory()->create();

        $response = $this->from(route('password.reset', [
                'token' => 'wrong',
                'email' => $user->email
            ]))
            ->post(route('password.update'), [
                'token' => 'wrong',
                'email' => $user->email,
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertRedirect(route('password.reset', [
            'token' => 'wrong',
            'email' => $user->email
        ]));
        $response->assertSessionHasErrors('email');
    }
}
