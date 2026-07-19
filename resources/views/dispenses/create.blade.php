<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Dispense medication</h2>
        <p class="mt-1 text-sm text-gray-500">Hand out stock to a patient and record it against the catalog.</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6 sm:p-8">

                <form method="POST" action="{{ route('medications.dispense.store', $medication) }}" class="space-y-6">
                    @csrf

                    {{-- Medication — fixed by the URL, shown as a locked field --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Medication</label>
                        <div
                            class="mt-1 flex items-center justify-between rounded-md border border-gray-200 bg-gray-50 px-3 py-2">
                            <span class="text-sm font-medium text-gray-900">
                                {{ $medication->name }}@if ($medication->strength)
                                    <span class="text-gray-400">· {{ $medication->strength }}</span>
                                @endif
                            </span>
                            <span class="text-xs text-gray-500">{{ $medication->stock_quantity }} in stock</span>
                        </div>
                    </div>

                    {{-- Patient --}}
                    <div>
                        <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
                        <select name="patient_id" id="patient_id"
                            class="tom-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select a patient…</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->patient_id }}" @selected(old('patient_id') == $patient->patient_id)>
                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" name="quantity" id="quantity" min="1" step="1"
                            value="{{ old('quantity', 1) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('medications.index') }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</a>
                        <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                            Dispense
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Tom Select: upgrades the plain <select> above into a searchable one --}}
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
