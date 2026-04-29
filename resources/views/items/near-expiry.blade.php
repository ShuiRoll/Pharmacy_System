@extends('layouts.app')

@section('page_title', 'Near Expiry Items')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold">Near Expiry Items</h1>
        <p class="text-gray-600 dark:text-gray-400">Batches with expiration dates within the next 30 days</p>
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
                <th class="text-left p-6 font-medium">Batch</th>
                <th class="text-center p-6 font-medium">Expiry</th>
                <th class="text-center p-6 font-medium">Quantity</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($items as $item)
            @php($batch = $item->inventoryBatches->sortBy('expiration_date')->first())
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="p-6 font-mono">{{ $item->item_code }}</td>
                <td class="p-6 font-medium">{{ $item->name }}</td>
                <td class="p-6">{{ $batch?->lot_number ?? '—' }}</td>
                <td class="p-6 text-center text-sm text-gray-500">{{ $batch?->expiration_date?->format('M d, Y') ?? '—' }}</td>
                <td class="p-6 text-center font-semibold">{{ $batch?->current_quantity ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-12 text-center">
                    <div class="mx-auto max-w-md rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-6 py-8">
                        <p class="text-lg font-semibold text-white">No near expiry items right now.</p>
                        <p class="mt-2 text-sm text-slate-300">No active batches are expiring within the next 30 days.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
