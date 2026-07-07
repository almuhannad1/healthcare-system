<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Appointments</h2>
                <p class="mt-1 text-sm text-gray-500">Manage and review all scheduled visits.</p>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">
                    {{ $appointments->count() }} {{ Str::plural('appointment', $appointments->count()) }}
                </span>
                <a href="{{ route('appointments.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New appointment
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        confirmOpen: false,
        action: '',
        name: '',
        open(action, name) { this.action = action;
            this.name = name;
            this.confirmOpen = true; },
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div
                    class="mb-6 flex items-center gap-2 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-800 ring-1 ring-inset ring-green-600/20">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

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
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Edit</th>
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
                                <tr class="transition-colors hover:bg-indigo-50/40">
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
                                        <div class="text-sm text-gray-500">{{ $appointment->doctor->specialty }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 max-w-xs truncate">
                                            {{ $appointment->reason ?: '—' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium capitalize ring-1 ring-inset {{ $badge }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ $appointment->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('appointments.edit', $appointment) }}"
                                            class="inline-flex items-center rounded-md px-2.5 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16">
                                        <div class="flex flex-col items-center justify-center text-center">
                                            <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            <h3 class="mt-3 text-sm font-medium text-gray-900">No appointments yet</h3>
                                            <p class="mt-1 text-sm text-gray-500">Scheduled appointments will appear
                                                here.</p>
                                            <a href="{{ route('appointments.create') }}"
                                                class="mt-4 inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>
                                                New appointment
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Cancel confirmation modal --}}
        <div x-cloak x-show="confirmOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="cancel-title"
            role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="confirmOpen" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                x-on:click="confirmOpen = false" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

            {{-- Panel --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="confirmOpen" x-on:keydown.escape.window="confirmOpen = false"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                    class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <span
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                            </span>
                            <div class="flex-1">
                                <h3 id="cancel-title" class="text-base font-semibold text-gray-900">
                                    Cancel appointment
                                </h3>
                                <p class="mt-1.5 text-sm text-gray-500">
                                    Are you sure you want to cancel
                                    <span class="font-medium text-gray-700" x-text="name"></span>'s appointment?
                                    This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
                        <button type="button" x-on:click="confirmOpen = false"
                            class="inline-flex items-center rounded-md bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            Keep it
                        </button>
                        <form method="POST" x-bind:action="action">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center rounded-md bg-red-600 px-3.5 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-500">
                                Yes, cancel it
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
