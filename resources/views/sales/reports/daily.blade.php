@extends('layouts.app')

@section('page_title', 'Daily Sales Report')

@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold">Daily Sales Report</h1>
        <p class="text-white/80">Sales completed today</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('reports.daily.pdf') }}" class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-blue-700">
            <i class="fas fa-file-pdf"></i>
            Export PDF
        </a>
        <a href="{{ route('sales.index') }}" class="inline-flex items-center rounded-2xl border border-gray-300 px-5 py-3 text-sm font-medium dark:border-gray-600">
            Back to Sales
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-white/80">Transactions</p>
        <p class="text-3xl font-semibold mt-2">{{ $sales->count() }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-white/80">Net Revenue</p>
        <p class="text-3xl font-semibold mt-2">PHP {{ number_format($total, 2) }}</p>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="text-left p-6 font-medium">Sale ID</th>
                <th class="text-left p-6 font-medium">Time</th>
                <th class="text-left p-6 font-medium">Cashier</th>
                <th class="text-center p-6 font-medium">Items</th>
                <th class="text-right p-6 font-medium">Total</th>
                <th class="text-center p-6 font-medium">Payment</th>
                <th class="text-center p-6 font-medium">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($sales as $sale)
            @php($isReturned = $sale->sale_returns_count > 0)
            <tr onclick="window.location='{{ route('sales.show', $sale) }}'" class="cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-700" title="View sale details">
                <td class="p-6 font-mono">#{{ str_pad($sale->saleID, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="p-6">{{ $sale->sold_at?->format('h:i A') ?? $sale->created_at?->format('h:i A') }}</td>
                <td class="p-6">{{ $sale->user->name ?? '-' }}</td>
                <td class="p-6 text-center">{{ $sale->saleLines->count() }}</td>
                <td class="p-6 text-right font-semibold">
                    @if($isReturned)
                        <span class="text-amber-600 dark:text-amber-200">PHP 0.00</span>
                        <span class="block text-xs font-normal text-white/80 line-through">PHP {{ number_format($sale->total, 2) }}</span>
                    @else
                        PHP {{ number_format($sale->total, 2) }}
                    @endif
                </td>
                <td class="p-6 text-center">{{ $sale->payment_method }}</td>
                <td class="p-6 text-center">
                    @if($isReturned)
                        <span class="status-pill status-warning">Returned</span>
                    @else
                        <span class="status-pill status-success">Saved</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($sales instanceof \Illuminate\Pagination\LengthAwarePaginator || $sales instanceof \Illuminate\Pagination\Paginator)
    <div class="mt-5 flex justify-end">
        {{ $sales->links() }}
    </div>
@endif
@endsection
