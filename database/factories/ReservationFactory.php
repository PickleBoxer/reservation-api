<?php

namespace Database\Factories;

use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('now', '+30 days');
        $endTime = (clone $startTime)->modify('+'.fake()->numberBetween(1, 8).' hours');

        return [
            'resource_id' => Resource::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'notes' => fake()->optional(0.6)->paragraph(),
        ];
    }
}
