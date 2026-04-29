@extends('layouts.app')

@section('page_title', 'New Purchase Order')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">New Purchase Order</h1>
        <p class="text-gray-600 dark:text-gray-400">Create order for supplier</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-10">
        <form action="{{ route('purchase-orders.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-medium mb-2">Supplier <span class="text-red-500">*</span></label>
                    <select name="supplierID" required 
                            class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->supplierID }}">{{ $supplier->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">PO Date <span class="text-red-500">*</span></label>
                    <input type="date" name="po_date" value="{{ date('Y-m-d') }}" required 
                           class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Expected Delivery Date</label>
                    <input type="date" name="expected_date" 
                           class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none">
                </div>

                <div class="rounded-lg border border-amber-400/30 bg-amber-400/10 px-5 py-4 text-sm text-amber-100">
                    New purchase orders are created as Pending and can be approved from Purchasing.
                </div>
            </div>

            <div class="mt-10 border-t border-gray-200 pt-8 dark:border-gray-700">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Order Items</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Medicines requested from this supplier</p>
                    </div>
                    <button type="button" onclick="addPoLine()" class="rounded-full border border-blue-500/50 px-4 py-2 text-sm font-medium text-blue-200">
                        Add Item
                    </button>
                </div>

                <div id="po-lines" class="space-y-4"></div>
            </div>

            <div class="mt-12 flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-medium">
                    Create Purchase Order
                </button>
                <a href="{{ route('purchase-orders.index') }}" 
                   class="flex-1 text-center border border-gray-300 dark:border-gray-600 py-4 rounded-2xl font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let poLineCount = 0;

function addPoLine(line = {}) {
    const container = document.getElementById('po-lines');
    const row = document.createElement('div');
    const selectedItem = line.item_id || '';

    row.className = 'grid grid-cols-12 gap-4 rounded-2xl bg-gray-100 p-4 dark:bg-gray-700';
    row.innerHTML = `
        <div class="col-span-12 md:col-span-6">
            <label class="mb-1 block text-xs font-medium">Medicine</label>
            <select name="items[${poLineCount}][item_id]" required class="w-full rounded-2xl bg-white px-4 py-3 dark:bg-gray-800">
                <option value="">Select Medicine</option>
                @foreach($items as $item)
                    <option value="{{ $item->itemID }}" ${selectedItem == '{{ $item->itemID }}' ? 'selected' : ''}>{{ $item->name }} ({{ $item->item_code }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Quantity</label>
            <input type="number" min="1" name="items[${poLineCount}][quantity]" value="${line.quantity || ''}" required class="w-full rounded-2xl bg-white px-4 py-3 dark:bg-gray-800">
        </div>
        <div class="col-span-6 md:col-span-3">
            <label class="mb-1 block text-xs font-medium">Unit Cost</label>
            <input type="number" min="0" step="0.01" name="items[${poLineCount}][unit_cost]" value="${line.unit_cost || ''}" required class="w-full rounded-2xl bg-white px-4 py-3 dark:bg-gray-800">
        </div>
        <div class="col-span-12 flex items-end md:col-span-1">
            <button type="button" onclick="this.closest('.grid').remove()" class="px-3 py-3 text-red-300">Remove</button>
        </div>
    `;

    container.appendChild(row);
    poLineCount++;
}

window.addEventListener('load', () => addPoLine());
</script>
@endsection
