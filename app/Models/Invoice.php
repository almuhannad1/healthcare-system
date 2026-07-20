<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;

    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'total_cents',
        'status',
    ];

    /**
     * Build the invoice for a visit: the doctor's consultation fee, plus a
     * line for every medication dispensed against that appointment.
     *
     * Prices are copied onto the items rather than referenced, so the invoice
     * keeps saying what was charged even after the catalog changes.
     *
     * Returns the existing invoice if the appointment already has one — a
     * visit is billed once.
     */
    public static function generateForAppointment(Appointment $appointment): self
    {
        return DB::transaction(function () use ($appointment) {
            if ($existing = $appointment->invoice()->first()) {
                return $existing;
            }

            $invoice = static::create([
                'appointment_id' => $appointment->appointment_id,
                'patient_id' => $appointment->patient_id,
                'total_cents' => 0,
                'status' => 'unpaid',
            ]);

            $feeCents = $appointment->doctor->consultationFeeCents();

            $invoice->items()->create([
                'description' => 'Consultation',
                'quantity' => 1,
                'unit_price_cents' => $feeCents,
                'line_total_cents' => $feeCents,
            ]);

            $total = $feeCents;

            foreach ($appointment->dispenses()->with('medication')->get() as $dispense) {
                $unitPrice = $dispense->medication->price_cents;
                $lineTotal = $unitPrice * $dispense->quantity;

                $invoice->items()->create([
                    'description' => trim($dispense->medication->name.' '.$dispense->medication->strength),
                    'quantity' => $dispense->quantity,
                    'unit_price_cents' => $unitPrice,
                    'line_total_cents' => $lineTotal,
                ]);

                $total += $lineTotal;
            }

            $invoice->update(['total_cents' => $total]);

            return $invoice;
        });
    }

    public function totalDollars(): string
    {
        return number_format($this->total_cents / 100, 2);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'invoice_id');
    }
}
