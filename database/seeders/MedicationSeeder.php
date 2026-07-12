<?php

namespace Database\Seeders;

use App\Models\Medication;
use Illuminate\Database\Seeder;

class MedicationSeeder extends Seeder
{
    /**
     * Seed the pharmacy medications.
     */
    public function run(): void
    {
        Medication::factory(30)->create();
    }
}
