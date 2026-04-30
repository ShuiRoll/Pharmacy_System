@extends('layouts.app')

@section('page_title', 'New Cycle Count')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">New Cycle Count</h1>
        <p class="text-gray-600 dark:text-gray-400">Plan the batches to count. Quantity changes are entered when the count is completed.</p>
    </div>

    <div class="rounded-3xl bg-white p-10 shadow-sm dark:bg-gray-800">
        <form action="{{ route('cycle-counts.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium">Count Date <span class="text-red-500">*</span></label>
                    <input type="date" name="count_date" value="{{ old('count_date', date('Y-m-d')) }}" required
                           class="w-full rounded-2xl bg-gray-100 px-6 py-4 focus:outline-none dark:bg-gray-700">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Status</label>
                    <div class="w-full rounded-2xl bg-gray-100 px-6 py-4 text-gray-600 dark:bg-gray-700 dark:text-gray-300">Incomplete</div>
                </div>
            </div>

            <div class="mt-10 border-t border-gray-200 pt-8 dark:border-gray-700">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Cycle Count Lines</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Expected quantity comes from the selected batch. Quantity change starts at 0 and is locked until completion.</p>
                    </div>
                    <button type="button" onclick="addCountLine()" class="rounded-full border border-blue-500/50 px-4 py-2 text-sm font-medium text-blue-200">
                        Add Item
                    </button>
                </div>

                <div id="count-lines" class="space-y-4"></div>
            </div>

            <div class="mt-12 flex gap-4">
                <button type="submit" class="flex-1 rounded-2xl bg-blue-600 py-4 font-medium text-white hover:bg-blue-700">
                    Plan Cycle Count
                </button>
                <a href="{{ route('cycle-counts.index') }}" class="flex-1 rounded-2xl border border-gray-300 py-4 text-center font-medium dark:border-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@php
    $batchLookup = $batches->mapWithKeys(function ($batch) {
        return [
            $batch->batchID => [
                'expected' => $batch->current_quantity,
            ],
        ];
    });
@endphp

<script>
let lineIndex = 0;
const batchLookup = @json($batchLookup);

function addCountLine(line = {}) {
    const container = document.getElementById('count-lines');
    const row = document.createElement('div');
    const selectedBatch = line.batchID || '';
    const expected = selectedBatch && batchLookup[selectedBatch] ? batchLookup[selectedBatch].expected : 0;
    const change = 0;

    row.className = 'grid grid-cols-12 gap-4 rounded-2xl bg-gray-100 p-4 dark:bg-gray-700';
    row.innerHTML = `
        <div class="col-span-12 md:col-span-5">
            <label class="mb-1 block text-xs font-medium">Item / Batch</label>
            <select name="lines[${lineIndex}][batchID]" required onchange="syncCountLine(this)" class="w-full rounded-2xl bg-white px-4 py-3 dark:bg-gray-800">
                <option value="">Select item batch</option>
                @foreach($batches as $batch)
                    <option value="{{ $batch->batchID }}" ${selectedBatch == '{{ $batch->batchID }}' ? 'selected' : ''}>
                        {{ $batch->item->name ?? 'Unknown item' }} - Batch {{ $batch->lot_number ?? $batch->batchID }} - Exp {{ $batch->expiration_date?->format('M d, Y') ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Expected</label>
            <input type="number" value="${expected}" readonly data-expected class="w-full rounded-2xl bg-white px-4 py-3 dark:bg-gray-800">
        </div>
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Quantity Change</label>
            <input type="number" value="0" readonly class="w-full rounded-2xl bg-white px-4 py-3 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
        </div>
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-xs font-medium">Actual</label>
            <input type="number" value="${expected + Number(change)}" readonly data-actual class="w-full rounded-2xl bg-white px-4 py-3 dark:bg-gray-800">
        </div>
        <div class="col-span-6 flex items-end md:col-span-1">
            <button type="button" onclick="this.closest('.grid').remove()" class="px-3 py-3 text-red-300">Remove</button>
        </div>
    `;

    container.appendChild(row);
    lineIndex++;
}

function syncCountLine(input) {
    const row = input.closest('.grid');
    const batchID = row.querySelector('select').value;
    const expected = batchID && batchLookup[batchID] ? Number(batchLookup[batchID].expected) : 0;
    const change = 0;

    row.querySelector('[data-expected]').value = expected;
    row.querySelector('[data-actual]').value = expected + change;
}

window.addEventListener('load', () => addCountLine());
</script>
@endsection
