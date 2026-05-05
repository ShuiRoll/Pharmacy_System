@extends('layouts.app')

@section('page_title', 'Suppliers')

@section('content')
@php
    $supplierCount = $suppliers->count();
    $pendingOrders = $pendingOrdersCount ?? 0;
    $receivingCount = $inbounds->count();
@endphp

<div class="space-y-8 text-left" style="text-align: left;" data-filter-scope>
    <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-xl shadow-black/20 backdrop-blur">
        <div class="mb-6 flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.3em] text-white">
            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80">Filter</span>
            <button type="button" data-section-filter="all" class="rounded-full border border-blue-500/50 bg-blue-600 px-3 py-1 text-white transition hover:border-blue-400 hover:text-blue-100">All</button>
            <button type="button" data-section-filter="suppliers" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80 transition hover:border-blue-400 hover:text-white">Directory</button>
            <button type="button" data-section-filter="purchasing" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80 transition hover:border-blue-400 hover:text-white">Purchasing</button>
            <button type="button" data-section-filter="receiving" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80 transition hover:border-blue-400 hover:text-white">Receiving</button>
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

                    <div class="mt-4 space-y-2 text-sm text-white/80">
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
                <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-[0.2em] text-white/80">
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
                <tbody class="divide-y divide-white/10">
                    @forelse($purchaseOrders as $po)
                        <tr class="transition hover:bg-white/5">
                            <td class="px-6 py-5 font-mono text-xs font-semibold text-white">PO-{{ str_pad($po->poID, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-5 text-white">{{ $po->supplier->supplier_name ?? '—' }}</td>
                            <td class="px-6 py-5 text-white/80">{{ $po->po_date ? $po->po_date->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-5 text-white/80">{{ $po->expected_date ? $po->expected_date->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-5 text-right font-semibold text-white">₱{{ number_format($po->total_amount ?? 0, 2) }}</td>
                            <td class="px-6 py-5 text-center">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $po->status == 'Received' ? 'bg-emerald-500/20 text-emerald-100 ring-1 ring-emerald-400/30' : ($po->status == 'Approved' ? 'bg-blue-500/20 text-blue-100 ring-1 ring-blue-400/30' : 'bg-amber-500/20 text-amber-100 ring-1 ring-amber-400/30') }}">{{ $po->status }}</span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button type="button" onclick="document.getElementById('po-details-{{ $po->poID }}').classList.toggle('hidden')" class="rounded-full border border-blue-400/40 bg-blue-500/10 px-3 py-1.5 text-xs font-semibold text-blue-100 transition hover:border-blue-300/70 hover:bg-blue-500/20">
                                        View Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr id="po-details-{{ $po->poID }}" class="hidden bg-slate-950/35">
                            <td colspan="7" class="px-6 py-5">
                                <div class="flex flex-col gap-4 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="text-sm text-white">
                                        <p class="font-semibold text-white">PO-{{ str_pad($po->poID, 4, '0', STR_PAD_LEFT) }} details</p>
                                        <p class="mt-1 text-white/80">Manage approval flow and order updates from one panel.</p>
                                    </div>

                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('purchase-orders.edit', $po) }}" class="rounded-full border border-blue-400/40 bg-blue-500/10 px-4 py-2 text-xs font-semibold text-blue-100 transition hover:border-blue-300/70 hover:bg-blue-500/20">Edit</a>
                                        @if($po->status === 'Pending')
                                            <form action="{{ route('purchase-orders.approve', $po) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-full border border-emerald-400/40 bg-emerald-500/10 px-4 py-2 text-xs font-semibold text-emerald-100 transition hover:border-emerald-300/70 hover:bg-emerald-500/20">Accept</button>
                                            </form>
                                            <form action="{{ route('purchase-orders.reject', $po) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-full border border-amber-400/40 bg-amber-500/10 px-4 py-2 text-xs font-semibold text-amber-100 transition hover:border-amber-300/70 hover:bg-amber-500/20">Reject</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('purchase-orders.destroy', $po) }}" method="POST" onsubmit="return confirm('Delete this purchase order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full border border-rose-400/40 bg-rose-500/10 px-4 py-2 text-xs font-semibold text-rose-100 transition hover:border-rose-300/70 hover:bg-rose-500/20">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-white/80">No purchase orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchaseOrders instanceof \Illuminate\Pagination\LengthAwarePaginator && $purchaseOrders->hasPages())
            <div class="border-t border-white/10 px-6 py-4">
                <div class="flex items-center justify-end gap-2 text-xs font-semibold text-white/80 sm:text-sm">
                    <span class="mr-2">Page {{ $purchaseOrders->currentPage() }} of {{ $purchaseOrders->lastPage() }}</span>

                    @if($purchaseOrders->onFirstPage())
                        <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-white/70">Prev</span>
                    @else
                        <a href="{{ $purchaseOrders->previousPageUrl() }}" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-white transition hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white">Prev</a>
                    @endif

                    @for($page = 1; $page <= $purchaseOrders->lastPage(); $page++)
                        @if($page === $purchaseOrders->currentPage())
                            <span class="rounded-lg border border-blue-500/50 bg-blue-600 px-3 py-1.5 text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $purchaseOrders->url($page) }}" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-white transition hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white">{{ $page }}</a>
                        @endif
                    @endfor

                    @if($purchaseOrders->hasMorePages())
                        <a href="{{ $purchaseOrders->nextPageUrl() }}" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-white transition hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white">Next</a>
                    @else
                        <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-white/70">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </section>

    <section id="receiving" data-filter-section="receiving" class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 text-left shadow-xl shadow-black/20 backdrop-blur" style="text-align: left;">
        <div class="flex flex-col items-start gap-4 border-b border-white/10 px-6 py-5 sm:flex-row sm:justify-between">
            <div class="text-left" style="text-align: left;">
                <h2 class="text-xl font-semibold text-white">Receiving</h2>
                <p class="text-sm text-white/80">Inbound receipts from suppliers</p>
            </div>
            <a href="{{ route('inbound.create') }}" class="self-start rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white transition hover:border-emerald-400/40 hover:bg-emerald-500/10 hover:text-white">New Receipt</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" style="text-align: left;">
                <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-[0.2em] text-white/80">
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
                <tbody class="divide-y divide-white/10">
                    @forelse($inbounds as $inbound)
                        <tr class="transition hover:bg-white/5">
                            <td class="px-6 py-5 font-mono text-xs font-semibold text-white/80">
                                <button type="button" onclick="document.getElementById('receipt-details-{{ $inbound->in_transactionID }}').classList.toggle('hidden')" class="table-action" style="padding: 0.4rem 0.75rem; font-size: 0.75rem; min-height: auto;">
                                    #{{ str_pad($inbound->in_transactionID, 5, '0', STR_PAD_LEFT) }}
                                </button>
                            </td>
                            <td class="px-6 py-5 text-white">{{ $inbound->poID ? 'PO-'.str_pad($inbound->poID, 4, '0', STR_PAD_LEFT) : 'Direct' }}</td>
                            <td class="px-6 py-5 text-white">{{ $inbound->purchaseOrder->supplier->supplier_name ?? 'Direct receipt' }}</td>
                            <td class="px-6 py-5 text-white/80">{{ $inbound->date_received?->format('M d, Y') }}</td>
                            <td class="px-6 py-5 text-white">{{ $inbound->user->name ?? '—' }}</td>
                            <td class="px-6 py-5 text-center"><span class="status-pill {{ $inbound->quality_status == 'Passed' ? 'status-success' : 'status-warning' }}">{{ $inbound->quality_status }}</span></td>
                            <td class="px-6 py-5 text-right font-semibold text-white">₱{{ number_format($inbound->total_cost ?? 0, 2) }}</td>
                            <td class="px-6 py-5 text-center text-white/80">{{ $inbound->inboundLineItems->count() }}</td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="document.getElementById('receipt-details-{{ $inbound->in_transactionID }}').classList.toggle('hidden')" class="table-action">Details</button>
                                    @if($inbound->quality_status === 'Pending')
                                        <a href="{{ route('inbound.edit', $inbound) }}" class="table-action action-success">Edit</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr id="receipt-details-{{ $inbound->in_transactionID }}" class="hidden bg-slate-950/40">
                            <td colspan="9" class="px-6 py-5">
                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <h3 class="mb-3 text-sm font-semibold text-white">Receipt line details</h3>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left text-xs text-white/80">
                                            <thead class="text-white/60">
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
                        <tr><td colspan="9" class="px-6 py-10 text-center text-sm text-white/70">No receipts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if(false)
    <section id="outbound" data-filter-section="outbound" class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 text-left shadow-xl shadow-black/20 backdrop-blur" style="text-align: left;">
                <div class="flex flex-col items-start gap-4 border-b border-white/10 px-6 py-5 sm:flex-row sm:justify-between">
            <div class="text-left" style="text-align: left;">
                <h2 class="text-xl font-semibold text-white">Outbound</h2>
                <p class="text-sm text-white/80">Transfering of stocks to a branch.</p>
            </div>
            <a href="{{ route('outbound.create') }}" class="table-action">New Outbound</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" style="text-align: left;">
                <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-[0.2em] text-white/80">
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
                            <td class="px-6 py-5 font-mono text-xs font-semibold text-white/80">#{{ str_pad($outbound->out_transactionID, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-5 text-white/80">{{ $outbound->transaction_date?->format('M d, Y') }}</td>
                            <td class="px-6 py-5 text-white">{{ $outbound->destination }}</td>
                            <td class="px-6 py-5 text-white">{{ $outbound->user->name ?? '—' }}</td>
                            <td class="px-6 py-5 text-center text-white/80">{{ $outbound->outboundLineItems->count() }}</td>
                            <td class="px-6 py-5 text-center">
                                <span class="rounded-lg px-3 py-1 text-xs font-semibold {{ $outbound->status === 'Transferred' ? 'bg-emerald-500/15 text-emerald-200' : ($outbound->status === 'Approved' ? 'bg-blue-500/15 text-blue-200' : 'bg-amber-500/15 text-amber-200') }}">
                                    {{ $outbound->status ?? 'Pending' }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right font-semibold text-white">₱{{ number_format($outbound->total_amount ?? 0, 2) }}</td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(($outbound->status ?? 'Pending') === 'Pending')
                                        <form action="{{ route('outbound.approve', $outbound) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="table-action action-success">Approve</button>
                                        </form>
                                    @endif
                                    @if(($outbound->status ?? 'Pending') === 'Approved')
                                        <form action="{{ route('outbound.deliver', $outbound) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="table-action action-success">Transfer</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-10 text-center text-sm text-white/80">No outbound transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @endif
</div>
@endsection
