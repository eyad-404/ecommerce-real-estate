<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'available', 'sold', 'reserved'];
        $transactionTypes = ['sale', 'rent'];

        return [
            'category' => $this->faker->randomElement(['Villa', 'Apartment', 'Townhouse', 'Studio']),
            'location' => $this->faker->city(),
            'price' => $this->faker->numberBetween(50000, 1000000),
            'status' => $this->faker->randomElement($statuses),
            'image' => 'images/properties/default.jpg', // default placeholder
            'user_id' => User::factory(), // creates a new user automatically
            'description' => $this->faker->paragraph(),
            'installment_years' => $this->faker->numberBetween(0, 10),
            'transaction_type' => $this->faker->randomElement($transactionTypes),
        ];
    }
}
