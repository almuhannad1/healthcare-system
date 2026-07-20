<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Medication extends Model
{
    use HasFactory;

    protected $primaryKey = 'medication_id';

    protected $fillable = [
        'name', 'strength', 'stock_quantity', 'low_stock_threshold', 'price_cents',
    ];

    // a tiny business-logic method living on the model.
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    /**
     * Atomically dispense stock. Returns true if dispensed,
     * false if there wasn't enough stock. Race-safe.
     *
     * $appointmentId ties the dispense to a visit so it can be invoiced;
     * null means stock handed out outside an appointment, which is
     * recorded but never billed.
     */
    public function dispense(int $quantity, int $patientId, int $userId, ?int $appointmentId = null): bool
    {
        return DB::transaction(function () use ($quantity, $patientId, $userId, $appointmentId) {
            $affected = static::where('medication_id', $this->medication_id)
                ->where('stock_quantity', '>=', $quantity)
                ->decrement('stock_quantity', $quantity);

            if ($affected === 0) {
                return false;   // not enough stock — nothing changed
            }

            Dispense::create([
                'medication_id' => $this->medication_id,
                'patient_id' => $patientId,
                'user_id' => $userId,
                'quantity' => $quantity,
                'appointment_id' => $appointmentId,
            ]);

            return true;
        });
    }
}
