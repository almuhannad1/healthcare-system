<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\Patient;
use Illuminate\Http\Request;

class DispenseController extends Controller
{
    public function create(Medication $medication)
    {
        $patients = Patient::orderBy('first_name')->get();

        return view('dispenses.create', compact('medication', 'patients'));
    }

    public function store(Request $request, Medication $medication)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,patient_id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $ok = $medication->dispense(
            $validated['quantity'],
            $validated['patient_id'],
            auth()->id(),
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
