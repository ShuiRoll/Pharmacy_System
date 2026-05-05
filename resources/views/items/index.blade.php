@extends('layouts.app')

@section('page_title', 'Items')

@section('content')
@php
    $totalStock = $items->sum(fn ($item) => $item->inventoryBatches->sum('current_quantity'));
    $lowStockCount = $items->filter(function ($item) {
        return $item->inventoryBatches->sum('current_quantity') < $item->minimum_stock_lvl;
    })->count();
@endphp

<div class="space-y-8" data-filter-scope>
    <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-xl shadow-black/20 backdrop-blur">
        <div class="mb-6 flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.3em] text-white">
            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80">Filter</span>
            <button type="button" data-section-filter="all" class="rounded-full border border-blue-500/50 bg-blue-600 px-3 py-1 text-white transition hover:border-blue-400 hover:text-blue-100">All</button>
            <button type="button" data-section-filter="items" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80 transition hover:border-blue-400 hover:text-white">Items</button>
            <button type="button" data-section-filter="locations" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80 transition hover:border-blue-400 hover:text-white">Locations</button>
        </div>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">Items</h1>
                <p class="mt-2 text-sm text-white">Track medicines, stock batches, and storage locations in one workspace.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('items.low-stock') }}" class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-medium text-white transition hover:border-amber-400/40 hover:bg-amber-500/10 hover:text-amber-200">Low Stock</a>
                <a href="{{ route('items.near-expiry') }}" class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-medium text-white transition hover:border-orange-400/40 hover:bg-orange-500/10 hover:text-orange-200">Near Expiry</a>
                <a href="{{ route('items.create') }}" class="inline-flex items-center rounded-full border border-blue-500/50 bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"><i class="fas fa-plus mr-1.5"></i>Add Item</a>
            </div>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white">Items</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $items->count() }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white">Locations</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $locations->count() }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white">Total Stock</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $totalStock }}</p>
            </div>
        </div>
    </section>

    <section id="items" class="space-y-8">
        <div data-filter-section="items" class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-xl shadow-black/20 backdrop-blur">
            <div class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-800 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-white">Items</h2>
                    <p class="text-sm text-white/80">Stock, batches, and expiry status</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('items.low-stock') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-white transition hover:border-amber-200 hover:text-white">Low Stock</a>
                    <a href="{{ route('items.near-expiry') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-white transition hover:border-orange-200 hover:text-white">Near Expiry</a>
                </div>
            </div>

            <div class="border-b border-white/10 px-6 py-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-white/80"></i>
                    <input type="text" id="search" placeholder="Search medicine or SKU..." class="w-full rounded-xl border border-white/10 bg-white/5 py-2 pl-9 pr-3 text-sm text-white outline-none transition placeholder:text-white/60 focus:border-blue-400/50 focus:bg-white/10 focus:ring-4 focus:ring-blue-500/10">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-[0.2em] text-white/80">
                        <tr>
                            <th class="px-6 py-4 font-semibold">SKU</th>
                            <th class="px-6 py-4 font-semibold">Medicine</th>
                            <th class="px-6 py-4 font-semibold">Category</th>
                            <th class="px-6 py-4 font-semibold">Location</th>
                            <th class="px-6 py-4 text-center font-semibold">Stock</th>
                            <th class="px-6 py-4 text-center font-semibold">Reorder</th>
                            <th class="px-6 py-4 text-center font-semibold">Status</th>
                            <th class="px-6 py-4 text-center font-semibold">Earliest Expiry</th>
                            <th class="px-6 py-4 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($items as $item)
                            @php
                                $stock = $item->inventoryBatches->sum('current_quantity');
                                $earliestBatch = $item->inventoryBatches->whereNotNull('expiration_date')->sortBy('expiration_date')->first();
                            @endphp
                            <tr class="transition hover:bg-white/5">
                                <td class="px-6 py-5 font-mono text-xs font-semibold text-white">{{ $item->item_code }}</td>
                                    <td class="px-6 py-5 font-medium text-white">{{ $item->name }}</td>
                                <td class="px-6 py-5 text-white/80">{{ $item->category ?? '—' }}</td>
                                <td class="px-6 py-5 text-white/80">{{ $item->location->name ?? '—' }}</td>
                                <td class="px-6 py-5 text-center font-semibold text-white">{{ $stock }}</td>
                                <td class="px-6 py-5 text-center text-white/80">{{ $item->minimum_stock_lvl }}</td>
                                <td class="px-6 py-5 text-center">
                                    @if($stock == 0)
                                        <span class="status-pill status-danger">No Stock</span>
                                    @elseif($stock < $item->minimum_stock_lvl)
                                        <span class="status-pill status-warning">Low</span>
                                    @else
                                        <span class="status-pill status-success">Good</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center text-white/80">{{ $earliestBatch?->expiration_date?->format('M Y') ?? '—' }}</td>
                                <td class="px-6 py-5 text-right">
                                    <div class="relative inline-block group">
                                        <a href="{{ route('items.show', $item) }}" class="table-action">Details</a>
                                        <div class="absolute right-0 top-full mt-1 w-28 bg-slate-800 border border-white/10 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                            <a href="{{ route('items.edit', $item) }}" class="block px-3 py-2 text-xs font-medium text-white hover:bg-white/5 first:rounded-t-lg">Edit</a>
                                            <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this item?');" class="block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left px-3 py-2 text-xs font-medium text-rose-200 hover:bg-white/5 last:rounded-b-lg">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <aside id="locations" data-filter-section="locations" class="space-y-6">
            <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-xl shadow-black/20 backdrop-blur">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Locations</h2>
                        <p class="text-sm text-white/80">Storage areas connected to inventory batches</p>
                    </div>
                    <a href="{{ route('locations.create') }}" class="rounded-full border border-blue-500/50 bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Add Location</a>
                </div>

                <div class="mt-6 space-y-3">
                    @forelse($locations as $location)
                        @php($locationStock = $location->inventoryBatches->sum('current_quantity'))
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-white">{{ $location->name }}</p>
                                    <p class="mt-1 text-sm text-white/80">{{ $location->inventoryBatches->count() }} batches linked</p>
                                </div>
                                <span class="rounded-full bg-blue-500/15 px-3 py-1 text-xs font-semibold text-blue-200">{{ $locationStock }} units</span>
                            </div>

                            <div class="mt-4 flex items-center gap-3">
                                <a href="{{ route('locations.edit', $location) }}" class="table-action">Edit</a>
                                <form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Delete this location?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="table-action action-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-white/20 p-6 text-sm text-white/80">No locations yet.</div>
                    @endforelse
                </div>
            </div>
        </aside>

        @if(false)
        <div id="outbound" data-filter-section="outbound" class="space-y-8">
            <section class="app-panel p-6 sm:p-8">
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
            </section>

            <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-xl shadow-black/20 backdrop-blur">
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
                                            <button type="button" onclick="document.getElementById('inventory-outbound-details-{{ $outbound->out_transactionID }}').classList.toggle('hidden')" class="font-medium text-blue-300 hover:text-blue-200">Details</button>

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
            </section>
        </div>
        @endif
    </section>
</div>
@endsection
