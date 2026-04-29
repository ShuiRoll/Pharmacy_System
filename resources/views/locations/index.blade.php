@extends('layouts.app')

@section('page_title', 'Locations')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold">Locations</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage storage areas and dispensing points</p>
    </div>
    <a href="{{ route('locations.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-medium flex items-center gap-2">
        <i class="fas fa-plus"></i> Add Location
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="text-left p-6 font-medium">Location</th>
                <th class="text-center p-6 font-medium">Stock Batches</th>
                <th class="w-40 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($locations as $location)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="p-6 font-medium">{{ $location->name }}</td>
                <td class="p-6 text-center">{{ $location->inventoryBatches->count() }}</td>
                <td class="p-6 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('locations.edit', $location) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Delete this location?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
