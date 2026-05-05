@extends('layouts.app')

@section('page_title', 'Outbound Transactions')

@section('content')
@php
    $pendingCount = $outbounds->where('status', 'Pending')->count();
    $approvedCount = $outbounds->where('status', 'Approved')->count();
    $transferredCount = $outbounds->where('status', 'Transferred')->count();
    $totalAmount = $outbounds->sum('total_amount');
@endphp

<div class="space-y-8">
    <section class="app-panel p-6 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">Outbound Transactions</h1>
                <p class="mt-2 text-sm text-white/80">Manage stock transfers from pending approval to completed transfer.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('suppliers.index') }}#outbound" class="btn btn-secondary">Supplier Workspace</a>
                <a href="{{ route('outbound.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    New Outbound
                </a>
            </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="app-card p-5">
                <p class="text-sm text-white/80">Pending</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $pendingCount }}</p>
            </div>
            <div class="app-card p-5">
                <p class="text-sm text-white/80">Approved</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $approvedCount }}</p>
            </div>
            <div class="app-card p-5">
                <p class="text-sm text-white/80">Transferred</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $transferredCount }}</p>
            </div>
            <div class="app-card p-5">
                <p class="text-sm text-white/80">Total Amount</p>
                <p class="mt-2 text-3xl font-semibold text-white">&#8369;{{ number_format($totalAmount, 2) }}</p>
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
                                <button type="button" onclick="document.getElementById('outbound-details-{{ $outbound->out_transactionID }}').classList.toggle('hidden')" class="text-blue-300 hover:text-blue-200">
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
                                    <button type="button" onclick="document.getElementById('outbound-details-{{ $outbound->out_transactionID }}').classList.toggle('hidden')" class="font-medium text-blue-300 hover:text-blue-200">Details</button>

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
                        <tr id="outbound-details-{{ $outbound->out_transactionID }}" class="hidden bg-slate-950/40">
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
@endsection
