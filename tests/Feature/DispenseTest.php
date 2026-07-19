<?php

namespace Tests\Feature;

use App\Models\Dispense;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DispenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispense_refuses_when_stock_is_insufficient(): void
    {
        $medication = Medication::factory()->create(['stock_quantity' => 1]);
        $patient = Patient::factory()->create();
        $user = User::factory()->create();

        $first = $medication->dispense(1, $patient->patient_id, $user->id);
        $second = $medication->dispense(1, $patient->patient_id, $user->id);

        $this->assertTrue($first);
        $this->assertFalse($second);
        $this->assertSame(0, $medication->fresh()->stock_quantity);
        $this->assertSame(1, Dispense::count());
    }
}
