<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => \App\Models\Patient::inRandomOrder()->first()->patient_id,
            'doctor_id' => \App\Models\Doctor::inRandomOrder()->first()->doctor_id,
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+2 month'),
            'status' => $this->faker->randomElement(['scheduled', 'completed', 'canceled']),
            'reason' => $this->faker->optional()->sentence(),
        ];
    }
}
