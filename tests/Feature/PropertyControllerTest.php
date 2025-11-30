<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Models\PropertyReservation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyControllerTest extends TestCase
{
    protected $admin;
    protected $seller;
    protected $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->seller = User::factory()->create(['role' => 'seller']);
        $this->buyer = User::factory()->create(['role' => 'buyer']);
    }

    /** @test */
   public function test_admin_can_create_property_with_fake_image()
{
    $this->actingAs($this->admin);

    // Fake the file manually
    $fakeImage = UploadedFile::fake()->image('villa.jpg');

    $response = $this->postJson(route('properties.store'), [
        'category' => 'Villa',
        'location' => 'Cairo',
        'price' => 1000000,
        'status' => 'available',
        'description' => 'Luxury villa',
        'transaction_type' => 'sale',
        'image' => $fakeImage,
    ]);

    $response->assertStatus(201)
             ->assertJson(['success' => true]);

    $this->assertDatabaseHas('properties', [
        'category' => 'Villa',
        'location' => 'Cairo',
        'user_id' => $this->admin->id,
    ]);

    // Skip Storage assert since controller uses move()
    $property = \App\Models\Property::first();
    $this->assertNotEmpty($property->image);
}

    /** @test */
    public function seller_can_create_property_and_be_owner()
    {
        $this->actingAs($this->seller);

        Storage::fake('public');

        $response = $this->postJson(route('properties.store'), [
            'category' => 'Apartment',
            'location' => 'Alexandria',
            'price' => 500000,
            'status' => 'available',
            'description' => 'Seaside apartment',
            'transaction_type' => 'sale',
            'image' => UploadedFile::fake()->image('apartment.jpg'),
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('properties', [
            'category' => 'Apartment',
            'location' => 'Alexandria',
            'user_id' => $this->seller->id,
        ]);
    }

    /** @test */
    public function admin_can_update_property()
    {
        $this->actingAs($this->admin);

        $property = Property::factory()->create(['user_id' => $this->seller->id]);

        $response = $this->putJson(route('properties.update', $property), [
            'category' => 'Updated Category',
            'transaction_type' => 'sale',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'category' => 'Updated Category',
        ]);
    }

    /** @test */
    public function owner_can_delete_property()
    {
        $this->actingAs($this->seller);

        $property = Property::factory()->create(['user_id' => $this->seller->id]);

        $response = $this->deleteJson(route('properties.destroy', $property));
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('properties', [
            'id' => $property->id,
        ]);
    }

    /** @test */
    public function buyer_can_reserve_property()
    {
        $this->actingAs($this->buyer);

        $property = Property::factory()->create(['status' => 'available']);

        $response = $this->post(route('properties.reserve', $property));

        $response->assertRedirect();
        $this->assertDatabaseHas('property_reservations', [
            'property_id' => $property->id,
            'user_id' => $this->buyer->id,
        ]);

        $this->assertEquals('reserved', $property->fresh()->status);
    }

    /** @test */
    public function buyer_can_cancel_own_reservation()
    {
        $this->actingAs($this->buyer);

        $property = Property::factory()->create(['status' => 'reserved']);
        PropertyReservation::create([
            'property_id' => $property->id,
            'user_id' => $this->buyer->id,
            'reserved_at' => now(),
        ]);

        $response = $this->delete(route('properties.cancelReservation', $property));

        $response->assertRedirect();
        $this->assertDatabaseMissing('property_reservations', [
            'property_id' => $property->id,
            'user_id' => $this->buyer->id,
        ]);

        $this->assertEquals('available', $property->fresh()->status);
    }

    /** @test */
    public function admin_can_cancel_any_reservation()
    {
        $this->actingAs($this->admin);

        $property = Property::factory()->create(['status' => 'reserved']);
        PropertyReservation::create([
            'property_id' => $property->id,
            'user_id' => $this->buyer->id,
            'reserved_at' => now(),
        ]);

        $response = $this->delete(route('properties.cancelReservation', $property));

        $response->assertRedirect();
        $this->assertDatabaseMissing('property_reservations', [
            'property_id' => $property->id,
        ]);

        $this->assertEquals('available', $property->fresh()->status);
    }
}
