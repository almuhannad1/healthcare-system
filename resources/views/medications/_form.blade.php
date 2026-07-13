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

{{-- Price (stored as integer cents) --}}
<div>
    <label for="price_cents" class="block text-sm font-medium text-gray-700">Price in cents</label>
    <input type="number" name="price_cents" id="price_cents" min="0" step="1"
        value="{{ old('price_cents', $medication?->price_cents) }}" placeholder="e.g. 1299 for $12.99"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    <p class="mt-1 text-xs text-gray-500">Whole cents — 1299 means $12.99. Storage stays integer.</p>
    @error('price_cents')
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
