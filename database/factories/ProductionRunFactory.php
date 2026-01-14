<?php

namespace Database\Factories;

use App\Models\Machine;
use App\Models\Mould;
use App\Models\ProductionRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductionRun>
 */
class ProductionRunFactory extends Factory
{
    protected $model = ProductionRun::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mould = Mould::factory()->create();
        $machine = Machine::factory()->create();
        $cavities = $mould->cavities;
        $shotTotal = fake()->numberBetween(100, 5000);
        $partTotal = $shotTotal * $cavities;
        $ngPart = fake()->numberBetween(0, (int)($partTotal * 0.05)); // 0-5% NG rate
        $okPart = $partTotal - $ngPart;

        return [
            'mould_id' => $mould->id,
            'machine_id' => $machine->id,
            'start_ts' => $startTs = fake()->dateTimeBetween('-7 days', '-1 hour'),
            'end_ts' => fake()->dateTimeBetween($startTs, 'now'),
            'cavities_snapshot' => $cavities,
            'shot_total' => $shotTotal,
            'part_total' => $partTotal,
            'ok_part' => $okPart,
            'ng_part' => $ngPart,
            'cycle_time_avg_sec' => fake()->numberBetween(20, 120),
            'operator_name' => fake()->name(),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the production run is active (not closed yet).
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_ts' => null,
            'shot_total' => 0,
            'part_total' => 0,
            'ok_part' => 0,
            'ng_part' => 0,
            'cycle_time_avg_sec' => null,
            'notes' => null,
        ]);
    }

    /**
     * Indicate that the production run is closed.
     */
    public function closed(): static
    {
        return $this->state(function (array $attributes) {
            $cavities = $attributes['cavities_snapshot'];
            $shotTotal = fake()->numberBetween(100, 5000);
            $partTotal = $shotTotal * $cavities;
            $ngPart = fake()->numberBetween(0, (int)($partTotal * 0.05));
            $okPart = $partTotal - $ngPart;

            return [
                'end_ts' => fake()->dateTimeBetween($attributes['start_ts'], 'now'),
                'shot_total' => $shotTotal,
                'part_total' => $partTotal,
                'ok_part' => $okPart,
                'ng_part' => $ngPart,
                'cycle_time_avg_sec' => fake()->numberBetween(20, 120),
            ];
        });
    }
}
