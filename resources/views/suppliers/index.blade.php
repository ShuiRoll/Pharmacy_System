@extends('layouts.app')

@section('page_title', 'Suppliers')

@section('content')
@php
    $supplierCount = $suppliers->count();
    $pendingOrders = $purchaseOrders->where('status', 'Pending')->count();
    $receivingCount = $inbounds->count();
@endphp

<div class="space-y-8 text-left" style="text-align: left;" data-filter-scope>
    <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-xl shadow-black/20 backdrop-blur">
        <div class="mb-6 flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.3em] text-white">
            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-slate-300">Filter</span>
            <button type="button" data-section-filter="all" class="rounded-full border border-blue-500/50 bg-blue-600 px-3 py-1 text-white transition hover:border-blue-400 hover:text-blue-100">All</button>
            <button type="button" data-section-filter="suppliers" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-slate-300 transition hover:border-blue-400 hover:text-blue-200">Directory</button>
            <button type="button" data-section-filter="purchasing" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-slate-300 transition hover:border-blue-400 hover:text-blue-200">Purchasing</button>
            <button type="button" data-section-filter="receiving" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-slate-300 transition hover:border-blue-400 hover:text-blue-200">Receiving</button>
        </div>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">Suppliers</h1>
                <p class="mt-2 text-sm text-white">Manage supplier records, purchasing, and stock flows.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center rounded-full border border-blue-500/50 bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"><i class="fas fa-plus mr-1.5"></i>Add Supplier</a>
                <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-medium text-white transition hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-blue-200">New PO</a>
                <a href="{{ route('inbound.create') }}" class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-medium text-white transition hover:border-emerald-400/40 hover:bg-emerald-500/10 hover:text-emerald-200">New Receipt</a>
            </div>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white">Suppliers</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $supplierCount }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white">Pending PO</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $pendingOrders }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-white">Receipts</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $receivingCount }}</p>
            </div>
        </div>
    </section>

    <section id="suppliers" data-filter-section="suppliers" class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-xl shadow-black/20 backdrop-blur">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-white">Supplier directory</h2>
                <p class="text-sm text-white">Contact details and quick management actions</p>
            </div>
            <a href="{{ route('suppliers.create') }}" class="rounded-full border border-blue-500/50 bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Add Supplier</a>
        </div>

        <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse($suppliers as $supplier)
                <div class="rounded-2xl border border-white/10 bg-white/5 p-5 transition hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ $supplier->supplier_name }}</h3>
                            <p class="mt-1 text-sm text-white">{{ $supplier->contact_person ?? 'No contact person' }}</p>
                        </div>
                        <span class="mt-1 inline-flex h-3 w-3 rounded-full bg-emerald-400"></span>
                    </div>

                    <div class="mt-4 space-y-2 text-sm text-slate-300">
                        <p class="text-white">{{ $supplier->contact_number ?: 'No contact number' }}</p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="flex-1 rounded-full border border-white/10 px-4 py-2 text-center text-sm font-medium text-white transition hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-blue-200">Edit</a>
                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete supplier?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-full px-4 py-2 text-sm font-medium text-rose-300 transition hover:bg-rose-500/10">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-white/20 p-6 text-sm text-white">No suppliers yet.</div>
            @endforelse
        </div>
    </section>

    <section id="purchasing" data-filter-section="purchasing" class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 text-left shadow-xl shadow-black/20 backdrop-blur" style="text-align: left;">
        <div class="flex flex-col items-start gap-4 border-b border-white/10 px-6 py-5 sm:flex-row sm:justify-between">
            <div class="text-left" style="text-align: left;">
                <h2 class="text-xl font-semibold text-white">Purchasing</h2>
                <p class="text-sm text-white">Purchase orders by supplier</p>
            </div>
            <a href="{{ route('purchase-orders.create') }}" class="self-start rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white transition hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-blue-200">New Purchase Order</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" style="text-align: left;">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-slate-800 dark:bg-slate-800/70 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold">PO Number</th>
                        <th class="px-6 py-4 font-semibold">Supplier</th>
                        <th class="px-6 py-4 font-semibold">PO Date</th>
                        <th class="px-6 py-4 font-semibold">Expected</th>
                        <th class="px-6 py-4 text-right font-semibold">Total</th>
                        <th class="px-6 py-4 text-center font-semibold">Status</th>
                        <th class="px-6 py-4 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($purchaseOrders as $po)
                        <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/60">
                            <td class="px-6 py-5 font-mono text-xs font-semibold text-slate-800 dark:text-white">PO-{{ str_pad($po->poID, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-5 text-slate-800 dark:text-white">{{ $po->supplier->supplier_name ?? '—' }}</td>
                            <td class="px-6 py-5 text-slate-700 dark:text-white">{{ $po->po_date ? $po->po_date->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-5 text-slate-700 dark:text-white">{{ $po->expected_date ? $po->expected_date->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-5 text-right font-semibold text-slate-800 dark:text-white">₱{{ number_format($po->total_amount ?? 0, 2) }}</td>
                            <td class="px-6 py-5 text-center">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $po->status == 'Received' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-200' : ($po->status == 'Approved' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-200' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-200') }}">{{ $po->status }}</span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('purchase-orders.edit', $po) }}" class="font-medium text-blue-600 transition hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-200">Edit</a>
                                    @if($po->status === 'Pending')
                                        <form action="{{ route('purchase-orders.approve', $po) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="font-medium text-emerald-600 transition hover:text-emerald-700 dark:text-emerald-300 dark:hover:text-emerald-200">Approve</button>
                                        </form>
                                        <form action="{{ route('purchase-orders.reject', $po) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="font-medium text-amber-600 transition hover:text-amber-700 dark:text-amber-300 dark:hover:text-amber-200">Reject</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('purchase-orders.destroy', $po) }}" method="POST" onsubmit="return confirm('Delete this purchase order?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-rose-600 transition hover:text-rose-700 dark:text-rose-300 dark:hover:text-rose-200">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-slate-700 dark:text-white">No purchase orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section id="receiving" data-filter-section="receiving" class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 text-left shadow-xl shadow-black/20 backdrop-blur" style="text-align: left;">
        <div class="flex flex-col items-start gap-4 border-b border-white/10 px-6 py-5 sm:flex-row sm:justify-between">
            <div class="text-left" style="text-align: left;">
                <h2 class="text-xl font-semibold text-white">Receiving</h2>
                <p class="text-sm text-slate-300">Inbound receipts from suppliers</p>
            </div>
            <a href="{{ route('inbound.create') }}" class="self-start rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-emerald-400/40 hover:bg-emerald-500/10 hover:text-emerald-200">New Receipt</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" style="text-align: left;">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-slate-800 dark:bg-slate-800/70 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Receipt</th>
                        <th class="px-6 py-4 font-semibold">PO</th>
                        <th class="px-6 py-4 font-semibold">Supplier</th>
                        <th class="px-6 py-4 font-semibold">Date Received</th>
                        <th class="px-6 py-4 font-semibold">Received By</th>
                        <th class="px-6 py-4 text-center font-semibold">Quality</th>
                        <th class="px-6 py-4 text-right font-semibold">Total</th>
                        <th class="px-6 py-4 text-center font-semibold">Items</th>
                        <th class="px-6 py-4 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($inbounds as $inbound)
                        <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/60">
                            <td class="px-6 py-5 font-mono text-xs font-semibold text-slate-600 dark:text-slate-300">
                                <button type="button" onclick="document.getElementById('receipt-details-{{ $inbound->in_transactionID }}').classList.toggle('hidden')" class="text-blue-300 hover:text-blue-200">
                                    #{{ str_pad($inbound->in_transactionID, 5, '0', STR_PAD_LEFT) }}
                                </button>
                            </td>
                            <td class="px-6 py-5 text-slate-900 dark:text-white">{{ $inbound->poID ? 'PO-'.str_pad($inbound->poID, 4, '0', STR_PAD_LEFT) : 'Direct' }}</td>
                            <td class="px-6 py-5 text-slate-900 dark:text-white">{{ $inbound->purchaseOrder->supplier->supplier_name ?? 'Direct receipt' }}</td>
                            <td class="px-6 py-5 text-slate-500 dark:text-slate-400">{{ $inbound->date_received?->format('M d, Y') }}</td>
                            <td class="px-6 py-5 text-slate-900 dark:text-white">{{ $inbound->user->name ?? '—' }}</td>
                            <td class="px-6 py-5 text-center"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $inbound->quality_status == 'Passed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-200' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-200' }}">{{ $inbound->quality_status }}</span></td>
                            <td class="px-6 py-5 text-right font-semibold text-slate-900 dark:text-white">₱{{ number_format($inbound->total_cost ?? 0, 2) }}</td>
                            <td class="px-6 py-5 text-center text-slate-500 dark:text-slate-400">{{ $inbound->inboundLineItems->count() }}</td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button type="button" onclick="document.getElementById('receipt-details-{{ $inbound->in_transactionID }}').classList.toggle('hidden')" class="font-medium text-blue-300 hover:text-blue-200">Details</button>
                                    @if($inbound->quality_status === 'Pending')
                                        <a href="{{ route('inbound.edit', $inbound) }}" class="font-medium text-emerald-300 hover:text-emerald-200">Edit</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr id="receipt-details-{{ $inbound->in_transactionID }}" class="hidden bg-slate-950/40">
                            <td colspan="9" class="px-6 py-5">
                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <h3 class="mb-3 text-sm font-semibold text-white">Receipt line details</h3>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left text-xs text-slate-300">
                                            <thead class="text-slate-400">
                                                <tr>
                                                    <th class="py-2">Medicine</th>
                                                    <th class="py-2">Lot</th>
                                                    <th class="py-2">Expiry</th>
                                                    <th class="py-2 text-right">Quantity</th>
                                                    <th class="py-2 text-right">Unit Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($inbound->inboundLineItems as $line)
                                                    <tr class="border-t border-white/10">
                                                        <td class="py-2 text-white">{{ $line->item->name ?? 'Unknown item' }}</td>
                                                        <td class="py-2">{{ $line->lot_number ?? 'N/A' }}</td>
                                                        <td class="py-2">{{ $line->expiration_date ? \Illuminate\Support\Carbon::parse($line->expiration_date)->format('M d, Y') : 'N/A' }}</td>
                                                        <td class="py-2 text-right">{{ $line->quantity_received }}</td>
                                                        <td class="py-2 text-right">₱{{ number_format($line->unit_cost ?? 0, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">No receipts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if(false)
    <section id="outbound" data-filter-section="outbound" class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 text-left shadow-xl shadow-black/20 backdrop-blur" style="text-align: left;">
        <div class="flex flex-col items-start gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-800 sm:flex-row sm:justify-between">
            <div class="text-left" style="text-align: left;">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Outbound</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Transfering of stocks to a branch.</p>
            </div>
            <a href="{{ route('outbound.create') }}" class="self-start rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-rose-500/40 dark:hover:bg-rose-500/10 dark:hover:text-rose-200">New Outbound</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" style="text-align: left;">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-slate-800 dark:bg-slate-800/70 dark:text-slate-400">
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
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($outbounds as $outbound)
                        <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/60">
                            <td class="px-6 py-5 font-mono text-xs font-semibold text-slate-600 dark:text-slate-300">#{{ str_pad($outbound->out_transactionID, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-5 text-slate-500 dark:text-slate-400">{{ $outbound->transaction_date?->format('M d, Y') }}</td>
                            <td class="px-6 py-5 text-slate-900 dark:text-white">{{ $outbound->destination }}</td>
                            <td class="px-6 py-5 text-slate-900 dark:text-white">{{ $outbound->user->name ?? '—' }}</td>
                            <td class="px-6 py-5 text-center text-slate-500 dark:text-slate-400">{{ $outbound->outboundLineItems->count() }}</td>
                            <td class="px-6 py-5 text-center">
                                <span class="rounded-lg px-3 py-1 text-xs font-semibold {{ $outbound->status === 'Transferred' ? 'bg-emerald-500/15 text-emerald-200' : ($outbound->status === 'Approved' ? 'bg-blue-500/15 text-blue-200' : 'bg-amber-500/15 text-amber-200') }}">
                                    {{ $outbound->status ?? 'Pending' }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right font-semibold text-slate-900 dark:text-white">₱{{ number_format($outbound->total_amount ?? 0, 2) }}</td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if(($outbound->status ?? 'Pending') === 'Pending')
                                        <form action="{{ route('outbound.approve', $outbound) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="font-medium text-blue-300 hover:text-blue-200">Approve</button>
                                        </form>
                                    @endif
                                    @if(($outbound->status ?? 'Pending') === 'Approved')
                                        <form action="{{ route('outbound.deliver', $outbound) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="font-medium text-emerald-300 hover:text-emerald-200">Transfer</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">No outbound transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @endif
</div>
@endsection
