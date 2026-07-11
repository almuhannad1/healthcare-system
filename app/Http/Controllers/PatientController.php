<?php

namespace App\Http\Controllers;

use App\Models\Patient;

class PatientController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('patient')) {
            abort(403);
        }
        $patients = Patient::orderBy('first_name')
            ->withCount('medicalRecords')
            ->get();

        return view('patients.index', compact('patients'));
    }
}
