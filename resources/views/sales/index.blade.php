@extends('layouts.app')

@section('page_title', 'Sales History')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Sales History</h1>
        <p class="text-gray-600 dark:text-gray-400">All completed sales and transactions</p>
    </div>
    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ route('reports.daily') }}"
           class="rounded-2xl border border-gray-300 px-5 py-3 text-sm font-medium dark:border-gray-600">
            Daily Report
        </a>
        <a href="{{ route('reports.monthly') }}"
           class="rounded-2xl border border-gray-300 px-5 py-3 text-sm font-medium dark:border-gray-600">
            Monthly Report
        </a>
        <a href="{{ route('sales.create') }}"
           class="flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">
            <i class="fas fa-plus"></i> New Sale
        </a>
    </div>
</div>

<div class="overflow-hidden rounded-3xl bg-white shadow-sm dark:bg-gray-800">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="p-6 text-left font-medium">Sale ID</th>
                <th class="p-6 text-left font-medium">Date & Time</th>
                <th class="p-6 text-left font-medium">Cashier</th>
                <th class="p-6 text-center font-medium">Items</th>
                <th class="p-6 text-right font-medium">Total Amount</th>
                <th class="p-6 text-center font-medium">Payment Method</th>
                <th class="p-6 text-center font-medium">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($sales as $sale)
                <tr onclick="window.location='{{ route('sales.show', $sale) }}'" class="cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-700" title="View sale details">
                    <td class="p-6 font-mono">#{{ str_pad($sale->saleID, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="p-6">{{ $sale->sold_at?->format('M d, Y - h:i A') ?? $sale->created_at?->format('M d, Y - h:i A') }}</td>
                    <td class="p-6">{{ $sale->user->name ?? '-' }}</td>
                    <td class="p-6 text-center">{{ $sale->saleLines->count() }}</td>
                    <td class="p-6 text-right font-semibold">PHP {{ number_format($sale->total, 2) }}</td>
                    <td class="p-6 text-center">
                        <span class="rounded-full bg-gray-100 px-4 py-1 text-xs dark:bg-gray-700">{{ $sale->payment_method }}</span>
                    </td>
                    <td class="p-6 text-center">
                        @if($sale->sale_returns_count > 0)
                            <span class="rounded-full bg-amber-100 px-4 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/15 dark:text-amber-200">Returned</span>
                        @else
                            <span class="rounded-full bg-emerald-100 px-4 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-200">Saved</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-10 text-center text-sm text-gray-500 dark:text-gray-400">
                        No completed sales yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
