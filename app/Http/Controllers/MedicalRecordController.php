<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    use AuthorizesRequests;

    public function index(Patient $patient)
    {
        $this->authorize('viewAny', [MedicalRecord::class, $patient]);

        $records = $patient->medicalRecords()->with('doctor')->latest('visit_date')->get();

        return view('records.index', compact('patient', 'records'));
    }

    public function create(Patient $patient)
    {
        $this->authorize('create', [MedicalRecord::class, $patient]);

        $user = auth()->user();
        $doctors = collect();
        $lockedDoctor = null;

        if ($user->hasRole('doctor') && $user->doctor) {
            $lockedDoctor = $user->doctor;      // doctor writes records as themselves
        } else {
            $doctors = Doctor::orderBy('first_name')->get();
        }

        return view('records.create', compact('patient', 'doctors', 'lockedDoctor'));
    }

    public function store(Request $request, Patient $patient)
    {
        $this->authorize('create', [MedicalRecord::class, $patient]);

        $user = auth()->user();

        // Security: a doctor's identity comes from the server, never the browser.
        if ($user->hasRole('doctor') && $user->doctor) {
            $request->merge(['doctor_id' => $user->doctor->doctor_id]);
        }

        $validated = $request->validate([
            'doctor_id' => ['required', 'integer', 'exists:doctors,doctor_id'],
            'visit_date' => ['required', 'date'],
            'diagnosis' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')
                ->store("records/{$patient->patient_id}", 'public');
        }

        $patient->medicalRecords()->create($validated);

        return redirect()
            ->route('patients.records.index', $patient)
            ->with('success', 'Medical record added.');
    }

    public function edit(Patient $patient, MedicalRecord $record)
    {
        $this->authorize('update', $record);

        $user = auth()->user();
        $doctors = collect();
        $lockedDoctor = null;

        if ($user->hasRole('doctor') && $user->doctor) {
            $lockedDoctor = $user->doctor;
        } else {
            $doctors = Doctor::orderBy('first_name')->get();
        }

        return view('records.edit', compact('patient', 'record', 'doctors', 'lockedDoctor'));
    }

    public function update(Request $request, Patient $patient, MedicalRecord $record)
    {
        $this->authorize('update', $record);

        $user = auth()->user();

        if ($user->hasRole('doctor') && $user->doctor) {
            $request->merge(['doctor_id' => $user->doctor->doctor_id]);
        }

        $validated = $request->validate([
            'doctor_id' => ['required', 'integer', 'exists:doctors,doctor_id'],
            'visit_date' => ['required', 'date'],
            'diagnosis' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $record->update($validated);

        return redirect()
            ->route('patients.records.index', $patient)
            ->with('success', 'Medical record updated.');
    }
}
