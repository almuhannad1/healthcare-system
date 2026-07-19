<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Medications</h2>
                <p class="mt-1 text-sm text-gray-500">Catalog of stocked medications, pricing, and stock levels.</p>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">
                    {{ $medications->count() }} {{ Str::plural('medication', $medications->count()) }}
                </span>
                <a href="{{ route('medications.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New medication
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
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
                                    Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Strength</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Price</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Stock</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($medications as $medication)
                                <tr class="transition-colors hover:bg-indigo-50/40">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $medication->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $medication->strength ?: '—' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${{ number_format($medication->price_cents / 100, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($medication->isLowStock())
                                            <span
                                                class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                                                Low stock — {{ $medication->stock_quantity }} left
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-700">{{ $medication->stock_quantity }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($medication->isLowStock())
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium capitalize text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                Reorder
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium capitalize text-green-700 ring-1 ring-inset ring-green-600/20">
                                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                In stock
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            @if ($medication->stock_quantity > 0)
                                                <a href="{{ route('medications.dispense.create', $medication) }}"
                                                    class="inline-flex items-center rounded-md px-2.5 py-1.5 text-sm font-medium text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700">Dispense</a>
                                            @else
                                                <span
                                                    class="inline-flex items-center rounded-md px-2.5 py-1.5 text-sm font-medium text-gray-300"
                                                    title="Out of stock">Dispense</span>
                                            @endif
                                            <a href="{{ route('medications.edit', $medication) }}"
                                                class="inline-flex items-center rounded-md px-2.5 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16">
                                        <div class="flex flex-col items-center justify-center text-center">
                                            <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                                            </svg>
                                            <h3 class="mt-3 text-sm font-medium text-gray-900">No medications yet</h3>
                                            <p class="mt-1 text-sm text-gray-500">Medications in the catalog will appear
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
