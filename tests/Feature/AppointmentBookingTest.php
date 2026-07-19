<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class AppointmentBookingTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_admin_can_book_an_appointment(): void
    {
        // Arrange — build the world this test needs
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::create(['name' => 'admin']));
        $patient = Patient::factory()->create();
        $doctor  = Doctor::factory()->create();

        // Act — submit the booking form as that admin
        $response = $this->actingAs($admin)->post(route('appointments.store'), [
            'patient_id'   => $patient->patient_id,
            'doctor_id'    => $doctor->doctor_id,
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
            'status'       => 'scheduled',
            'reason'       => 'Checkup',
        ]);

        // Assert — it worked AND the data actually exists
        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->patient_id,
            'doctor_id'  => $doctor->doctor_id,
        ]);
    }
}
