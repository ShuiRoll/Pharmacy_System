@extends('layouts.app')

@section('page_title', 'Locations')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-white">Locations</h1>
        <p class="text-white/80">Manage storage areas and dispensing points</p>
    </div>
    <a href="{{ route('locations.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-medium flex items-center gap-2">
        <i class="fas fa-plus"></i> Add Location
    </a>
</div>

<div class="rounded-3xl border border-white/10 bg-white/5 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="text-left p-6 font-medium text-white">Location</th>
                <th class="text-center p-6 font-medium text-white">Stock Batches</th>
                <th class="w-40 text-right text-white">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($locations as $location)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="p-6 font-medium text-white">{{ $location->name }}</td>
                <td class="p-6 text-center text-white/80">{{ $location->inventoryBatches->count() }}</td>
                <td class="p-6 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('locations.edit', $location) }}" class="table-action">Edit</a>
                        <form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Delete this location?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="table-action action-danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
