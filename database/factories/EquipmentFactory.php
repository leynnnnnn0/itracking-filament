<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateAcquired = fake()->date();

        return [
            'personnel_id' => fake()->numberBetween(1, 100),
            'accounting_officer_id' => fake()->numberBetween(1, 100),
            'organization_unit_id' => fake()->numberBetween(1, 5),
            'operating_unit_project_id' => fake()->numberBetween(1, 5),
            'fund_id' => fake()->numberBetween(1, 5),
            'personal_protective_equipment_id' => fake()->numberBetween(1, 5),
            'property_number' => fake()->unique()->numerify('PN#####'),
            'quantity' => fake()->numberBetween(1, 100),
            'quantity_borrowed' => 0,
            'unit' => fake()->randomElement(['pcs', 'unit', 'pack']),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'date_acquired' => $dateAcquired,
            'estimated_useful_time' => fake()->date('Y-m'),
            'unit_price' => fake()->randomFloat(2, 10, 1000),
            'total_amount' => fake()->randomFloat(2, 10, 1000),
            'status' => 'active',
        ];
    }
}
