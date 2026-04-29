@extends('layouts.app')

@section('page_title', 'New Inventory Adjustment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">New Inventory Adjustment</h1>
        <p class="text-gray-600 dark:text-gray-400">Adjust stock for damage, loss, or expiration</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-10">
        <form action="{{ route('inventory-adjustments.store') }}" method="POST">
            @csrf

            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-medium mb-2">Select Batch</label>
                    <select name="batchID" required class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl">
                        <option value="">-- Select Batch --</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->batchID }}">
                                {{ $batch->item->name }} - Batch {{ $batch->lot_number }} ({{ $batch->current_quantity }} left)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Quantity Change <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity_changed" required 
                           class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl">
                    <p class="text-xs text-gray-500 mt-1">Use negative number for reduction (e.g. -5)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Reason</label>
                    <textarea name="reason" rows="3" required
                              class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl"></textarea>
                </div>
            </div>

            <div class="mt-12 flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl">
                    Record Adjustment
                </button>
                <a href="{{ route('inventory-adjustments.index') }}" class="flex-1 text-center border py-4 rounded-2xl font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection