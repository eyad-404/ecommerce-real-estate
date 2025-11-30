<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase; // resets DB after each test

    /** @test */
    public function register_page_can_be_accessed()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('myauth.register');
    }

    /** @test */
    public function user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '01234567890',
            'role' => 'buyer',
            'birth_date' => '1995-01-01',
            'gender' => 'male',
            'location' => 'Cairo',
        ]);

        //dd(\App\Models\User::all()->toArray());

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com'
    ]);

        $response->assertRedirect(route('home'));
    }

    /** @test */
    public function login_page_can_be_accessed()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('myauth.login');
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $secretKey = env('PASSWORD_HMAC_KEY');
        $password = 'password123';
        $hmac = hash_hmac('sha256', $password, $secretKey);

        $user = User::factory()->create([
            'email' => 'testlogin@example.com',
            'password' => bcrypt($hmac),
        ]);

        $response = $this->post('/login', [
            'email' => 'testlogin@example.com',
            'password' => $password,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_wrong_credentials()
    {
        $user = User::factory()->create([
            'email' => 'wrong@example.com',
            'password' => bcrypt('wrongpassword'),
        ]);

        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'incorrect',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** @test */
    public function check_email_endpoint_returns_correct_response()
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com'
        ]);

        $response = $this->post('/check-email', ['email' => 'existing@example.com']);
        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);

        $response = $this->post('/check-email', ['email' => 'notfound@example.com']);
        $response->assertJson(['exists' => false]);
    }
}
