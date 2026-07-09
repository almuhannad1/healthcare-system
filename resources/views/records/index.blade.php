<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Medical history</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $patient->first_name }} {{ $patient->last_name }}</p>
            </div>
            <a href="{{ route('patients.records.create', $patient) }}"
                class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                Add record
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div
                    class="rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-800 ring-1 ring-inset ring-green-600/20">
                    {{ session('success') }}
                </div>
            @endif

            @forelse ($records as $record)
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex items-center justify-between">
                        <p class="text-lg font-semibold text-gray-900">{{ $record->diagnosis }}</p>
                        <span class="text-sm text-gray-500">
                            {{ \Illuminate\Support\Carbon::parse($record->visit_date)->format('M j, Y') }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $record->doctor ? 'Dr. ' . $record->doctor->first_name . ' ' . $record->doctor->last_name : 'Doctor removed' }}
                    </p>
                    <p class="mt-3 text-sm text-gray-700">{{ $record->notes ?: '—' }}</p>

                    @if ($record->attachment_path)
                        <a href="{{ \Storage::url($record->attachment_path) }}" target="_blank"
                            class="mt-3 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            View attachment
                        </a>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-gray-900/5">
                    <p class="text-sm text-gray-500">No records yet for this patient.</p>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
