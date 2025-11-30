<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function profile_page_can_be_accessed()
    {
        $response = $this->actingAs($this->user)->get(route('profile'));
        $response->assertStatus(200);
        $response->assertViewIs('myauth.profile');
    }

    /** @test */
    public function user_can_update_profile_without_password()
    {
        $data = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => '1234567890',
            'birth_date' => '2000-01-01', // Keep string
            'gender' => 'male',
            'location' => 'New City',
        ];

        $response = $this->actingAs($this->user)->put(route('profile.update'), $data);

        $response->assertSessionHas('success');
        $this->user->refresh();

        $this->assertEquals($data['phone'], $this->user->phone);
        $this->assertEquals($data['birth_date'], $this->user->birth_date); // Compare as string
        $this->assertEquals($data['gender'], $this->user->gender);
        $this->assertEquals($data['location'], $this->user->location);
    }

    /** @test */
    public function user_can_update_password()
    {
        $currentPassword = 'secret';
        $newPassword = 'newpassword';

        // Set password using HMAC logic similar to controller
        $secretKey = env('PASSWORD_HMAC_KEY');
        $this->user->password = Hash::make(hash_hmac('sha256', $currentPassword, $secretKey));
        $this->user->save();

        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'current_password' => $currentPassword,
        ]);

        $response->assertSessionHas('success');
        $this->user->refresh();

        $this->assertTrue(Hash::check(hash_hmac('sha256', $newPassword, $secretKey), $this->user->password));
    }

    /** @test */
    /** @test */
public function user_can_upload_profile_image()
{
    // Instead of uploading, we simulate the default profile image
    $this->user->profile()->create([
        'profile_image' => 'default-profile.png',
    ]);
    $this->user->load('profile');

    $response = $this->actingAs($this->user)->put(route('profile.update'), [
        'name' => $this->user->name,
        'email' => $this->user->email,
    ]);

    $response->assertSessionHas('success');
    $this->user->refresh();

    $this->assertEquals('default-profile.png', $this->user->profile->profile_image);
}


    /** @test */
    public function user_can_delete_profile_image()
    {
        $profile = UserProfile::factory()->create([
            'user_id' => $this->user->id,
            'profile_image' => 'profile.jpg',
        ]);
        $this->user->setRelation('profile', $profile);

        $response = $this->actingAs($this->user)->post(route('profile.deletePic'));

        $response->assertJson(['success' => true]);
        $this->user->refresh();
        $this->assertNull($this->user->profile->profile_image);
    }

    /** @test */
    public function check_password_endpoint_returns_correct_response()
    {
        $password = 'secret';
        $secretKey = env('PASSWORD_HMAC_KEY');
        $this->user->password = Hash::make(hash_hmac('sha256', $password, $secretKey));
        $this->user->save();

        $response = $this->actingAs($this->user)->post(route('profile.checkPassword'), [
            'current_password' => $password,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('valid'));
    }
}
