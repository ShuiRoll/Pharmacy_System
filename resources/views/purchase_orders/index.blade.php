@extends('layouts.app')

@section('page_title', 'Purchase Orders')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold">Purchase Orders</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage orders placed with suppliers</p>
    </div>
    <a href="{{ route('purchase-orders.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-medium flex items-center gap-2">
        <i class="fas fa-plus"></i> New Purchase Order
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="text-left p-6 font-medium">PO Number</th>
                <th class="text-left p-6 font-medium">Supplier</th>
                <th class="text-left p-6 font-medium">PO Date</th>
                <th class="text-left p-6 font-medium">Expected Date</th>
                <th class="text-center p-6 font-medium">Total Amount</th>
                <th class="text-center p-6 font-medium">Status</th>
                <th class="w-56 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($purchaseOrders as $po)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="p-6 font-mono">PO-{{ str_pad($po->poID, 4, '0', STR_PAD_LEFT) }}</td>
                <td class="p-6">{{ $po->supplier->supplier_name ?? '—' }}</td>
                <td class="p-6">{{ $po->po_date ? $po->po_date->format('M d, Y') : '—' }}</td>
                <td class="p-6">{{ $po->expected_date ? $po->expected_date->format('M d, Y') : '—' }}</td>
                <td class="p-6 text-right font-semibold">₱{{ number_format($po->total_amount ?? 0, 2) }}</td>
                <td class="p-6 text-center">
                    <span class="px-4 py-1 rounded-full text-xs font-medium
                        @if($po->status == 'Received') bg-green-100 text-green-700
                        @elseif($po->status == 'Approved') bg-blue-100 text-blue-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ $po->status }}
                    </span>
                </td>
                <td class="p-6 text-right">
                    <div class="flex items-center justify-end flex-wrap gap-3">
                        <a href="{{ route('purchase-orders.edit', $po) }}" class="text-blue-600 hover:underline">Edit</a>
                        @if($po->status === 'Pending')
                            <form action="{{ route('purchase-orders.approve', $po) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 hover:underline">Approve</button>
                            </form>
                            <form action="{{ route('purchase-orders.reject', $po) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-amber-600 hover:underline">Reject</button>
                            </form>
                        @endif
                        <form action="{{ route('purchase-orders.destroy', $po) }}" method="POST" onsubmit="return confirm('Delete this purchase order?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
