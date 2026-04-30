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
        <h1 class="text-3xl font-bold">Sale #{{ str_pad($sale->saleID, 5, '0', STR_PAD_LEFT) }}</h1>
        <p class="text-gray-600 dark:text-gray-400">{{ $sale->sold_at?->format('M d, Y - h:i A') ?? $sale->created_at?->format('M d, Y - h:i A') }}</p>
    </div>
    <a href="{{ route('sales.index') }}" class="rounded-2xl border border-gray-300 px-5 py-3 text-sm font-medium dark:border-gray-600">
        Back to Sales
    </a>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <section class="rounded-3xl bg-white p-6 shadow-sm dark:bg-gray-800 lg:col-span-2">
        <div class="mb-5 flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold">Order Items</h2>
            @if($isReturned)
                <span class="rounded-full bg-amber-100 px-4 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/15 dark:text-amber-200">Returned</span>
            @else
                <span class="rounded-full bg-emerald-100 px-4 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-200">Saved</span>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="p-4 text-left text-sm font-medium">Medicine</th>
                        <th class="p-4 text-left text-sm font-medium">Batch</th>
                        <th class="p-4 text-center text-sm font-medium">Qty</th>
                        <th class="p-4 text-right text-sm font-medium">Price</th>
                        <th class="p-4 text-right text-sm font-medium">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($sale->saleLines as $line)
                        <tr>
                            <td class="p-4 font-medium">{{ $line->item->name ?? '-' }}</td>
                            <td class="p-4 text-sm text-gray-500 dark:text-gray-400">{{ $line->batch->lot_number ?? '-' }}</td>
                            <td class="p-4 text-center">{{ $line->quantity }}</td>
                            <td class="p-4 text-right">PHP {{ number_format($line->price, 2) }}</td>
                            <td class="p-4 text-right font-semibold">PHP {{ number_format($line->price * $line->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <aside class="space-y-6">
        <section class="rounded-3xl bg-white p-6 shadow-sm dark:bg-gray-800">
            <h2 class="text-xl font-semibold">Payment</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500 dark:text-gray-400">Cashier</dt>
                    <dd class="font-medium">{{ $sale->user->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500 dark:text-gray-400">Method</dt>
                    <dd class="font-medium">{{ $sale->payment_method }}</dd>
                </div>
                @if($sale->gcash_reference)
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">GCash Ref</dt>
                        <dd class="font-medium">{{ $sale->gcash_reference }}</dd>
                    </div>
                @endif
                @if($sale->card_reference)
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">Card Ref</dt>
                        <dd class="font-medium">{{ $sale->card_reference }}</dd>
                    </div>
                @endif
            </dl>
        </section>

        <section class="rounded-3xl bg-white p-6 shadow-sm dark:bg-gray-800">
            <h2 class="text-xl font-semibold">Totals</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500 dark:text-gray-400">Subtotal</dt>
                    <dd class="font-medium">PHP {{ number_format($subtotal, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500 dark:text-gray-400">VAT</dt>
                    <dd class="font-medium">PHP {{ number_format($taxAmount, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-t border-gray-200 pt-3 text-base dark:border-gray-700">
                    <dt class="font-semibold">Original Total</dt>
                    <dd class="font-bold">PHP {{ number_format($sale->total, 2) }}</dd>
                </div>
                @if($isReturned)
                    <div class="flex justify-between gap-4 text-base text-amber-600 dark:text-amber-200">
                        <dt class="font-semibold">Report Total</dt>
                        <dd class="font-bold">PHP 0.00</dd>
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
