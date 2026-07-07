<x-app-layout>
    @php
        $when = \Illuminate\Support\Carbon::parse($appointment->scheduled_at);

        $patientName = trim("{$appointment->patient->first_name} {$appointment->patient->last_name}");
        $doctorName = trim("{$appointment->doctor->first_name} {$appointment->doctor->last_name}");

        $patientInitials = strtoupper(
            substr($appointment->patient->first_name, 0, 1) . substr($appointment->patient->last_name, 0, 1),
        );
        $doctorInitials = strtoupper(
            substr($appointment->doctor->first_name, 0, 1) . substr($appointment->doctor->last_name, 0, 1),
        );

        $statusStyles = [
            'scheduled' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            'completed' => 'bg-green-50 text-green-700 ring-green-600/20',
            'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
            'canceled' => 'bg-red-50 text-red-700 ring-red-600/20',
            'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        ];
        $badge = $statusStyles[strtolower($appointment->status)] ?? 'bg-gray-50 text-gray-600 ring-gray-500/20';

        $dob = $appointment->patient->date_of_birth
            ? \Illuminate\Support\Carbon::parse($appointment->patient->date_of_birth)
            : null;
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Appointment details</h2>
                <p class="mt-1 text-sm text-gray-500">Reference #{{ $appointment->appointment_id }}</p>
            </div>
            <a href="{{ route('appointments.index') }}"
                class="inline-flex items-center gap-1.5 rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Hero: date, time & status --}}
            <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 shadow-sm">
                <div class="flex flex-col gap-6 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white ring-1 ring-inset ring-white/25">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-indigo-100">{{ $when->format('l') }}</p>
                            <p class="mt-0.5 text-2xl font-bold tracking-tight text-white">
                                {{ $when->format('M j, Y') }}</p>
                            <p class="mt-1 flex items-center gap-1.5 text-sm text-indigo-100">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ $when->format('g:i A') }}
                            </p>
                        </div>
                    </div>
                    <span
                        class="inline-flex w-fit items-center gap-1.5 rounded-full bg-white/95 px-3 py-1 text-sm font-medium capitalize ring-1 ring-inset {{ $badge }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        {{ $appointment->status }}
                    </span>
                </div>
            </div>

            {{-- Patient & Doctor cards --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- Patient --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex items-center gap-4">
                        <span
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-base font-semibold text-indigo-700">
                            {{ $patientInitials }}
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Patient</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $patientName }}</p>
                        </div>
                    </div>

                    <dl class="mt-6 space-y-4 border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sm text-gray-500">Email</dt>
                            <dd class="text-sm font-medium text-gray-900 text-right">
                                <a href="mailto:{{ $appointment->patient->email }}"
                                    class="hover:text-indigo-600">{{ $appointment->patient->email }}</a>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sm text-gray-500">Phone</dt>
                            <dd class="text-sm font-medium text-gray-900 text-right">
                                {{ $appointment->patient->phone ?: '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sm text-gray-500">Gender</dt>
                            <dd class="text-sm font-medium text-gray-900 capitalize text-right">
                                {{ $appointment->patient->gender ?: '—' }}</dd>
                        </div>
                        @if ($dob)
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-sm text-gray-500">Date of birth</dt>
                                <dd class="text-sm font-medium text-gray-900 text-right">
                                    {{ $dob->format('M j, Y') }}
                                    <span class="text-gray-400">({{ $dob->age }} yrs)</span>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Doctor --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex items-center gap-4">
                        <span
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-base font-semibold text-emerald-700">
                            {{ $doctorInitials }}
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Doctor</p>
                            <p class="text-lg font-semibold text-gray-900">Dr. {{ $doctorName }}</p>
                        </div>
                    </div>

                    <dl class="mt-6 space-y-4 border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sm text-gray-500">Specialty</dt>
                            <dd class="text-right">
                                <span
                                    class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                    {{ $appointment->doctor->specialty ?: '—' }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sm text-gray-500">Email</dt>
                            <dd class="text-sm font-medium text-gray-900 text-right">
                                <a href="mailto:{{ $appointment->doctor->email }}"
                                    class="hover:text-indigo-600">{{ $appointment->doctor->email }}</a>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sm text-gray-500">Phone</dt>
                            <dd class="text-sm font-medium text-gray-900 text-right">
                                {{ $appointment->doctor->phone ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Reason for visit --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-900">Reason for visit</h3>
                </div>
                <p class="mt-3 text-sm leading-relaxed text-gray-700">
                    {{ $appointment->reason ?: 'No reason provided.' }}</p>
            </div>

            {{-- Meta --}}
            <p class="px-1 text-xs text-gray-400">
                Booked {{ \Illuminate\Support\Carbon::parse($appointment->created_at)->diffForHumans() }}
                @if ($appointment->updated_at && $appointment->updated_at->ne($appointment->created_at))
                    · Updated {{ $appointment->updated_at->diffForHumans() }}
                @endif
            </p>

        </div>
    </div>
</x-app-layout>
