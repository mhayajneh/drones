<?php

namespace Database\Factories;

use App\Models\Drone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DroneFactory extends Factory
{

    protected $model = Drone::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'serial' => strtoupper(fake()->bothify('????##??##??##??##')),
            'latitude' => fake()->latitude(31.5, 32.5),
            'longitude' => fake()->longitude(35.0, 36.0),
            'height' => fake()->randomFloat(2, 10, 100),
            'horizontal_speed' => fake()->randomFloat(2, 0, 5),
            'vertical_speed' => fake()->randomFloat(2, -2, 2),
            'elevation' => fake()->numberBetween(0, 1000),
            'gear' => fake()->numberBetween(1, 4),
            'is_online' => fake()->boolean(70),
            'is_dangerous' => false,
            'is_marked_safe' => false,
            'last_seen_at' => now(),
        ];
    }
    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_online' => true,
            'last_seen_at' => now(),
        ]);
    }

    public function dangerous(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_dangerous' => true,
            'height' => 550, // Above threshold
        ]);
    }
}
