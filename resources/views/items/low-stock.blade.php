@extends('layouts.app')

@section('page_title', 'Low Stock Items')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold">Low Stock Items</h1>
        <p class="text-gray-600 dark:text-gray-400">Items that are at or below their reorder point</p>
    </div>
    <a href="{{ route('items.index') }}" class="px-5 py-3 rounded-2xl border border-gray-300 dark:border-gray-600 text-sm font-medium">
        Back to Inventory
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="text-left p-6 font-medium">SKU</th>
                <th class="text-left p-6 font-medium">Medicine</th>
                <th class="text-center p-6 font-medium">Stock</th>
                <th class="text-center p-6 font-medium">Reorder Point</th>
                <th class="text-center p-6 font-medium">Status</th>
                <th class="text-right p-6 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($items as $item)
            @php($stock = $item->inventoryBatches->sum('current_quantity'))
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="p-6 font-mono">{{ $item->item_code }}</td>
                <td class="p-6 font-medium">{{ $item->name }}</td>
                <td class="p-6 text-center font-semibold">{{ $stock }}</td>
                <td class="p-6 text-center">{{ $item->minimum_stock_lvl }}</td>
                <td class="p-6 text-center">
                    <span class="px-4 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Low</span>
                </td>
                <td class="p-6 text-right">
                    <a href="{{ route('purchase-orders.create', ['item_id' => $item->itemID]) }}" class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                        <i class="fas fa-plus"></i>
                        Create Order
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-12 text-center">
                    <div class="mx-auto max-w-md rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-6 py-8">
                        <p class="text-lg font-semibold text-white">No low stock items right now.</p>
                        <p class="mt-2 text-sm text-slate-300">All tracked medicines are currently above their reorder point.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
