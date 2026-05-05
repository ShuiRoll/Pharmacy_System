@extends('layouts.app')

@section('page_title', 'Edit Location')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Edit Location</h1>
        <p class="text-white/80">{{ $location->name }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-10">
        <form action="{{ route('locations.update', $location) }}" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-2">Location Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $location->name) }}" required
                       class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none focus:border-blue-500">
            </div>

            <div class="mt-12 flex gap-4">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl transition">
                    Update Location
                </button>
                <a href="{{ route('locations.index') }}"
                   class="flex-1 text-center border border-gray-300 dark:border-gray-600 py-4 rounded-2xl font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection