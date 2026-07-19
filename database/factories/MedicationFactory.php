<?php

namespace Database\Factories;

use App\Models\Medication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medication>
 */
class MedicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Paracetamol', 'Ibuprofen', 'Amoxicillin', 'Omeprazole',
                'Metformin', 'Atorvastatin', 'Cetirizine', 'Azithromycin',
            ]),
            'strength' => fake()->randomElement(['250mg', '500mg', '850mg', '20mg', '10mg']),
            'stock_quantity' => fake()->numberBetween(0, 200),
            'low_stock_threshold' => 15,
            'price_cents' => fake()->numberBetween(100, 5000),
        ];
    }
}
