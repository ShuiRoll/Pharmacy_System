@extends('layouts.app')

@section('page_title', 'Complete Cycle Count')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold">Complete Cycle Count</h1>
            <p class="text-gray-600 dark:text-gray-400">#{{ str_pad($cycleCount->countID, 5, '0', STR_PAD_LEFT) }} planned on {{ $cycleCount->count_date->format('M d, Y') }}</p>
        </div>
        <a href="{{ route('cycle-counts.show', $cycleCount) }}" class="rounded-2xl border border-gray-300 px-5 py-3 text-sm font-medium dark:border-gray-600">
            View Details
        </a>
    </div>

    <div class="rounded-3xl bg-white p-10 shadow-sm dark:bg-gray-800">
        <form action="{{ route('cycle-counts.update', $cycleCount) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium">Count Date</label>
                    <div class="w-full rounded-2xl bg-gray-100 px-6 py-4 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        {{ $cycleCount->count_date->format('M d, Y') }}
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Status</label>
                    <div class="w-full rounded-2xl bg-gray-100 px-6 py-4 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        Incomplete
                    </div>
                </div>
            </div>

            <div class="mt-10 border-t border-gray-200 pt-8 dark:border-gray-700">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold">Planned Count Products</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Enter the quantity change from the expected count. Non-zero changes create inventory adjustments.</p>
                </div>

                <div id="count-lines" class="space-y-4"></div>
            </div>

            <div class="mt-12 flex gap-4">
                <button type="submit" class="flex-1 rounded-2xl bg-blue-600 py-4 font-semibold text-white transition hover:bg-blue-700">
                    Complete Cycle Count
                </button>
                <a href="{{ route('cycle-counts.index') }}" class="flex-1 rounded-2xl border border-gray-300 py-4 text-center font-medium dark:border-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@php
    $existingLines = $cycleCount->cycleCountLines->map(function ($line) {
        return [
            'batchID' => $line->batchID,
            'item' => $line->batch->item->name ?? 'Unknown item',
            'lot' => $line->batch->lot_number ?? 'N/A',
            'expiry' => $line->batch->expiration_date?->format('M d, Y') ?? 'N/A',
            'expected' => $line->expected_quantity,
            'quantity_changed' => $line->actual_quantity - $line->expected_quantity,
        ];
    })->values();
@endphp

<script>
let lineIndex = 0;
const existingLines = @json($existingLines);

function addCountLine(line) {
    const container = document.getElementById('count-lines');
    const row = document.createElement('div');
    const expected = Number(line.expected || 0);
    const change = Number(line.quantity_changed || 0);

    row.className = 'grid grid-cols-12 gap-4 rounded-2xl bg-gray-100 p-4 dark:bg-gray-700';
    row.innerHTML = `
        <input type="hidden" name="lines[${lineIndex}][batchID]" value="${line.batchID}">
        <div class="col-span-12 md:col-span-4">
            <label class="mb-1 block text-xs font-medium">Item / Batch</label>
            <div class="min-h-[48px] rounded-2xl bg-white px-4 py-3 text-sm dark:bg-gray-800">
                <p class="font-semibold">${line.item}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Batch ${line.lot} - Exp ${line.expiry}</p>
            </div>
        </div>
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Expected</label>
            <input type="number" value="${expected}" readonly data-expected class="w-full rounded-2xl bg-white px-4 py-3 dark:bg-gray-800">
        </div>
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Quantity Change</label>
            <input type="number" name="lines[${lineIndex}][quantity_changed]" value="${change}" required oninput="syncCountLine(this)" class="w-full rounded-2xl bg-white px-4 py-3 dark:bg-gray-800">
        </div>
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Actual</label>
            <input type="number" value="${expected + change}" readonly data-actual class="w-full rounded-2xl bg-white px-4 py-3 dark:bg-gray-800">
        </div>
        <div class="col-span-12 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Reason</label>
            <select name="lines[${lineIndex}][reason]" onchange="toggleOtherReason(this)" class="w-full rounded-2xl bg-white px-4 py-3 dark:bg-gray-800" data-reason-select>
                <option value="">Select reason</option>
                <option value="Physical count mismatch">Physical count mismatch</option>
                <option value="Damaged stock found">Damaged stock found</option>
                <option value="Missing stock">Missing stock</option>
                <option value="Data entry correction">Data entry correction</option>
                <option value="Others">Others</option>
            </select>
            <input type="text" name="lines[${lineIndex}][reason_other]" placeholder="Enter reason" class="mt-3 hidden w-full rounded-2xl bg-white px-4 py-3 dark:bg-gray-800" data-other-reason>
        </div>
    `;

    container.appendChild(row);
    lineIndex++;
}

function syncCountLine(input) {
    const row = input.closest('.grid');
    const expected = Number(row.querySelector('[data-expected]').value || 0);
    const change = Number(input.value || 0);

    row.querySelector('[data-actual]').value = expected + change;
    row.querySelector('[data-reason-select]').required = change !== 0;
}

function toggleOtherReason(select) {
    const input = select.closest('.grid').querySelector('[data-other-reason]');
    const needsOtherReason = select.value === 'Others';

    input.classList.toggle('hidden', !needsOtherReason);
    input.required = needsOtherReason;
    if (!needsOtherReason) {
        input.value = '';
    }
}

window.addEventListener('load', () => {
    existingLines.forEach(addCountLine);
});
</script>
@endsection
