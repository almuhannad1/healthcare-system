<?php

namespace App\Http\Requests;

use Illuminate\Validation\Validator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Appointment;  
use Illuminate\Support\Carbon;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Runs BEFORE authorize() and rules().
     * Injects the locked identity fields from the authenticated user,
     * so the browser can never dictate who the appointment is for.
     */
    protected function prepareForValidation(): void
    {
        $user = $this->user();

        if ($user->hasRole('doctor') && $user->doctor) {
            $this->merge(['doctor_id' => $user->doctor->doctor_id]);
        }
        if ($user->hasRole('patient') && $user->patient) {
            $this->merge(['patient_id' => $user->patient->patient_id]);
        }
    }
    
    /**
     * Determine if the user is authorized to make this request.
     * It answers a different question than validation: not "is the data valid?" but "is this person even allowed to attempt this action?"
     */
    public function authorize(): bool
    {
        // Can the current user create an appointment at all?
        // For now: any logged-in user. We'll tighten this later
        // (e.g. only doctors/admins) once roles drive the UI.
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,patient_id'],
            'doctor_id' => ['required', 'integer', 'exists:doctors,doctor_id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['scheduled', 'completed', 'canceled'])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Only bother checking if the basic fields even passed.
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            // The proposed appointment's start time, as a real date object.
            $start = Carbon::parse($this->scheduled_at);

            // Define the window: nothing for this doctor within 30 min on either side.
            $windowStart = $start->copy()->subMinutes(30);
            $windowEnd   = $start->copy()->addMinutes(30);

            $clash = Appointment::where('doctor_id', $this->doctor_id)
                ->whereBetween('scheduled_at', [$windowStart, $windowEnd])
                ->exists();

            if ($clash) {
                $validator->errors()->add(
                    'scheduled_at',
                    'This doctor already has an appointment within 30 minutes of that time.'
                );
            }
        });
    }
}
