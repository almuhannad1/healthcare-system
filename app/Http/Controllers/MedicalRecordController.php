<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Patient $patient)
    {
        $records = $patient->medicalRecords()
            ->with('doctor')
            ->latest('visit_date')
            ->get();

        return view('records.index', compact('patient', 'records'));
    }

    public function create(Patient $patient)
    {
        $doctors = Doctor::orderBy('first_name')->get();

        return view('records.create', compact('patient', 'doctors'));
    }

    public function store(Request $request, Patient $patient)
    {
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
}
