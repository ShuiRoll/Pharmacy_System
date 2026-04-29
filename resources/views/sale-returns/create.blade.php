@extends('layouts.app')

@section('page_title', 'New Sale Return')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">New Sale Return</h1>
        <p class="text-gray-600 dark:text-gray-400">Record a refund or returned POS transaction</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-10">
        <form action="{{ route('sale-returns.store') }}" method="POST">
            @csrf

            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-medium mb-2">Sale ID <span class="text-red-500">*</span></label>
                    <select name="saleID" required
                            class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none focus:border-blue-500">
                        <option value="">Select completed sale</option>
                        @foreach($sales as $sale)
                            <option value="{{ $sale->saleID }}" @selected(old('saleID') == $sale->saleID)>
                                #{{ str_pad($sale->saleID, 5, '0', STR_PAD_LEFT) }}
                                - {{ $sale->sold_at?->format('M d, Y h:i A') ?? $sale->created_at?->format('M d, Y h:i A') }}
                                - PHP {{ number_format($sale->total, 2) }}
                                - {{ $sale->user->name ?? 'Unknown cashier' }}
                                {{ $sale->sale_returns_count ? '- already returned' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @if($sales->isEmpty())
                        <p class="mt-2 text-sm text-amber-300">No completed sales are available yet.</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Return Date <span class="text-red-500">*</span></label>
                    <input type="date" name="return_date" value="{{ date('Y-m-d') }}" required
                           class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="4" required
                              class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none focus:border-blue-500"></textarea>
                </div>
            </div>

            <div class="mt-12 flex gap-4">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl transition">
                    Save Return
                </button>
                <a href="{{ route('sale-returns.index') }}"
                   class="flex-1 text-center border border-gray-300 dark:border-gray-600 py-4 rounded-2xl font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
