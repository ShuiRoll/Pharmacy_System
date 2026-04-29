@extends('layouts.app')

@section('page_title', 'Edit Inbound Receipt')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Edit Inbound Receipt</h1>
        <p class="text-gray-600 dark:text-gray-400">Receipt #{{ str_pad($inbound->in_transactionID, 5, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-10">
        <form action="{{ route('inbound.update', $inbound) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <div>
                    <label class="block text-sm font-medium mb-2">Purchase Order Reference</label>
                    <select name="poID" class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl">
                        <option value="">Direct Receipt</option>
                        @foreach($purchaseOrders ?? [] as $po)
                            <option value="{{ $po->poID }}" {{ $inbound->poID == $po->poID ? 'selected' : '' }}>
                                PO-{{ str_pad($po->poID, 4, '0', STR_PAD_LEFT) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Date Received</label>
                    <input type="date" name="date_received" 
                           value="{{ old('date_received', $inbound->date_received?->format('Y-m-d')) }}" 
                           class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Quality Status</label>
                    <select name="quality_status" class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl">
                        <option value="Passed" {{ $inbound->quality_status == 'Passed' ? 'selected' : '' }}>Passed</option>
                        <option value="Pending" {{ $inbound->quality_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Failed" {{ $inbound->quality_status == 'Failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
            </div>

            <div class="mt-12 flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl transition">
                    Update Receipt
                </button>
                <a href="{{ route('inbound.index') }}" 
                   class="flex-1 text-center border border-gray-300 dark:border-gray-600 py-4 rounded-2xl font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection