<?php

namespace Database\Factories;

use App\Models\Machine;
use App\Models\MaintenanceEvent;
use App\Models\Mould;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaintenanceEvent>
 */
class MaintenanceEventFactory extends Factory
{
    protected $model = MaintenanceEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mould = Mould::factory()->create();
        $machine = Machine::factory()->create();
        $startTs = fake()->dateTimeBetween('-30 days', '-1 day');
        $downtimeMin = fake()->numberBetween(15, 480);

        return [
            'mould_id' => $mould->id,
            'machine_id' => fake()->optional(0.7)->passthrough($machine->id),
            'type' => fake()->randomElement(['PM', 'CM']),
            'start_ts' => $startTs,
            'end_ts' => fake()->dateTimeBetween($startTs, $startTs->format('Y-m-d H:i:s') . ' +' . $downtimeMin . ' minutes'),
            'downtime_min' => $downtimeMin,
            'next_due_date' => fake()->optional(0.5)->dateTimeBetween('now', '+180 days'),
            'next_due_shot' => fake()->optional(0.5)->numberBetween(50000, 200000),
            'performed_by' => fake()->name(),
            'notes' => fake()->optional(0.4)->sentence(),
        ];
    }

    /**
     * Indicate that the event is a Preventive Maintenance (PM).
     */
    public function pm(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'PM',
            'next_due_date' => fake()->dateTimeBetween('now', '+180 days'),
            'next_due_shot' => fake()->numberBetween(50000, 200000),
        ]);
    }

    /**
     * Indicate that the event is a Corrective Maintenance (CM).
     */
    public function cm(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'CM',
            'next_due_date' => null,
            'next_due_shot' => null,
        ]);
    }
}
