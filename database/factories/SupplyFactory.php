<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supply>
 */
class SupplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 50);
        $used = fake()->numberBetween(0, $quantity);
        return [
            'description' => fake()->word(),
            'unit' => fake()->randomElement(['pcs', 'pack']),
            'quantity' => $quantity,
            'used' => $used,
            'recently_added' => $quantity,
            'total' => $quantity - $used,
            'expiry_date' => fake()->dateTimeBetween('2024-10-01', '2025-10-01'),
            'is_consumable' => fake()->boolean(),
        ];
    }
}
