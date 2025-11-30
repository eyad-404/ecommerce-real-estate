<?php

namespace Database\Factories;

use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'profile_image' => 'images/default-profile.png', // default image
        ];
    }
}
