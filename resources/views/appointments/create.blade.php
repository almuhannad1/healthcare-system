<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Book an appointment</h2>
        <p class="mt-1 text-sm text-gray-500">Schedule a visit between a patient and a doctor.</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6 sm:p-8">

                <form method="POST" action="{{ route('appointments.store') }}" class="space-y-6">
                    @csrf

                    {{-- Patient --}}
                    {{-- Patient: dropdown for admin/doctor, locked for patient --}}
                    <div>
                        <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>

                        @if ($lockedPatient)
                            {{-- Patient user: their record is fixed. Show it, don't let them change it. --}}
                            <div
                                class="mt-1 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-900 ring-1 ring-gray-200">
                                {{ $lockedPatient->first_name }} {{ $lockedPatient->last_name }}
                            </div>
                            {{-- NOTE: no hidden input here on purpose — the server fills patient_id (Stage C). --}}
                        @else
                            <select name="patient_id" id="patient_id"
                                class="tom-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a patient…</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->patient_id }}" @selected(old('patient_id') == $patient->patient_id)>
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
                    {{-- Doctor: dropdown for admin/patient, locked for doctor --}}
                    <div>
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>

                        @if ($lockedDoctor)
                            <div
                                class="mt-1 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-900 ring-1 ring-gray-200">
                                Dr. {{ $lockedDoctor->first_name }} {{ $lockedDoctor->last_name }} —
                                {{ $lockedDoctor->specialty }}
                            </div>
                        @else
                            <select name="doctor_id" id="doctor_id"
                                class="tom-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a doctor…</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->doctor_id }}" @selected(old('doctor_id') == $doctor->doctor_id)>
                                        Dr. {{ $doctor->first_name }} {{ $doctor->last_name }} —
                                        {{ $doctor->specialty }}
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
                        <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Date &amp;
                            time</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                            value="{{ old('scheduled_at') }}"
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
                                <option value="{{ $status }}" @selected(old('status', 'scheduled') == $status)>
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
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('appointments.index') }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</a>
                        <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Book appointment
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Tom Select: upgrades the plain <select>s above into searchable ones --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.querySelectorAll('.tom-select').forEach((el) => {
            new TomSelect(el, {
                create: false,
                allowEmptyOption: true
            });
        });
    </script>
</x-app-layout>
