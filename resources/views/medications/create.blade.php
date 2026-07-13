<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Add a medication</h2>
        <p class="mt-1 text-sm text-gray-500">Add a new item to the catalog with its pricing and stock.</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6 sm:p-8">

                <form method="POST" action="{{ route('medications.store') }}" class="space-y-6">
                    @csrf
                    @include('medications._form', ['medication' => null])

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('medications.index') }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</a>
                        <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                            Add medication
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
