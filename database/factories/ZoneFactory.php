<?php

namespace Database\Factories;

use App\Models\Plant;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Zone>
 */
class ZoneFactory extends Factory
{
    protected $model = Zone::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plant_id' => Plant::factory(),
            'code' => strtoupper(fake()->unique()->bothify('Z-##')),
            'name' => 'Zone ' . fake()->randomElement(['A', 'B', 'C', 'D', 'Production', 'Assembly', 'Quality']),
        ];
    }
}
