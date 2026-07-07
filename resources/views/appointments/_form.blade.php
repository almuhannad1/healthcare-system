{{-- 
    Shared appointment form fields.
    Expects:
      $appointment    — an Appointment model (for edit) or null (for create)
      $patients, $doctors, $lockedPatient, $lockedDoctor — from the controller
--}}

{{-- Patient --}}
<div>
    <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
    @if ($lockedPatient)
        <div class="mt-1 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-900 ring-1 ring-gray-200">
            {{ $lockedPatient->first_name }} {{ $lockedPatient->last_name }}
        </div>
    @else
        <select name="patient_id" id="patient_id"
            class="tom-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select a patient…</option>
            @foreach ($patients as $patient)
                <option value="{{ $patient->patient_id }}" @selected(old('patient_id', $appointment?->patient_id) == $patient->patient_id)>
                    {{ $patient->first_name }} {{ $patient->last_name }}
                </option>
            @endforeach
        </select>
    @endif
    @error('patient_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Doctor --}}
<div>
    <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
    @if ($lockedDoctor)
        <div class="mt-1 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-900 ring-1 ring-gray-200">
            Dr. {{ $lockedDoctor->first_name }} {{ $lockedDoctor->last_name }} — {{ $lockedDoctor->specialty }}
        </div>
    @else
        <select name="doctor_id" id="doctor_id"
            class="tom-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select a doctor…</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->doctor_id }}" @selected(old('doctor_id', $appointment?->doctor_id) == $doctor->doctor_id)>
                    Dr. {{ $doctor->first_name }} {{ $doctor->last_name }} — {{ $doctor->specialty }}
                </option>
            @endforeach
        </select>
    @endif
    @error('doctor_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Scheduled at --}}
<div>
    <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Date &amp; time</label>
    <input type="datetime-local" name="scheduled_at" id="scheduled_at"
        value="{{ old('scheduled_at', $appointment ? \Illuminate\Support\Carbon::parse($appointment->scheduled_at)->format('Y-m-d\TH:i') : '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    @error('scheduled_at')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Status --}}
<div>
    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
    <select name="status" id="status"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @foreach (['scheduled', 'completed', 'canceled'] as $status)
            <option value="{{ $status }}" @selected(old('status', $appointment?->status ?? 'scheduled') == $status)>
                {{ ucfirst($status) }}
            </option>
        @endforeach
    </select>
    @error('status')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Reason --}}
<div>
    <label for="reason" class="block text-sm font-medium text-gray-700">Reason <span
            class="text-gray-400">(optional)</span></label>
    <textarea name="reason" id="reason" rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('reason', $appointment?->reason) }}</textarea>
    @error('reason')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
