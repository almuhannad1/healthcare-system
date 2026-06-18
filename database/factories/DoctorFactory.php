<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'specialty' => fake()->randomElement(['Cardiology', 'Dermatology', 'Neurology', 'Pediatrics', 'General Practice', 'Orthopedics', 'Psychiatry', 'Oncology', 'Dentistry', 'Ophthalmology']),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
