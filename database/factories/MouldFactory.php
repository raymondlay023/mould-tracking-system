<?php

namespace Database\Factories;

use App\Models\Mould;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mould>
 */
class MouldFactory extends Factory
{
    protected $model = Mould::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('MLD-####-??')),
            'name' => fake()->words(3, true) . ' Mould',
            'cavities' => fake()->numberBetween(1, 8),
            'customer' => fake()->company(),
            'resin' => fake()->randomElement(['PP', 'PE', 'ABS', 'PVC', 'Nylon', 'PC']),
            'min_tonnage_t' => $minTonnage = fake()->numberBetween(50, 300),
            'max_tonnage_t' => fake()->numberBetween($minTonnage, $minTonnage + 200),
            'pm_interval_shot' => fake()->numberBetween(50000, 200000),
            'pm_interval_days' => fake()->numberBetween(30, 180),
            'commissioned_at' => fake()->dateTimeBetween('-5 years', '-1 month'),
            'rmp_last_at' => fake()->optional(0.3)->dateTimeBetween('-1 year', 'now'),
            'rmp_approved_by' => fake()->optional(0.3)->name(),
            'status' => fake()->randomElement(['AVAILABLE', 'IN_SETUP', 'IN_RUN', 'IN_MAINTENANCE', 'IN_TRANSIT']),
        ];
    }

    /**
     * Indicate that the mould is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'AVAILABLE',
        ]);
    }

    /**
     * Indicate that the mould is in a production run.
     */
    public function inRun(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'IN_RUN',
        ]);
    }

    /**
     * Indicate that the mould is in maintenance.
     */
    public function inMaintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'IN_MAINTENANCE',
        ]);
    }
}
