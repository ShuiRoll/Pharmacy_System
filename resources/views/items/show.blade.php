@extends('layouts.app')

@section('page_title', 'Item Details')

@section('content')
@php
    $totalStock = $item->inventoryBatches->sum('current_quantity');
    $activeBatches = $item->inventoryBatches->where('current_quantity', '>', 0)->count();
    $earliestBatch = $item->inventoryBatches
        ->where('current_quantity', '>', 0)
        ->whereNotNull('expiration_date')
        ->sortBy('expiration_date')
        ->first();
@endphp

<div class="space-y-8">
    <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-xl shadow-black/20 backdrop-blur">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <div class="mb-4 flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/80">
                    <span class="rounded-full border border-blue-500/30 bg-blue-500/10 px-3 py-1 text-blue-200">Item Details</span>
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80">{{ $item->item_code }}</span>
                </div>
                <h1 class="text-4xl font-semibold tracking-tight text-white">{{ $item->name }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/80">
                    {{ $item->description ?: 'Batch-level stock and expiry details for this item.' }}
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('items.edit', $item) }}" class="rounded-full border border-blue-500/50 bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Edit Item</a>
                <a href="{{ route('items.index') }}" class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white transition hover:border-blue-400/40 hover:bg-blue-500/10">Back to Items</a>
            </div>
        </div>

                <div class="mt-8 grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white/80">Total Stock</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $totalStock }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white/80">Active Batches</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $activeBatches }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white/80">Default Location</p>
                <p class="mt-2 text-lg font-semibold text-white">{{ $item->location->name ?? 'Not set' }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white/80">Current SRP</p>
                <p class="mt-2 text-lg font-semibold text-white">PHP {{ number_format($item->price, 2) }}</p>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-xl shadow-black/20 backdrop-blur">
            <div class="border-b border-white/10 px-6 py-5">
            <h2 class="text-xl font-semibold text-white">Batches by Expiry Date</h2>
            <p class="mt-1 text-sm text-white/80">Earliest expiry appears first so stock can be reviewed using FEFO.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-800/70 text-xs uppercase tracking-[0.2em] text-white/60">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Batch</th>
                        <th class="px-6 py-4 font-semibold">Expiry Date</th>
                        <th class="px-6 py-4 font-semibold">Location</th>
                        <th class="px-6 py-4 text-right font-semibold">Current Quantity</th>
                        <th class="px-6 py-4 text-right font-semibold">Unit Cost</th>
                        <th class="px-6 py-4 text-right font-semibold">Current SRP</th>
                        <th class="px-6 py-4 text-right font-semibold">Retail Value</th>
                        <th class="w-36 px-6 py-4 text-center font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($item->inventoryBatches as $batch)
                        @php
                            $isExpired = $batch->expiration_date && $batch->expiration_date->isPast();
                            $isNearExpiry = $batch->expiration_date && ! $isExpired && $batch->expiration_date->lte(now()->addDays(30));
                        @endphp
                        <tr class="transition hover:bg-slate-800/60">
                            <td class="px-6 py-5 font-mono text-xs font-semibold text-white">{{ $batch->lot_number ?? 'Batch '.$batch->batchID }}</td>
                            <td class="px-6 py-5 text-white/80">{{ $batch->expiration_date?->format('M d, Y') ?? 'No expiry' }}</td>
                            <td class="px-6 py-5 text-white/80">{{ $batch->location->name ?? 'Not set' }}</td>
                            <td class="px-6 py-5 text-right font-semibold text-white">{{ $batch->current_quantity }}</td>
                            <td class="px-6 py-5 text-right text-white/80">PHP {{ number_format($batch->unit_cost ?? 0, 2) }}</td>
                            <td class="px-6 py-5 text-right text-white/80">PHP {{ number_format($item->price, 2) }}</td>
                            <td class="px-6 py-5 text-right font-semibold text-white">PHP {{ number_format($batch->current_quantity * $item->price, 2) }}</td>
                            <td class="w-36 px-6 py-5 text-center">
                                @if($batch->current_quantity <= 0)
                                    <span class="inline-flex min-w-24 items-center justify-center whitespace-nowrap rounded-full bg-slate-500/15 px-3 py-1 text-xs font-semibold text-white/80">Empty</span>
                                @elseif($isExpired)
                                    <span class="inline-flex min-w-24 items-center justify-center whitespace-nowrap rounded-full bg-rose-500/15 px-3 py-1 text-xs font-semibold text-rose-200">Expired</span>
                                @elseif($isNearExpiry)
                                    <span class="inline-flex min-w-24 items-center justify-center whitespace-nowrap rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-200">Near Expiry</span>
                                @else
                                    <span class="inline-flex min-w-24 items-center justify-center whitespace-nowrap rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-200">Good</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-white/80">No batches recorded for this item yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
