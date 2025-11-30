<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for all tests
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function users_management_page_loads()
    {
        $response = $this->actingAs($this->admin, 'web')
                         ->get(route('users-management'));

        $response->assertStatus(200);

        // Check that users and properties data exists
        $users = $response->viewData('users');
        $properties = $response->viewData('properties');

        $this->assertNotNull($users, "Users data missing in view");
        $this->assertNotNull($properties, "Properties data missing in view");
    }

    /** @test */
    public function store_creates_user()
    {
        $response = $this->actingAs($this->admin, 'web')
                         ->post(route('users.store'), [
                             'name' => 'John Doe',
                             'email' => 'john@example.com',
                             'password' => 'password123',
                             'birth_date' => '1990-01-01',
                             'gender' => 'male',
                             'location' => 'Cairo',
                             'phone' => '01012345678',
                             'role' => 'buyer',
                         ]);

        $response->assertStatus(302); // Redirect back with success
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function update_edits_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin, 'web')
                         ->put(route('users.update', $user->id), [
                             'name' => 'Updated Name',
                             'email' => $user->email,
                             'password' => '',
                             'birth_date' => $user->birth_date,
                             'gender' => $user->gender,
                             'location' => $user->location,
                             'phone' => $user->phone,
                             'role' => $user->role,
                         ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function destroy_deletes_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin, 'web')
                         ->delete(route('users.destroy', $user->id), [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
   /** @test */
public function search_returns_results()
{
    $targetUser = User::factory()->create(['name' => 'Search Me']);

    // Simulate AJAX request so controller returns JSON
    $response = $this->actingAs($this->admin, 'web')
                     ->get(route('users.search', ['query' => 'Search Me']), [
                         'HTTP_X-Requested-With' => 'XMLHttpRequest'
                     ]);

    $response->assertStatus(200);

    // Decode JSON response
    $users = collect($response->json());

    // Ensure collection contains the target user
    $found = $users->contains(fn($user) => $user['id'] === $targetUser->id);

    $this->assertTrue($found, 'Target user not found in search results');
}

}
