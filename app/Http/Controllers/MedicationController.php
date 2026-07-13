<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicationRequest;
use App\Http\Requests\UpdateMedicationRequest;
use App\Models\Medication;

class MedicationController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Medication::class);

        $medications = Medication::orderBy('name')->get();

        return view('medications.index', compact('medications'));
    }

    /**
     * Show the form for creating a new medication.
     */
    public function create()
    {
        $this->authorize('create', Medication::class);

        return view('medications.create');
    }

    /**
     * Store a newly created medication.
     */
    public function store(StoreMedicationRequest $request)
    {
        Medication::create($request->validated());

        return redirect()
            ->route('medications.index')
            ->with('success', 'Medication added to the catalog.');
    }

    /**
     * Show the form for editing the given medication.
     */
    public function edit(Medication $medication)
    {
        $this->authorize('update', $medication);

        return view('medications.edit', compact('medication'));
    }

    /**
     * Update the given medication.
     */
    public function update(UpdateMedicationRequest $request, Medication $medication)
    {
        $medication->update($request->validated());

        return redirect()
            ->route('medications.index')
            ->with('success', 'Medication updated.');
    }
}
