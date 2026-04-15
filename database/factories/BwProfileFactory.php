<?php

namespace Database\Factories;

use App\Models\BwProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BwProfile>
 */
class BwProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word . ' ' . $this->faker->numberBetween(1, 100) . 'Mbps',
            'rate_limit' => $this->faker->numberBetween(1, 100) . 'M/' . $this->faker->numberBetween(1, 100) . 'M',
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
