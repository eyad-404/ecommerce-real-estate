<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $secretKey = env('PASSWORD_HMAC_KEY', 'testkey'); // fallback for testing
        $password = 'password';
        $hmacHash = hash_hmac('sha256', $password, $secretKey);
        $bcryptHash = Hash::make($hmacHash);

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $bcryptHash,
            'phone' => $this->faker->numerify('01#########'),
            'role' => $this->faker->randomElement(['admin','buyer','seller','developer']),
            'birth_date' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['male','female']),
            'location' => $this->faker->city(),
        ];
    }
}
