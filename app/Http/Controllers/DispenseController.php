<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Medication;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DispenseController extends Controller
{
    public function create(Medication $medication)
    {
        $patients = Patient::orderBy('first_name')->get();

        // Every appointment the form might offer, filtered down to the
        // chosen patient's in the browser.
        $appointments = Appointment::orderByDesc('scheduled_at')->get();

        return view('dispenses.create', compact('medication', 'patients', 'appointments'));
    }

    public function store(Request $request, Medication $medication)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,patient_id'],
            'quantity' => ['required', 'integer', 'min:1'],
            // Must be one of *this* patient's appointments — otherwise a
            // hand-crafted request could bill a visit to the wrong person.
            'appointment_id' => [
                'nullable',
                'integer',
                Rule::exists('appointments', 'appointment_id')
                    ->where('patient_id', $request->input('patient_id')),
            ],
        ]);

        $ok = $medication->dispense(
            $validated['quantity'],
            $validated['patient_id'],
            auth()->id(),
            $validated['appointment_id'] ?? null,
        );

        if (! $ok) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => "Not enough stock — only {$medication->fresh()->stock_quantity} left."]);
        }

        return redirect()
            ->route('medications.index')
            ->with('success', "Dispensed {$validated['quantity']} × {$medication->name}.");
    }
}
