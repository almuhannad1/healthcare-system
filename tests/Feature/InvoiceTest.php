<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin']));

        return $admin;
    }

    private function appointment(?int $feeCents = null): Appointment
    {
        return Appointment::factory()->create([
            'doctor_id' => Doctor::factory()->create(['consultation_fee_cents' => $feeCents])->doctor_id,
            'patient_id' => Patient::factory()->create()->patient_id,
        ]);
    }

    public function test_invoice_bills_the_consultation_fee_plus_dispensed_medication(): void
    {
        $appointment = $this->appointment(feeCents: 7500);
        $medication = Medication::factory()->create([
            'stock_quantity' => 10,
            'price_cents' => 1250,
        ]);

        $medication->dispense(2, $appointment->patient_id, $this->admin()->id, $appointment->appointment_id);

        $invoice = Invoice::generateForAppointment($appointment);

        // 7500 consultation + (2 x 1250) medication
        $this->assertSame(10000, $invoice->total_cents);
        $this->assertSame('100.00', $invoice->totalDollars());
        $this->assertCount(2, $invoice->items);
        $this->assertSame('unpaid', $invoice->status);
    }

    public function test_doctor_without_a_fee_falls_back_to_the_clinic_default(): void
    {
        config(['billing.consultation_fee_cents' => 4200]);

        $invoice = Invoice::generateForAppointment($this->appointment(feeCents: null));

        $this->assertSame(4200, $invoice->total_cents);
    }

    public function test_prices_are_snapshotted_so_later_catalog_changes_do_not_alter_the_invoice(): void
    {
        $appointment = $this->appointment(feeCents: 5000);
        $medication = Medication::factory()->create(['stock_quantity' => 10, 'price_cents' => 1000]);
        $medication->dispense(1, $appointment->patient_id, $this->admin()->id, $appointment->appointment_id);

        $invoice = Invoice::generateForAppointment($appointment);

        $medication->update(['price_cents' => 9999]);

        $this->assertSame(6000, $invoice->fresh()->total_cents);
        $this->assertSame(1000, $invoice->items()->where('description', 'like', '%'.$medication->name.'%')->first()->unit_price_cents);
    }

    public function test_a_visit_is_billed_only_once(): void
    {
        $appointment = $this->appointment(feeCents: 5000);

        $first = Invoice::generateForAppointment($appointment);
        $second = Invoice::generateForAppointment($appointment);

        $this->assertSame($first->invoice_id, $second->invoice_id);
        $this->assertSame(1, Invoice::where('appointment_id', $appointment->appointment_id)->count());
    }

    public function test_dispenses_from_other_visits_are_not_billed_to_this_one(): void
    {
        $appointment = $this->appointment(feeCents: 5000);
        $medication = Medication::factory()->create(['stock_quantity' => 10, 'price_cents' => 1000]);

        // Same patient, but handed out with no appointment attached.
        $medication->dispense(3, $appointment->patient_id, $this->admin()->id, null);

        $invoice = Invoice::generateForAppointment($appointment);

        $this->assertSame(5000, $invoice->total_cents);
        $this->assertCount(1, $invoice->items);
    }

    public function test_admin_can_download_the_invoice_pdf(): void
    {
        $invoice = Invoice::generateForAppointment($this->appointment(feeCents: 5000));

        $response = $this->actingAs($this->admin())->get(route('invoices.pdf', $invoice));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_a_patient_cannot_download_someone_elses_invoice(): void
    {
        $invoice = Invoice::generateForAppointment($this->appointment(feeCents: 5000));

        $intruder = User::factory()->create();
        $intruder->roles()->attach(Role::firstOrCreate(['name' => 'patient']));
        Patient::factory()->create(['user_id' => $intruder->id]);

        $this->actingAs($intruder)
            ->get(route('invoices.pdf', $invoice))
            ->assertForbidden();
    }

    public function test_admin_can_mark_an_invoice_paid(): void
    {
        $invoice = Invoice::generateForAppointment($this->appointment(feeCents: 5000));

        $this->actingAs($this->admin())
            ->patch(route('invoices.paid', $invoice))
            ->assertRedirect();

        $this->assertSame('paid', $invoice->fresh()->status);
    }

    public function test_a_patient_cannot_mark_their_own_invoice_paid(): void
    {
        $appointment = $this->appointment(feeCents: 5000);
        $invoice = Invoice::generateForAppointment($appointment);

        $patientUser = User::factory()->create();
        $patientUser->roles()->attach(Role::firstOrCreate(['name' => 'patient']));
        Patient::where('patient_id', $appointment->patient_id)->update(['user_id' => $patientUser->id]);

        $this->actingAs($patientUser)
            ->patch(route('invoices.paid', $invoice))
            ->assertForbidden();

        $this->assertSame('unpaid', $invoice->fresh()->status);
    }
}
