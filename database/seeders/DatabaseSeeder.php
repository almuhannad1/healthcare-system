<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Patient::factory(100)->create();
        Doctor::factory(50)->create();
        Appointment::factory(200)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        foreach (['patient', 'doctor', 'pharmacist', 'admin'] as $name) {
            Role::firstOrCreate(['name' => $name]);
        }

        // We add role setup and link a couple of users, placed after the existing factory calls so the records exist to link to.
        Patient::factory(100)->create();
        Doctor::factory(50)->create();
        Appointment::factory(200)->create();

        // 1. Make sure all four roles exist.
        foreach (['patient', 'doctor', 'pharmacist', 'admin'] as $name) {
            // firstOrCreate - makes it safe to run repeatedly. If a role already exists it's reused, not duplicated. This is why re-seeding never creates four extra admin rows.
            Role::firstOrCreate(['name' => $name]);
        }

        // 2. An admin/reception user — sees everyone.
        $admin = User::factory()->create([
            'name' => 'Reception Admin',
            'email' => 'admin@example.com',
        ]);
        // syncWithoutDetaching - same as tinker: attach the role without disturbing others. Fits many-to-many.
        $admin->roles()->syncWithoutDetaching(
            [Role::where('name', 'admin')->first()->id]
        );

        // 3. A doctor user — linked to a real doctor record.
        $doctorUser = User::factory()->create([
            'name' => 'Doctor User',
            'email' => 'doctor@example.com',
        ]);
        $doctorUser->roles()->syncWithoutDetaching(
            [Role::where('name', 'doctor')->first()->id]
        );
        Doctor::first()->update(['user_id' => $doctorUser->id]);

        // 4. A patient user — linked to a real patient record.
        $patientUser = User::factory()->create([
            'name' => 'Patient User',
            'email' => 'patient@example.com',
        ]);
        $patientUser->roles()->syncWithoutDetaching(
            [Role::where('name', 'patient')->first()->id]
        );
        Patient::first()->update(['user_id' => $patientUser->id]);
    }
}
