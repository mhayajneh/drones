<?php

namespace Database\Factories;

use App\Models\Drone;
use App\Models\LocationHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LocationHistoryFactory extends Factory
{

    protected $model = LocationHistory::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'drone_id' => Drone::factory(),
            'latitude' => fake()->latitude(31.5, 32.5),
            'longitude' => fake()->longitude(35.0, 36.0),
            'height' => fake()->randomFloat(2, 10, 100),
            'horizontal_speed' => fake()->randomFloat(2, 0, 5),
            'vertical_speed' => fake()->randomFloat(2, -2, 2),
            'recorded_at' => now(),
        ];
    }
}
