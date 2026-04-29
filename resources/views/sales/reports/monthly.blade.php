@extends('layouts.app')

@section('page_title', 'Monthly Sales Report')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold">Monthly Sales Report</h1>
        <p class="text-gray-600 dark:text-gray-400">Sales completed this month</p>
    </div>
    <a href="{{ route('sales.index') }}" class="px-5 py-3 rounded-2xl border border-gray-300 dark:border-gray-600 text-sm font-medium">
        Back to Sales
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">Transactions</p>
        <p class="text-3xl font-semibold mt-2">{{ $sales->count() }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">Total Revenue</p>
        <p class="text-3xl font-semibold mt-2">₱{{ number_format($total, 2) }}</p>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="text-left p-6 font-medium">Sale ID</th>
                <th class="text-left p-6 font-medium">Date</th>
                <th class="text-left p-6 font-medium">Cashier</th>
                <th class="text-center p-6 font-medium">Items</th>
                <th class="text-right p-6 font-medium">Total</th>
                <th class="text-center p-6 font-medium">Payment</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($sales as $sale)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="p-6 font-mono">#{{ str_pad($sale->saleID, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="p-6">{{ $sale->sold_at->format('M d, Y • h:i A') }}</td>
                <td class="p-6">{{ $sale->user->name ?? '—' }}</td>
                <td class="p-6 text-center">{{ $sale->saleLines->count() }}</td>
                <td class="p-6 text-right font-semibold">₱{{ number_format($sale->total, 2) }}</td>
                <td class="p-6 text-center">{{ $sale->payment_method }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection