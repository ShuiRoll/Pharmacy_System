@extends('layouts.app')

@section('page_title', 'Sale Details')

@section('content')
@php
    $isReturned = $sale->saleReturns->isNotEmpty();
    $return = $sale->saleReturns->first();
    $subtotal = $sale->subtotal ?? $sale->saleLines->sum(fn ($line) => $line->price * $line->quantity);
    $taxAmount = $sale->tax_amount ?? 0;
@endphp

<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h1 class="text-3xl font-bold text-white">Sale #{{ str_pad($sale->saleID, 5, '0', STR_PAD_LEFT) }}</h1>
        <p class="text-white/80">{{ $sale->sold_at?->format('M d, Y - h:i A') ?? $sale->created_at?->format('M d, Y - h:i A') }}</p>
    </div>
    <a href="{{ route('sales.index') }}" class="table-action">Back to Sales</a>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-sm lg:col-span-2">
        <div class="mb-5 flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-white">Order Items</h2>
            @if($isReturned)
                <span class="status-pill status-warning">Returned</span>
            @else
                <span class="status-pill status-success">Saved</span>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-white/10">
            <table class="w-full">
                <thead class="bg-slate-900/50 text-white/80">
                    <tr>
                        <th class="p-4 text-left text-sm font-semibold uppercase tracking-[0.2em]">Medicine</th>
                        <th class="p-4 text-left text-sm font-semibold uppercase tracking-[0.2em]">Batch</th>
                        <th class="p-4 text-center text-sm font-semibold uppercase tracking-[0.2em]">Qty</th>
                        <th class="p-4 text-right text-sm font-semibold uppercase tracking-[0.2em]">Price</th>
                        <th class="p-4 text-right text-sm font-semibold uppercase tracking-[0.2em]">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($sale->saleLines as $line)
                        <tr>
                            <td class="p-4 font-medium text-white">{{ $line->item->name ?? '-' }}</td>
                            <td class="p-4 text-sm text-white/80">{{ $line->batch->lot_number ?? '-' }}</td>
                            <td class="p-4 text-center text-white">{{ $line->quantity }}</td>
                            <td class="p-4 text-right text-white">PHP {{ number_format($line->price, 2) }}</td>
                            <td class="p-4 text-right font-semibold text-white">PHP {{ number_format($line->price * $line->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <aside class="space-y-6">
        <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-sm">
            <h2 class="text-xl font-semibold text-white">Payment</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-white/80">Cashier</dt>
                    <dd class="font-medium text-white">{{ $sale->user->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-white/80">Method</dt>
                    <dd class="font-medium text-white">{{ $sale->payment_method }}</dd>
                </div>
                @if($sale->gcash_reference)
                    <div class="flex justify-between gap-4">
                        <dt class="text-white/80">GCash Ref</dt>
                        <dd class="font-medium text-white">{{ $sale->gcash_reference }}</dd>
                    </div>
                @endif
                @if($sale->card_reference)
                    <div class="flex justify-between gap-4">
                        <dt class="text-white/80">Card Ref</dt>
                        <dd class="font-medium text-white">{{ $sale->card_reference }}</dd>
                    </div>
                @endif
            </dl>
        </section>

        <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-sm">
            <h2 class="text-xl font-semibold text-white">Totals</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-white/80">Subtotal</dt>
                    <dd class="font-medium text-white">PHP {{ number_format($subtotal, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-white/80">VAT</dt>
                    <dd class="font-medium text-white">PHP {{ number_format($taxAmount, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-t border-white/10 pt-3 text-base">
                    <dt class="font-semibold text-white">Original Total</dt>
                    <dd class="font-bold text-white">PHP {{ number_format($sale->total, 2) }}</dd>
                </div>
                @if($isReturned)
                    <div class="flex justify-between gap-4 text-base text-amber-600">
                        <dt class="font-semibold text-white">Report Total</dt>
                        <dd class="font-bold text-white">PHP 0.00</dd>
                    </div>
                @endif
            </dl>
        </section>

        @if($isReturned)
            <section class="rounded-3xl border border-amber-500/20 bg-amber-500/10 p-6">
                <h2 class="text-xl font-semibold text-amber-100">Return Details</h2>
                <dl class="mt-5 space-y-3 text-sm text-amber-50">
                    <div class="flex justify-between gap-4">
                        <dt class="text-amber-200">Return Date</dt>
                        <dd class="font-medium">{{ $return->return_date?->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-amber-200">Processed By</dt>
                        <dd class="font-medium">{{ $return->user->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-amber-200">Reason</dt>
                        <dd class="mt-1 font-medium">{{ $return->reason ?? '-' }}</dd>
                    </div>
                </dl>
            </section>
        @endif
    </aside>
</div>
@endsection
