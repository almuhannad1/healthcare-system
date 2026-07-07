<?php

namespace App\Http\Requests;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Appointment;
use Illuminate\Support\Carbon;

class UpdateAppointmentRequest extends FormRequest
{
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

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

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
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $start = Carbon::parse($this->scheduled_at);
            $windowStart = $start->copy()->subMinutes(30);
            $windowEnd   = $start->copy()->addMinutes(30);

            $clash = Appointment::where('doctor_id', $this->doctor_id)
                ->whereBetween('scheduled_at', [$windowStart, $windowEnd])
                ->where('appointment_id', '!=', $this->route('appointment')->appointment_id)  // ← THE FIX
                ->exists();

            if ($clash) {
                $validator->errors()->add(
                    'scheduled_at',
                    'This doctor already has another appointment within 30 minutes of that time.'
                );
            }
        });
    }
}