{{--
    Shared medication form fields.
    Expects:
      $medication — a Medication model (for edit) or null (for create)
--}}

{{-- Name --}}
<div>
    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
    <input type="text" name="name" id="name" value="{{ old('name', $medication?->name) }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Strength --}}
<div>
    <label for="strength" class="block text-sm font-medium text-gray-700">Strength <span
            class="text-gray-400">(optional)</span></label>
    <input type="text" name="strength" id="strength" value="{{ old('strength', $medication?->strength) }}"
        placeholder="e.g. 500 mg"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    @error('strength')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Price — entered in dollars, stored as integer cents --}}
<div>
    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
    <div class="relative mt-1">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
        <input type="number" name="price" id="price" min="0" step="0.01"
            value="{{ old('price', $medication ? number_format($medication->price_cents / 100, 2, '.', '') : '') }}"
            placeholder="12.50"
            class="block w-full rounded-md border-gray-300 pl-7 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <p class="mt-1 text-xs text-gray-500">Enter dollars and cents — 12.50. Stored internally as cents.</p>
    @error('price')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Stock quantity --}}
<div>
    <label for="stock_quantity" class="block text-sm font-medium text-gray-700">Stock quantity</label>
    <input type="number" name="stock_quantity" id="stock_quantity" min="0" step="1"
        value="{{ old('stock_quantity', $medication?->stock_quantity) }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    @error('stock_quantity')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Low-stock threshold --}}
<div>
    <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700">Low-stock threshold</label>
    <input type="number" name="low_stock_threshold" id="low_stock_threshold" min="0" step="1"
        value="{{ old('low_stock_threshold', $medication?->low_stock_threshold ?? 10) }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    <p class="mt-1 text-xs text-gray-500">Flagged as low stock once quantity drops to this or below.</p>
    @error('low_stock_threshold')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
