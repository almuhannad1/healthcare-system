<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Clinic dashboard</h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ now()->format('F Y') }} · updated {{ now()->format('g:i A') }}
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ─── 1. Headline numbers ─────────────────────────────── --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total patients</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900">
                        {{ number_format($totalPatients) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Appointments this month</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900">
                        {{ number_format($appointmentsThisMonth) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Revenue collected</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-green-700">
                        ${{ number_format($revenueCents / 100, 2) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Unpaid balance</p>
                    <p @class([
                        'mt-2 text-3xl font-bold tracking-tight',
                        'text-amber-600' => $unpaidCents > 0,
                        'text-gray-900' => $unpaidCents === 0,
                    ])>
                        ${{ number_format($unpaidCents / 100, 2) }}
                    </p>
                </div>
            </div>

            {{-- ─── 3. Low stock — loudest thing on the page ─────────── --}}
            @if ($lowStock->isNotEmpty())
                <div class="overflow-hidden rounded-2xl bg-red-50 shadow-sm ring-1 ring-red-600/20">
                    <div class="flex items-center gap-3 border-b border-red-200 px-6 py-4">
                        <svg class="h-5 w-5 shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <h3 class="text-sm font-semibold text-red-900">
                            {{ $lowStock->count() }}
                            {{ Str::plural('medication', $lowStock->count()) }} need restocking
                        </h3>
                    </div>

                    <table class="min-w-full divide-y divide-red-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-red-700">
                                    Medication</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-red-700">
                                    In stock</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-red-700">
                                    Threshold</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-red-700">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-red-100">
                            @foreach ($lowStock as $medication)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $medication->name }}
                                        @if ($medication->strength)
                                            <span class="text-gray-400">· {{ $medication->strength }}</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <span @class([
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset',
                                            'bg-red-100 text-red-800 ring-red-600/30' => $medication->stock_quantity === 0,
                                            'bg-red-50 text-red-700 ring-red-600/20' => $medication->stock_quantity > 0,
                                        ])>
                                            {{ $medication->stock_quantity === 0 ? 'Out of stock' : $medication->stock_quantity }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-500">
                                        {{ $medication->low_stock_threshold }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <a href="{{ route('medications.edit', $medication) }}"
                                            class="font-medium text-red-700 hover:text-red-900">Restock</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="rounded-2xl bg-green-50 px-6 py-4 text-sm text-green-800 ring-1 ring-inset ring-green-600/20">
                    All medications are above their restock threshold.
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- ─── 2. Appointments per doctor ───────────────────── --}}
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-sm font-semibold text-gray-900">Busiest doctors</h3>
                        <p class="mt-0.5 text-xs text-gray-500">By total appointments booked</p>
                    </div>

                    @if ($busiestDoctors->isEmpty())
                        <p class="p-8 text-center text-sm text-gray-500">No doctors yet.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-100">
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($busiestDoctors as $doctor)
                                    <tr>
                                        <td class="px-6 py-3 text-sm text-gray-900">
                                            Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                                            <span class="ml-1 text-xs text-gray-400">{{ $doctor->specialty }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-3 text-right text-sm font-semibold text-gray-900">
                                            {{ number_format($doctor->appointments_count) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- ─── 4. Recent unpaid invoices ────────────────────── --}}
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Unpaid invoices</h3>
                            <p class="mt-0.5 text-xs text-gray-500">Oldest first — chase these</p>
                        </div>
                        <a href="{{ route('invoices.index') }}"
                            class="text-xs font-medium text-indigo-600 hover:text-indigo-800">View all</a>
                    </div>

                    @if ($unpaidInvoices->isEmpty())
                        <p class="p-8 text-center text-sm text-gray-500">Nothing outstanding.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-100">
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($unpaidInvoices as $invoice)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-3 text-sm text-gray-500">
                                            #{{ $invoice->invoice_id }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-900">
                                            {{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}
                                            <span class="block text-xs text-gray-400">
                                                {{ $invoice->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-3 text-right text-sm font-semibold text-amber-700">
                                            ${{ $invoice->totalDollars() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
