@extends('layouts.app')

@section('page_title', 'Sale Returns')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Sale Returns</h1>
        <p class="text-white/80">Track refunded or exchanged POS transactions</p>
    </div>
    <a href="{{ route('sale-returns.create') }}"
       class="flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">
        <i class="fas fa-rotate-left"></i> New Return
    </a>
</div>

<div class="overflow-hidden rounded-3xl bg-white shadow-sm dark:bg-gray-800">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="p-6 text-left font-medium">Return ID</th>
                <th class="p-6 text-left font-medium">Sale</th>
                <th class="p-6 text-left font-medium">Date</th>
                <th class="p-6 text-left font-medium">Processed By</th>
                <th class="p-6 text-left font-medium">Reason</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($returns as $return)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="p-6 font-mono">#{{ str_pad($return->returnID, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="p-6">#{{ str_pad($return->saleID, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="p-6 text-white/80">{{ $return->return_date->format('M d, Y') }}</td>
                    <td class="p-6">{{ $return->user->name ?? '-' }}</td>
                    <td class="p-6">{{ $return->reason ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-10 text-center text-sm text-white/80">
                        No returns recorded yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($returns instanceof \Illuminate\Pagination\LengthAwarePaginator || $returns instanceof \Illuminate\Pagination\Paginator)
    <div class="mt-5 flex justify-end">
        {{ $returns->links() }}
    </div>
@endif
@endsection
