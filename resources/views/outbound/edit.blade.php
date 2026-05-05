@extends('layouts.app')

@section('page_title', 'Edit Outbound Transaction')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Edit Outbound Transaction</h1>
        <p class="text-white/80">Transaction #{{ str_pad($outbound->out_transactionID, 5, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-10">
        <form action="{{ route('outbound.update', $outbound) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-medium mb-2">Destination</label>
                    <input type="text" name="destination" 
                           value="{{ old('destination', $outbound->destination) }}" 
                           class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Transaction Date</label>
                    <input type="date" name="transaction_date" 
                           value="{{ old('transaction_date', $outbound->transaction_date->format('Y-m-d')) }}" 
                           class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl">
                </div>
            </div>

            <div class="mt-12 flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl transition">
                    Update Outbound
                </button>
                <a href="{{ route('outbound.index') }}" 
                   class="flex-1 text-center border border-gray-300 dark:border-gray-600 py-4 rounded-2xl font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection