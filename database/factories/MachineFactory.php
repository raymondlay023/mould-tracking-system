<?php

namespace Database\Factories;

use App\Models\Machine;
use App\Models\Plant;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Machine>
 */
class MachineFactory extends Factory
{
    protected $model = Machine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plant = Plant::factory()->create();
        $zone = Zone::factory()->create(['plant_id' => $plant->id]);

        return [
            'plant_id' => $plant->id,
            'zone_id' => $zone->id,
            'code' => strtoupper(fake()->unique()->bothify('MC-###')),
            'name' => 'Machine ' . fake()->numerify('IM-####'),
            'tonnage_t' => fake()->randomElement([80, 120, 150, 200, 250, 300, 400, 500]),
        ];
    }
}
