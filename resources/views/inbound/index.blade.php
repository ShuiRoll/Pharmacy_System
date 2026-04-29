@extends('layouts.app')

@section('page_title', 'Inbound Receiving')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold">Inbound Receiving</h1>
        <p class="text-gray-600 dark:text-gray-400">Goods received from suppliers</p>
    </div>
    <a href="{{ route('inbound.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-medium flex items-center gap-2">
        <i class="fas fa-plus"></i> New Receipt
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="text-left p-6 font-medium">Receipt ID</th>
                <th class="text-left p-6 font-medium">PO Reference</th>
                <th class="text-left p-6 font-medium">Date Received</th>
                <th class="text-left p-6 font-medium">Received By</th>
                <th class="text-center p-6 font-medium">Quality</th>
                <th class="text-right p-6 font-medium">Total Cost</th>
                <th class="text-center p-6 font-medium">Items</th>
                <th class="w-32 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($inbounds as $inbound)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="p-6 font-mono">#{{ str_pad($inbound->in_transactionID, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="p-6">
                    @if($inbound->poID)
                        PO-{{ str_pad($inbound->poID, 4, '0', STR_PAD_LEFT) }}
                    @else
                        <span class="text-gray-400">Direct</span>
                    @endif
                </td>
                <td class="p-6">{{ $inbound->date_received?->format('M d, Y') }}</td>
                <td class="p-6">{{ $inbound->user->name ?? '—' }}</td>
                <td class="p-6 text-center">
                    @if($inbound->quality_status == 'Passed')
                        <span class="bg-green-100 text-green-700 px-4 py-1 rounded-full text-xs">Passed</span>
                    @else
                        <span class="bg-yellow-100 text-yellow-700 px-4 py-1 rounded-full text-xs">{{ $inbound->quality_status }}</span>
                    @endif
                </td>
                <td class="p-6 text-right font-semibold">₱{{ number_format($inbound->total_cost ?? 0, 2) }}</td>
                <td class="p-6 text-center">{{ $inbound->inboundLineItems->count() }}</td>
                <td class="p-6 text-right">
                    <a href="#" class="text-blue-600 hover:underline">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
