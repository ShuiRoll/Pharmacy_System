@extends('layouts.app')

@section('page_title', 'Cycle Counts')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold">Cycle Counts</h1>
        <p class="text-white/80">Physical inventory verification</p>
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
                <th class="w-56 p-6 text-right font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($cycleCounts as $count)
                @php($isCompleted = $count->status === 'Completed')
                <tr onclick="window.location='{{ route('cycle-counts.show', $count) }}'" class="cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-700" title="View cycle count details">
                    <td class="p-6 font-mono">#{{ str_pad($count->countID, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="p-6">{{ $count->count_date->format('M d, Y') }}</td>
                    <td class="p-6">{{ $count->user->name ?? '-' }}</td>
                    <td class="p-6 text-center">
                        <span class="px-4 py-1 rounded-full text-xs font-medium {{ $isCompleted ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $isCompleted ? 'Completed' : 'Incomplete' }}
                        </span>
                    </td>
                    <td class="p-6 text-center">{{ $count->cycleCountLines->count() }}</td>
                    <td class="p-6 text-right">
                        @unless($isCompleted)
                            <a href="{{ route('cycle-counts.edit', $count) }}" onclick="event.stopPropagation()" class="inline-flex items-center justify-center whitespace-nowrap rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                                Complete Cycle Count
                            </a>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-sm text-white/80">
                        No cycle counts planned yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
