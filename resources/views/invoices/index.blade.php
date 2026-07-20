<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Invoices</h2>
        <p class="mt-1 text-sm text-gray-500">Bills raised against completed appointments.</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800 ring-1 ring-inset ring-green-600/20">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5">
                @if ($invoices->isEmpty())
                    <p class="p-8 text-center text-sm text-gray-500">
                        No invoices yet. Generate one from an appointment.
                    </p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Doctor</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        #{{ $invoice->invoice_id }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                        {{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                        Dr. {{ $invoice->appointment->doctor->last_name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium text-gray-900">
                                        ${{ $invoice->totalDollars() }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span @class([
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize ring-1 ring-inset',
                                            'bg-green-50 text-green-700 ring-green-600/20' => $invoice->status === 'paid',
                                            'bg-amber-50 text-amber-700 ring-amber-600/20' => $invoice->status !== 'paid',
                                        ])>
                                            {{ $invoice->status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('invoices.pdf', $invoice) }}"
                                                class="font-medium text-indigo-600 hover:text-indigo-800">
                                                Download PDF
                                            </a>

                                            @can('markPaid', $invoice)
                                                @if ($invoice->status !== 'paid')
                                                    <form method="POST" action="{{ route('invoices.paid', $invoice) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="font-medium text-green-700 hover:text-green-900">
                                                            Mark paid
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
