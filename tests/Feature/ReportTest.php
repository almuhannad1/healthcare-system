<?php

namespace Tests\Feature;

use App\Models\Medication;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }

    public function test_admin_can_see_the_dashboard(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Clinic dashboard');
    }

    public function test_a_patient_is_forbidden(): void
    {
        $this->actingAs($this->userWithRole('patient'))
            ->get(route('reports.index'))
            ->assertForbidden();
    }

    public function test_a_doctor_is_forbidden(): void
    {
        $this->actingAs($this->userWithRole('doctor'))
            ->get(route('reports.index'))
            ->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('reports.index'))->assertRedirect(route('login'));
    }

    public function test_low_stock_compares_each_medication_against_its_own_threshold(): void
    {
        // Below its own threshold — belongs on the alert list.
        Medication::factory()->create([
            'name' => 'Amoxicillin',
            'stock_quantity' => 5,
            'low_stock_threshold' => 20,
        ]);

        // Higher stock than the one above, but a low threshold — NOT an alert.
        // This is what whereColumn buys over a fixed value comparison.
        Medication::factory()->create([
            'name' => 'Ibuprofen',
            'stock_quantity' => 8,
            'low_stock_threshold' => 3,
        ]);

        $this->actingAs($this->userWithRole('admin'))
            ->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Amoxicillin')
            ->assertDontSee('Ibuprofen')
            ->assertViewHas('lowStock', fn ($lowStock) => $lowStock->count() === 1
                && $lowStock->first()->name === 'Amoxicillin');
    }
}
