<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Appointments</h2>
                <p class="mt-1 text-sm text-gray-500">Manage and review all scheduled visits.</p>
            </div>
            <span
                class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">
                {{ $appointments->count() }} {{ Str::plural('appointment', $appointments->count()) }}
            </span>
            <a href="{{ route('appointments.create') }}"
                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                New appointment
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    When</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Patient</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Doctor</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Reason</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($appointments as $appointment)
                                @php
                                    $when = \Illuminate\Support\Carbon::parse($appointment->scheduled_at);
                                    $patientName = trim(
                                        "{$appointment->patient->first_name} {$appointment->patient->last_name}",
                                    );
                                    $doctorName = trim(
                                        "{$appointment->doctor->first_name} {$appointment->doctor->last_name}",
                                    );
                                    $statusStyles = [
                                        'scheduled' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                        'completed' => 'bg-green-50 text-green-700 ring-green-600/20',
                                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                                        'canceled' => 'bg-red-50 text-red-700 ring-red-600/20',
                                        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                    ];
                                    $badge =
                                        $statusStyles[strtolower($appointment->status)] ??
                                        'bg-gray-50 text-gray-600 ring-gray-500/20';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $when->format('M j, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $when->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                                                {{ strtoupper(substr($appointment->patient->first_name, 0, 1) . substr($appointment->patient->last_name, 0, 1)) }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-900">{{ $patientName }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Dr. {{ $doctorName }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 max-w-xs truncate">
                                            {{ $appointment->reason ?: '—' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize ring-1 ring-inset {{ $badge }}">
                                            {{ $appointment->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16">
                                        <div class="flex flex-col items-center justify-center text-center">
                                            <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            <h3 class="mt-3 text-sm font-medium text-gray-900">No appointments yet</h3>
                                            <p class="mt-1 text-sm text-gray-500">Scheduled appointments will appear
                                                here.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
