<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use AuthorizesRequests;
    /*
    create/edit are the "show a form" pair, and store/update are the "process a form" pair. Edit and create are cousins (both display forms); update and store are cousins (both save).
    */

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $query = Appointment::with('patient', 'doctor')->latest('scheduled_at');

        if ($user->hasRole('doctor') && $user->doctor) {
            $query->where('doctor_id', $user->doctor->doctor_id);
        } elseif ($user->hasRole('patient') && $user->patient) {
            $query->where('patient_id', $user->patient->patient_id);
        }
        // admin: no filter — sees all

        $appointments = $query->get();

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Appointment::class);
        
        $user = auth()->user();

        // Defaults: the admin/reception superset — see everyone.
        $patients = collect();
        $doctors = collect();
        $lockedPatient = null;   // if set, patient field is fixed to this record
        $lockedDoctor = null;   // if set, doctor field is fixed to this record

        if ($user->hasRole('admin')) {
            $patients = Patient::orderBy('first_name')->get();
            $doctors = Doctor::orderBy('first_name')->get();

        } elseif ($user->hasRole('doctor')) {
            // A doctor books patients into THEIR OWN schedule.
            $patients = Patient::orderBy('first_name')->get();
            $lockedDoctor = $user->doctor;   // doctor field locked to self

        } elseif ($user->hasRole('patient')) {
            // A patient books THEMSELVES with a doctor of their choice.
            $doctors = Doctor::orderBy('first_name')->get();
            $lockedPatient = $user->patient; // patient field locked to self

        } else {
            abort(403, 'You are not allowed to book appointments.');
        }

        return view('appointments.create', compact(
            'patients', 'doctors', 'lockedPatient', 'lockedDoctor'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $this->authorize('create', Appointment::class);

        Appointment::create($request->validated());

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment booked.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);

        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     * edit = show the form. update = save the form.
     * GET request.
     */
    public function edit(Appointment $appointment)
    {
        $this->authorize('create', Appointment::class);
        $user = auth()->user();
        $patients = collect();
        $doctors = collect();
        $lockedPatient = null;
        $lockedDoctor = null;

        if ($user->hasRole('admin')) {
            $patients = Patient::orderBy('first_name')->get();
            $doctors = Doctor::orderBy('first_name')->get();
        } elseif ($user->hasRole('doctor')) {
            $patients = Patient::orderBy('first_name')->get();
            $lockedDoctor = $user->doctor;
        } elseif ($user->hasRole('patient')) {
            $doctors = Doctor::orderBy('first_name')->get();
            $lockedPatient = $user->patient;
        } else {
            abort(403);
        }

        return view('appointments.edit', compact(
            'appointment', 'patients', 'doctors', 'lockedPatient', 'lockedDoctor'
        ));
    }

    /**
     * Update the specified resource in storage.
     * edit = show the form. update = save the form.
     * PUT request
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $appointment->update($request->validated());

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $this->authorize('update', $appointment);
        $appointment->update(['status' => 'canceled']);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment canceled.');
    }
}
