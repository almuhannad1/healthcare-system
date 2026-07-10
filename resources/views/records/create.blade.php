<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Add medical record</h2>
        <p class="mt-1 text-sm text-gray-500">{{ $patient->first_name }} {{ $patient->last_name }}</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6 sm:p-8">

                <form method="POST" action="{{ route('patients.records.store', $patient) }}" enctype="multipart/form-data"
                    class="space-y-6"> @csrf

                    {{-- Doctor: locked for doctors, dropdown for others --}}
                    <div>
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                        @if ($lockedDoctor)
                            <div
                                class="mt-1 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-900 ring-1 ring-gray-200">
                                Dr. {{ $lockedDoctor->first_name }} {{ $lockedDoctor->last_name }} —
                                {{ $lockedDoctor->specialty }}
                            </div>
                            {{-- no input — the server fills doctor_id --}}
                        @else
                            <select name="doctor_id" id="doctor_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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

                    <div>
                        <label for="visit_date" class="block text-sm font-medium text-gray-700">Visit date</label>
                        <input type="date" name="visit_date" id="visit_date"
                            value="{{ old('visit_date', now()->format('Y-m-d')) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('visit_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis</label>
                        <input type="text" name="diagnosis" id="diagnosis" value="{{ old('diagnosis') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('diagnosis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes <span
                                class="text-gray-400">(optional)</span></label>
                        <textarea name="notes" id="notes" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('patients.records.index', $patient) }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</a>
                        <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                            Save record
                        </button>
                    </div>

                    <div>
                        <label for="attachment" class="block text-sm font-medium text-gray-700">Attachment <span
                                class="text-gray-400">(optional — PDF or image)</span></label>
                        <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png"
                            class="mt-1 block w-full text-sm text-gray-600">
                        @error('attachment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
