@extends('layouts.app')

@section('page_title', 'Receive Goods')

@section('content')
<div class="mx-auto max-w-6xl">
    <div class="mb-8 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold">Receive Goods</h1>
            <p class="text-gray-600 dark:text-gray-400">Inbound Transaction - Warehouse / Pharmacy</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">Received By</p>
            <p class="font-medium">{{ auth()->user()->name }}</p>
        </div>
    </div>

    <div class="rounded-3xl bg-white p-10 shadow-sm dark:bg-gray-800">
        <form id="inbound-form" action="{{ route('inbound.store') }}" method="POST">
            @csrf

            <div class="mb-10 grid grid-cols-1 gap-8 md:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-medium">Approved Purchase Order</label>
                    <select id="po-select" name="poID" class="w-full rounded-2xl bg-gray-100 px-5 py-4 focus:outline-none dark:bg-gray-700">
                        <option value="">Direct Receipt (No PO)</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->poID }}">PO-{{ str_pad($po->poID, 4, '0', STR_PAD_LEFT) }} - {{ $po->supplier->supplier_name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Date Received</label>
                    <input type="date" name="date_received" value="{{ old('date_received', date('Y-m-d')) }}" required
                           class="w-full rounded-2xl bg-gray-100 px-5 py-4 focus:outline-none dark:bg-gray-700">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Quality Status</label>
                    <select name="quality_status" class="w-full rounded-2xl bg-gray-100 px-5 py-4 focus:outline-none dark:bg-gray-700">
                        <option value="Passed">Passed</option>
                        <option value="Pending">Pending Inspection</option>
                        <option value="Failed">Failed / Rejected</option>
                    </select>
                </div>
            </div>

            <div class="mb-4 flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold">Items Received</h2>
                <button type="button" onclick="addLineItem()" class="rounded-full border border-blue-500/50 px-4 py-2 text-sm font-medium text-blue-200">
                    Add Another Item
                </button>
            </div>

            <div id="line-items" class="space-y-6"></div>

            <div class="mt-12 flex gap-4">
                <button type="submit" class="flex-1 rounded-2xl bg-green-600 py-5 text-lg font-semibold text-white transition hover:bg-green-700">
                    Complete Receipt & Update Stock
                </button>
                <a href="{{ route('inbound.index') }}" class="flex-1 rounded-2xl border border-gray-300 py-5 text-center font-medium dark:border-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@php
    $purchaseOrderLines = $purchaseOrders->mapWithKeys(function ($po) {
        return [
            $po->poID => $po->purchaseOrderLines->map(function ($line) {
                return [
                    'item_id' => $line->itemID,
                    'quantity' => $line->quantity_ordered,
                    'unit_cost' => $line->unit_cost,
                ];
            })->values(),
        ];
    });

    $itemLocations = $items->mapWithKeys(function ($item) {
        return [$item->itemID => $item->locationID];
    });
@endphp

<script>
let lineCount = 0;
const purchaseOrders = @json($purchaseOrderLines);
const itemLocations = @json($itemLocations);

function addLineItem(line = {}) {
    lineCount++;

    const container = document.getElementById('line-items');
    const row = document.createElement('div');
    const selectedItem = line.item_id || '';
    const selectedLocation = line.location_id || itemLocations[selectedItem] || '';

    row.className = 'grid grid-cols-12 gap-4 rounded-3xl bg-gray-50 p-6 dark:bg-gray-700';
    row.innerHTML = `
        <div class="col-span-12 md:col-span-4">
            <label class="mb-1 block text-xs font-medium">Medicine</label>
            <select name="items[${lineCount}][item_id]" required onchange="syncLineLocation(this)" class="w-full rounded-2xl border border-gray-300 bg-white px-5 py-4 dark:border-gray-600 dark:bg-gray-800">
                <option value="">Select Medicine</option>
                @foreach($items as $item)
                    <option value="{{ $item->itemID }}" ${selectedItem == '{{ $item->itemID }}' ? 'selected' : ''}>{{ $item->name }} ({{ $item->item_code }})</option>
                @endforeach
            </select>
        </div>

        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Quantity</label>
            <input type="number" name="items[${lineCount}][quantity]" value="${line.quantity || ''}" required min="1" class="w-full rounded-2xl border border-gray-300 bg-white px-5 py-4 text-center dark:border-gray-600 dark:bg-gray-800">
        </div>

        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Lot Number</label>
            <input type="text" name="items[${lineCount}][lot_number]" class="w-full rounded-2xl border border-gray-300 bg-white px-5 py-4 dark:border-gray-600 dark:bg-gray-800">
        </div>

        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Expiration Date</label>
            <input type="date" name="items[${lineCount}][expiration_date]" class="w-full rounded-2xl border border-gray-300 bg-white px-5 py-4 dark:border-gray-600 dark:bg-gray-800">
        </div>

        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Unit Cost</label>
            <input type="number" step="0.01" min="0" name="items[${lineCount}][unit_cost]" value="${line.unit_cost || ''}" required class="w-full rounded-2xl border border-gray-300 bg-white px-5 py-4 dark:border-gray-600 dark:bg-gray-800">
        </div>

        <div class="col-span-12 md:col-span-4">
            <label class="mb-1 block text-xs font-medium">Stock Location</label>
            <select name="items[${lineCount}][location_id]" data-location-select class="w-full rounded-2xl border border-gray-300 bg-white px-5 py-4 dark:border-gray-600 dark:bg-gray-800">
                <option value="">Use item default</option>
                @foreach($locations as $location)
                    <option value="{{ $location->locationID }}" ${selectedLocation == '{{ $location->locationID }}' ? 'selected' : ''}>{{ $location->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-span-12 flex items-end md:col-span-1">
            <button type="button" onclick="this.closest('.grid').remove()" class="p-3 text-red-300 hover:text-red-200">
                Remove
            </button>
        </div>
    `;

    container.appendChild(row);
}

function syncLineLocation(select) {
    const row = select.closest('.grid');
    const locationSelect = row.querySelector('[data-location-select]');

    if (locationSelect && itemLocations[select.value]) {
        locationSelect.value = itemLocations[select.value];
    }
}

document.getElementById('po-select').addEventListener('change', function () {
    const lines = purchaseOrders[this.value] || [];

    if (!lines.length) {
        return;
    }

    document.getElementById('line-items').innerHTML = '';
    lines.forEach(addLineItem);
});

window.addEventListener('load', () => addLineItem());
</script>
@endsection
