@extends('layouts.app')

@section('page_title', 'Sales History')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-white">Sales History</h1>
        <p class="text-white/80">All completed sales and transactions</p>
    </div>
    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ route('reports.daily') }}" class="table-action">Daily Report</a>
        <a href="{{ route('reports.monthly') }}" class="table-action">Monthly Report</a>
        <a href="{{ route('sales.create') }}"
           class="flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">
            <i class="fas fa-plus"></i> New Sale
        </a>
    </div>
</div>

<div class="overflow-hidden rounded-3xl bg-white shadow-sm dark:bg-gray-800">
    <table class="w-full">
        <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-[0.2em] text-white/80">
            <tr class="border-b">
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
                <tr onclick="window.location='{{ route('sales.show', $sale) }}'" class="cursor-pointer transition hover:bg-white/5" title="View sale details">
                    <td class="p-6 font-mono text-white">#{{ str_pad($sale->saleID, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="p-6 text-white/80">{{ $sale->sold_at?->format('M d, Y - h:i A') ?? $sale->created_at?->format('M d, Y - h:i A') }}</td>
                    <td class="p-6 text-white/80">{{ $sale->user->name ?? '-' }}</td>
                    <td class="p-6 text-center text-white">{{ $sale->saleLines->count() }}</td>
                    <td class="p-6 text-right font-semibold text-white">PHP {{ number_format($sale->total, 2) }}</td>
                    <td class="p-6 text-center">
                        <span class="rounded-full px-4 py-1 text-xs font-semibold bg-white/5 text-white/80">{{ $sale->payment_method }}</span>
                    </td>
                    <td class="p-6 text-center">
                        @if($sale->sale_returns_count > 0)
                            <span class="status-pill status-warning">Returned</span>
                        @else
                            <span class="status-pill status-success">Saved</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-10 text-center text-sm text-white/80">
                        No completed sales yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($sales instanceof \Illuminate\Pagination\LengthAwarePaginator || $sales instanceof \Illuminate\Pagination\Paginator)
    <div class="mt-5 flex justify-end">
        {{ $sales->links() }}
    </div>
@endif
@endsection
