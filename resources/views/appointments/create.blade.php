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
                    @include('appointments._form', ['appointment' => null])

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('appointments.index') }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</a>
                        <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
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
