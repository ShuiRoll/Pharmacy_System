@extends('layouts.app')

@section('page_title', 'Cycle Count Details')

@section('content')
@php($isCompleted = $cycleCount->status === 'Completed')

<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h1 class="text-3xl font-bold">Cycle Count #{{ str_pad($cycleCount->countID, 5, '0', STR_PAD_LEFT) }}</h1>
        <p class="text-white/80">Planned products and completed adjustment details</p>
    </div>
    <div class="flex flex-wrap gap-3">
        @unless($isCompleted)
            <a href="{{ route('cycle-counts.edit', $cycleCount) }}" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                Complete Cycle Count
            </a>
        @endunless
        <a href="{{ route('cycle-counts.index') }}" class="rounded-2xl border border-gray-300 px-5 py-3 text-sm font-medium dark:border-gray-600">
            Back to Cycle Counts
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 md:grid-cols-3">
    <div class="rounded-3xl bg-white p-6 shadow-sm dark:bg-gray-800">
        <p class="text-sm text-white/80">Count Date</p>
        <p class="mt-2 text-2xl font-semibold">{{ $cycleCount->count_date->format('M d, Y') }}</p>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow-sm dark:bg-gray-800">
        <p class="text-sm text-white/80">Performed By</p>
        <p class="mt-2 text-2xl font-semibold">{{ $cycleCount->user->name ?? '-' }}</p>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow-sm dark:bg-gray-800">
        <p class="text-sm text-white/80">Status</p>
        <span class="mt-3 inline-flex rounded-full px-4 py-1 text-xs font-semibold {{ $isCompleted ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
            {{ $isCompleted ? 'Completed' : 'Incomplete' }}
        </span>
    </div>
</div>

<div class="mt-8 overflow-hidden rounded-3xl bg-white shadow-sm dark:bg-gray-800">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="p-6 text-left font-medium">Medicine</th>
                <th class="p-6 text-left font-medium">Batch</th>
                <th class="p-6 text-left font-medium">Location</th>
                <th class="p-6 text-right font-medium">Expected</th>
                <th class="p-6 text-right font-medium">Change</th>
                <th class="p-6 text-right font-medium">Actual</th>
                <th class="p-6 text-left font-medium">Adjustment Reason</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($cycleCount->cycleCountLines as $line)
                @php($change = $line->actual_quantity - $line->expected_quantity)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="p-6 font-medium">{{ $line->batch->item->name ?? '-' }}</td>
                    <td class="p-6 font-mono text-sm">{{ $line->batch->lot_number ?? '-' }}</td>
                    <td class="p-6">{{ $line->batch->location->name ?? '-' }}</td>
                    <td class="p-6 text-right">{{ $line->expected_quantity }}</td>
                    <td class="p-6 text-right">
                        <span class="status-pill {{ $change > 0 ? 'status-success' : ($change < 0 ? 'status-danger' : '') }}">
                            {{ $change > 0 ? '+' : '' }}{{ $change }}
                        </span>
                    </td>
                    <td class="p-6 text-right font-semibold">{{ $line->actual_quantity }}</td>
                    <td class="p-6">{{ $line->inventoryAdjustment->reason ?? ($change === 0 ? 'No adjustment needed' : '-') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-10 text-center text-sm text-white/80">No planned products for this cycle count.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
