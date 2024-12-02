<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Personnel>
 */
class PersonnelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => fake()->numberBetween(1, 5),
            'office_id' => fake()->numberBetween(1, 5),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'office_phone' => '09' . fake()->numberBetween(000000000, 999999999),
            'office_email' => fake()->unique()->safeEmail(),
            'start_date' => fake()->date(),
            'end_date' => fake()->optional()->date(),
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
