<?php

namespace Tests\Feature;

use App\Models\Appointment;
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
        $doctor = Doctor::factory()->create();

        // Act — submit the booking form as that admin
        $response = $this->actingAs($admin)->post(route('appointments.store'), [
            'patient_id' => $patient->patient_id,
            'doctor_id' => $doctor->doctor_id,
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
            'status' => 'scheduled',
            'reason' => 'Checkup',
        ]);

        // Assert — it worked AND the data actually exists
        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->patient_id,
            'doctor_id' => $doctor->doctor_id,
        ]);
    }

    public function test_double_booking_same_doctor_is_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::create(['name' => 'admin']));
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();

        $slot = now()->addDay()->setTime(15, 0);

        // First booking takes the slot
        Appointment::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $doctor->doctor_id,
            'scheduled_at' => $slot,
            'status' => 'scheduled',
        ]);

        // Second booking, same doctor, 15 minutes later — inside the 30-min window
        $response = $this->actingAs($admin)->post(route('appointments.store'), [
            'patient_id' => Patient::factory()->create()->patient_id,
            'doctor_id' => $doctor->doctor_id,
            'scheduled_at' => $slot->copy()->addMinutes(15)->format('Y-m-d H:i'),
            'status' => 'scheduled',
        ]);

        // Rejected with an error on scheduled_at, and only ONE appointment exists
        $response->assertSessionHasErrors('scheduled_at');
        $this->assertSame(1, Appointment::count());
    }

    public function test_patient_cannot_edit_another_patients_appointment(): void
    {
        $patientRole = Role::create(['name' => 'patient']);

        // Patient A owns the appointment
        $ownerUser = User::factory()->create();
        $ownerUser->roles()->attach($patientRole);
        $owner = Patient::factory()->create(['user_id' => $ownerUser->id]);

        // Patient B is the intruder
        $intruderUser = User::factory()->create();
        $intruderUser->roles()->attach($patientRole);
        Patient::factory()->create(['user_id' => $intruderUser->id]);

        $appointment = Appointment::create([
            'patient_id' => $owner->patient_id,
            'doctor_id' => Doctor::factory()->create()->doctor_id,
            'scheduled_at' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($intruderUser)
            ->get(route('appointments.edit', $appointment))
            ->assertForbidden();   // 403
    }

    public function test_doctor_identity_is_overridden_on_booking(): void
    {
        $doctorRole = Role::create(['name' => 'doctor']);
        $user = User::factory()->create();
        $user->roles()->attach($doctorRole);
        $ownDoctor = Doctor::factory()->create(['user_id' => $user->id]);
        $otherDoctor = Doctor::factory()->create();   // the forged identity

        $this->actingAs($user)->post(route('appointments.store'), [
            'patient_id' => Patient::factory()->create()->patient_id,
            'doctor_id' => $otherDoctor->doctor_id,   // ← lying to the server
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
            'status' => 'scheduled',
        ]);

        // The lie was discarded; the appointment belongs to the REAL doctor
        $this->assertDatabaseHas('appointments', ['doctor_id' => $ownDoctor->doctor_id]);
        $this->assertDatabaseMissing('appointments', ['doctor_id' => $otherDoctor->doctor_id]);
    }
}
