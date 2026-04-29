@extends('layouts.app')

@section('page_title', 'Edit Item')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white">Edit Item</h1>
        <p class="mt-1 text-sm text-slate-300">{{ $item->name }}</p>
    </div>

    <div class="app-panel p-6 sm:p-8">
        <form action="{{ route('items.update', $item) }}" method="POST" data-unique-form>
            @csrf
            @method('PUT')

            <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-slate-200">Item Name <span class="text-rose-300">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                           data-unique-field="name"
                           data-unique-values='@json($itemNames ?? [])'
                           data-original-value="{{ $item->name }}"
                           class="form-input">
                    <p data-unique-warning="name" class="mt-2 hidden text-sm text-amber-200">This item name already exists.</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-200">SKU / Item Code <span class="text-rose-300">*</span></label>
                    <input type="text" name="item_code" value="{{ old('item_code', $item->item_code) }}" required inputmode="numeric" pattern="[0-9]+"
                           data-unique-field="item_code"
                           data-unique-values='@json($itemCodes ?? [])'
                           data-original-value="{{ $item->item_code }}"
                           class="form-input font-mono uppercase">
                    <p data-unique-warning="item_code" class="mt-2 hidden text-sm text-amber-200">This SKU is already in use.</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-200">Category</label>
                    <input type="text" name="category" value="{{ old('category', $item->category) }}" class="form-input">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-200">Price (&#8369;) <span class="text-rose-300">*</span></label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $item->price) }}" required class="form-input">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-200">Minimum Stock Level <span class="text-rose-300">*</span></label>
                    <input type="number" min="0" name="minimum_stock_lvl" value="{{ old('minimum_stock_lvl', $item->minimum_stock_lvl) }}" required class="form-input">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-slate-200">Default Storage Location</label>
                    <select name="locationID" class="form-input">
                        <option value="">Select location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->locationID }}" @selected(old('locationID', $item->locationID) == $location->locationID)>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-slate-200">Description</label>
                    <textarea name="description" rows="4" class="form-input">{{ old('description', $item->description) }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <button type="submit" data-unique-submit class="btn btn-primary flex-1">Update Item</button>
                <a href="{{ route('items.index') }}" class="btn btn-secondary flex-1">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
