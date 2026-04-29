@extends('layouts.app')

@section('page_title', 'Cycle Counts')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold">Cycle Counts</h1>
        <p class="text-gray-600 dark:text-gray-400">Physical inventory verification</p>
    </div>
    <a href="{{ route('cycle-counts.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-medium flex items-center gap-2">
        <i class="fas fa-plus"></i> New Cycle Count
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="text-left p-6 font-medium">Count ID</th>
                <th class="text-left p-6 font-medium">Date</th>
                <th class="text-left p-6 font-medium">Performed By</th>
                <th class="text-center p-6 font-medium">Status</th>
                <th class="text-center p-6 font-medium">Items Counted</th>
                <th class="w-40 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($cycleCounts as $count)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="p-6 font-mono">#{{ str_pad($count->countID, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="p-6">{{ $count->count_date->format('M d, Y') }}</td>
                <td class="p-6">{{ $count->user->name ?? '—' }}</td>
                <td class="p-6 text-center">
                    <span class="px-4 py-1 rounded-full text-xs font-medium 
                        @if($count->status == 'Completed') bg-green-100 text-green-700 @else bg-yellow-100 text-yellow-700 @endif">
                        {{ $count->status }}
                    </span>
                </td>
                <td class="p-6 text-center">{{ $count->cycleCountLines->count() }}</td>
                <td class="p-6 text-right">
                    <a href="{{ route('cycle-counts.edit', $count) }}" class="text-blue-600 hover:underline">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
