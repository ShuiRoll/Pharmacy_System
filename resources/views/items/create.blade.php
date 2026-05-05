@extends('layouts.app')

@section('page_title', 'Add New Item')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white">Add New Item</h1>
        <p class="mt-1 text-sm text-white/80">SKU is generated as a numeric item code and can still be adjusted.</p>
    </div>

    <div class="app-panel p-6 sm:p-8">
        <form action="{{ route('items.store') }}" method="POST" data-unique-form>
            @csrf

            <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-white">Item Name <span class="text-rose-300">*</span></label>
                    <input id="item-name" type="text" name="name" value="{{ old('name') }}" required
                           data-unique-field="name"
                           data-unique-values='@json($itemNames ?? [])'
                           class="form-input">
                    <p data-unique-warning="name" class="mt-2 hidden text-sm text-amber-200">This item name already exists.</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white">SKU / Item Code <span class="text-rose-300">*</span></label>
                    <input id="item-code" type="text" name="item_code" value="{{ old('item_code') }}" required inputmode="numeric" pattern="[0-9]+"
                           data-unique-field="item_code"
                           data-unique-values='@json($itemCodes ?? [])'
                           class="form-input font-mono uppercase">
                    <p data-unique-warning="item_code" class="mt-2 hidden text-sm text-amber-200">This SKU is already in use.</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white">Category</label>
                    <input type="text" name="category" value="{{ old('category') }}" class="form-input">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white">Current SRP (&#8369;) <span class="text-rose-300">*</span></label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required class="form-input">
                    <p class="mt-2 text-xs text-white/80">Use the current Philippine suggested retail price for POS selling.</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white">Minimum Stock Level <span class="text-rose-300">*</span></label>
                    <input type="number" min="0" name="minimum_stock_lvl" value="{{ old('minimum_stock_lvl', 50) }}" required class="form-input">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-white">Default Storage Location</label>
                    <select name="locationID" class="form-input">
                        <option value="">Select location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->locationID }}" @selected(old('locationID') == $location->locationID)>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-white">Description</label>
                    <textarea name="description" rows="4" class="form-input">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <button type="submit" data-unique-submit class="btn btn-primary flex-1">Save Item</button>
                <a href="{{ route('items.index') }}" class="btn btn-secondary flex-1">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
const existingSkus = @json($itemCodes ?? []);
let skuTouched = false;

function makeSku() {
    const numericCodes = existingSkus
        .map(value => String(value).replace(/\D/g, ''))
        .filter(Boolean)
        .map(value => Number(value));
    let nextCode = Math.max(100000, ...numericCodes) + 1;
    let sku = String(nextCode).padStart(6, '0');

    while (existingSkus.map(value => String(value).toLowerCase()).includes(sku.toLowerCase())) {
        nextCode += 1;
        sku = String(nextCode).padStart(6, '0');
    }

    return sku;
}

document.getElementById('item-code').addEventListener('input', (event) => {
    skuTouched = true;
    event.target.value = event.target.value.replace(/\D/g, '');
});
document.getElementById('item-name').addEventListener('input', (event) => {
    if (!skuTouched) {
        document.getElementById('item-code').value = makeSku();
        document.getElementById('item-code').dispatchEvent(new Event('input', { bubbles: true }));
    }
});

window.addEventListener('load', () => {
    if (!document.getElementById('item-code').value) {
        document.getElementById('item-code').value = makeSku();
        document.getElementById('item-code').dispatchEvent(new Event('input', { bubbles: true }));
        skuTouched = false;
    }
});
</script>
@endsection
