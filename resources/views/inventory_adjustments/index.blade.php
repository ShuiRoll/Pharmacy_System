@extends('layouts.app')

@section('page_title', 'Adjustments')

@section('content')
@php
    $adjustmentCount = $adjustments->count();
    $recentAdjustments = $adjustments->where('adjustment_date', '>=', now()->startOfMonth())->count();
    $pendingOutbounds = $outbounds->where('status', 'Pending')->count();
    $approvedOutbounds = $outbounds->where('status', 'Approved')->count();
    $transferredOutbounds = $outbounds->where('status', 'Transferred')->count();
    $outboundTotalAmount = $outbounds->sum('total_amount');
@endphp

<div class="space-y-8" data-filter-scope>
    <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-xl shadow-black/20 backdrop-blur">
        <div class="mb-6 flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.3em] text-white">
            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80">Filter</span>
            <button type="button" data-section-filter="all" class="rounded-full border border-blue-500/50 bg-blue-600 px-3 py-1 text-white transition hover:border-blue-400 hover:text-blue-100">All</button>
            <button type="button" data-section-filter="adjustments" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80 transition hover:border-blue-400 hover:text-white">Adjustments</button>
            <button type="button" data-section-filter="watchlist" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80 transition hover:border-blue-400 hover:text-white">Watchlist</button>
            <button type="button" data-section-filter="outbound" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80 transition hover:border-blue-400 hover:text-white">Outbound</button>
        </div>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">Adjustments</h1>
                <p class="mt-2 text-sm text-white/80">Monitor stock health and record manual corrections.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('items.low-stock') }}" class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-medium text-white transition hover:border-amber-400/40 hover:bg-amber-500/10 hover:text-amber-200">Low Stock</a>
                <a href="{{ route('items.near-expiry') }}" class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-medium text-white transition hover:border-orange-400/40 hover:bg-orange-500/10 hover:text-orange-200">Near Expiry</a>
                <a href="{{ route('cycle-counts.index') }}" class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-medium text-white transition hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-blue-200">Cycle Counts</a>
                <a href="{{ route('outbound.create') }}" class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-medium text-white transition hover:border-rose-400/40 hover:bg-rose-500/10 hover:text-rose-200">New Outbound</a>
            </div>
        </div>

                <div class="mt-8 grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white/80">Adjustments</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $adjustmentCount }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white/80">This Month</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $recentAdjustments }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white/80">Watchlisted</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $lowStockItems->count() + $nearExpiryItems->count() }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white/80">Outbound</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $outbounds->count() }}</p>
            </div>
        </div>
    </section>

    <section class="grid gap-8 xl:grid-cols-[1.35fr_0.85fr]">
        <div id="adjustments" data-filter-section="adjustments" class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-xl shadow-black/20 backdrop-blur">
            <div class="border-b border-slate-200 px-6 py-6 dark:border-slate-800">
                <h2 class="text-xl font-semibold text-white">Recent adjustments</h2>
                <p class="text-sm text-white/80">Manual stock corrections recorded by the team</p>
            </div>

            <div class="overflow-x-auto px-6 py-6">
                <table class="w-full text-sm">
                        <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-[0.2em] text-white/80">
                            <tr>
                                <th class="px-6 py-5 font-semibold">Date</th>
                                <th class="px-6 py-5 font-semibold">Item</th>
                                <th class="px-6 py-5 font-semibold">Batch</th>
                                <th class="px-6 py-5 text-center font-semibold">Change</th>
                                <th class="px-6 py-5 font-semibold">Reason</th>
                                <th class="px-6 py-5 font-semibold">Recorded By</th>
                            </tr>
                        </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($adjustments as $adjustment)
                            <tr class="transition hover:bg-white/5">
                                <td class="px-6 py-5 text-white/80">{{ $adjustment->adjustment_date->format('M d, Y') }}</td>
                                <td class="px-6 py-5 font-medium text-white">{{ $adjustment->batch->item->name ?? '—' }}</td>
                                <td class="px-6 py-5 text-white/80">{{ $adjustment->batch->lot_number ?? '—' }}</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="status-pill {{ $adjustment->quantity_changed >= 0 ? 'status-success' : 'status-danger' }}">
                                        {{ $adjustment->quantity_changed > 0 ? '+' : '' }}{{ $adjustment->quantity_changed }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-white/80">{{ $adjustment->reason }}</td>
                                <td class="px-6 py-5 text-white">{{ $adjustment->user->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-white/80">No adjustments recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <aside id="watchlist" data-filter-section="watchlist" class="space-y-6">
                <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-xl shadow-black/20 backdrop-blur">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Low stock</h2>
                        <p class="text-sm text-white/80">Items at or below reorder point</p>
                    </div>
                    <a href="{{ route('items.low-stock') }}" class="text-sm font-medium table-action">View all</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($lowStockItems->take(5) as $item)
                        @php($stock = $item->inventoryBatches->sum('current_quantity'))
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-white">{{ $item->name }}</p>
                                    <p class="mt-1 text-sm text-white/80">{{ $item->item_code }}</p>
                                </div>
                                <span class="rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-200">{{ $stock }} left</span>
                            </div>
                        </div>
                        @empty
                        <div class="rounded-2xl border border-dashed border-white/20 p-6 text-sm text-white/70">Nothing is below reorder point.</div>
                    @endforelse
                </div>
            </div>

                        <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-xl shadow-black/20 backdrop-blur">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Near expiry</h2>
                        <p class="text-sm text-white/80">Batches expiring in 30 days</p>
                    </div>
                    <a href="{{ route('items.near-expiry') }}" class="text-sm font-medium table-action">View all</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($nearExpiryItems->take(5) as $item)
                        @php($batch = $item->inventoryBatches->sortBy('expiration_date')->first())
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-white">{{ $item->name }}</p>
                                    <p class="mt-1 text-sm text-white/80">Batch {{ $batch?->lot_number ?? '—' }}</p>
                                </div>
                                <span class="rounded-full bg-orange-500/15 px-3 py-1 text-xs font-semibold text-orange-200">{{ $batch?->expiration_date?->format('M d') ?? '—' }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="rounded-2xl border border-dashed border-white/20 p-6 text-sm text-white/70">No near-expiry batches right now.</div>
                    @endforelse
                </div>
            </div>
        </aside>
    </section>

    <section id="outbound" data-filter-section="outbound" class="space-y-8">
        <div class="app-panel p-6 sm:p-8">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-white">Outbound Transactions</h2>
                    <p class="mt-2 text-sm text-white/80">Manage stock transfers from pending approval to completed transfer.</p>
                </div>

                <a href="{{ route('outbound.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    New Outbound
                </a>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="app-card p-5">
                    <p class="text-sm text-white/80">Pending</p>
                    <p class="mt-2 text-3xl font-semibold text-white">{{ $pendingOutbounds }}</p>
                </div>
                <div class="app-card p-5">
                    <p class="text-sm text-white/80">Approved</p>
                    <p class="mt-2 text-3xl font-semibold text-white">{{ $approvedOutbounds }}</p>
                </div>
                <div class="app-card p-5">
                    <p class="text-sm text-white/80">Transferred</p>
                    <p class="mt-2 text-3xl font-semibold text-white">{{ $transferredOutbounds }}</p>
                </div>
                <div class="app-card p-5">
                    <p class="text-sm text-white/80">Total Amount</p>
                    <p class="mt-2 text-3xl font-semibold text-white">&#8369;{{ number_format($outboundTotalAmount, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-xl shadow-black/20 backdrop-blur">
            <div class="flex flex-col gap-3 border-b border-white/10 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-white">Outbound Records</h2>
                    <p class="text-sm text-white/80">Line details, status, and transfer actions.</p>
                </div>
                <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm font-semibold text-white/80">
                    {{ $outbounds->count() }} records
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="table-head">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Transaction</th>
                            <th class="px-6 py-4 font-semibold">Date</th>
                            <th class="px-6 py-4 font-semibold">Destination</th>
                            <th class="px-6 py-4 font-semibold">Processed By</th>
                            <th class="px-6 py-4 text-center font-semibold">Items</th>
                            <th class="px-6 py-4 text-center font-semibold">Status</th>
                            <th class="px-6 py-4 text-right font-semibold">Total</th>
                            <th class="px-6 py-4 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($outbounds as $outbound)
                            <tr class="transition hover:bg-white/5">
                                <td class="px-6 py-5 font-mono text-xs font-semibold text-white/80">
                                    <button type="button" onclick="document.getElementById('inventory-outbound-details-{{ $outbound->out_transactionID }}').classList.toggle('hidden')" class="text-blue-300 hover:text-blue-200">
                                        #{{ str_pad($outbound->out_transactionID, 5, '0', STR_PAD_LEFT) }}
                                    </button>
                                </td>
                                <td class="px-6 py-5 text-white/80">{{ $outbound->transaction_date?->format('M d, Y') }}</td>
                                <td class="px-6 py-5 font-medium text-white">{{ $outbound->destination }}</td>
                                <td class="px-6 py-5 text-white/80">{{ $outbound->user->name ?? 'N/A' }}</td>
                                <td class="px-6 py-5 text-center text-white/80">{{ $outbound->outboundLineItems->count() }}</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="rounded-lg px-3 py-1 text-xs font-semibold {{ $outbound->status === 'Transferred' ? 'bg-emerald-500/15 text-emerald-200' : ($outbound->status === 'Approved' ? 'bg-blue-500/15 text-blue-200' : 'bg-amber-500/15 text-amber-200') }}">
                                        {{ $outbound->status ?? 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right font-semibold text-white">&#8369;{{ number_format($outbound->total_amount ?? 0, 2) }}</td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button type="button" onclick="document.getElementById('inventory-outbound-details-{{ $outbound->out_transactionID }}').classList.toggle('hidden')" class="table-action">Details</button>

                                        @if(($outbound->status ?? 'Pending') === 'Pending')
                                            <form action="{{ route('outbound.approve', $outbound) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="table-action action-success">Approve</button>
                                            </form>
                                        @endif

                                        @if(($outbound->status ?? 'Pending') === 'Approved')
                                            <form action="{{ route('outbound.deliver', $outbound) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="table-action action-success">Transfer</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr id="inventory-outbound-details-{{ $outbound->out_transactionID }}" class="hidden bg-slate-950/40">
                                <td colspan="8" class="px-6 py-5">
                                    <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                        <h3 class="mb-3 text-sm font-semibold text-white">Transfer line details</h3>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-left text-xs text-white/80">
                                                <thead class="text-white/80">
                                                    <tr>
                                                        <th class="py-2">Medicine</th>
                                                        <th class="py-2">Batch</th>
                                                        <th class="py-2 text-right">Quantity</th>
                                                        <th class="py-2 text-right">Unit Price</th>
                                                        <th class="py-2 text-right">Line Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($outbound->outboundLineItems as $line)
                                                        <tr class="border-t border-white/10">
                                                            <td class="py-2 text-white">{{ $line->batch->item->name ?? 'Unknown item' }}</td>
                                                            <td class="py-2">{{ $line->batch->lot_number ?? 'N/A' }}</td>
                                                            <td class="py-2 text-right">{{ $line->quantity_dispensed }}</td>
                                                            <td class="py-2 text-right">&#8369;{{ number_format($line->unit_price ?? 0, 2) }}</td>
                                                            <td class="py-2 text-right">&#8369;{{ number_format($line->line_total ?? 0, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="mx-auto max-w-md rounded-2xl border border-white/10 bg-white/5 px-6 py-8">
                                        <p class="text-lg font-semibold text-white">No outbound transactions yet.</p>
                                        <p class="mt-2 text-sm text-white/80">Create a pending outbound transfer when stock needs to move out.</p>
                                        <a href="{{ route('outbound.create') }}" class="btn btn-primary mt-5">Create Outbound</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
