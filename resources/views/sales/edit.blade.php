@extends('layouts.app')

@section('page_title', 'Edit Sale')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Edit Sale</h1>
        <p class="text-gray-600 dark:text-gray-400">Sale #{{ str_pad($sale->saleID, 5, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-10">
        <form action="{{ route('sales.update', $sale) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-medium mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl">
                        <option value="Cash" {{ $sale->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="GCash" {{ $sale->payment_method == 'GCash' ? 'selected' : '' }}>GCash</option>
                        <option value="Card" {{ $sale->payment_method == 'Card' ? 'selected' : '' }}>Card</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Total Amount</label>
                    <input type="number" step="0.01" name="total" 
                           value="{{ old('total', $sale->total) }}" 
                           class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl">
                </div>
            </div>

            <div class="mt-12 flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl transition">
                    Update Sale
                </button>
                <a href="{{ route('sales.index') }}" 
                   class="flex-1 text-center border border-gray-300 dark:border-gray-600 py-4 rounded-2xl font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection