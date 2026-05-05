@extends('layouts.app')

@section('page_title', 'Add New Supplier')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Add New Supplier</h1>
        <p class="text-white/80">Register a new supplier for purchasing</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-10">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf

            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-medium mb-2">Supplier Name <span class="text-red-500">*</span></label>
                    <input type="text" name="supplier_name" required
                           class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none focus:border-blue-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2">Contact Person</label>
                        <input type="text" name="contact_person"
                               class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Contact Number</label>
                        <input type="text" name="contact_number"
                               class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            <div class="mt-12 flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl transition">
                    Save Supplier
                </button>
                <a href="{{ route('suppliers.index') }}" 
                   class="flex-1 text-center border border-gray-300 dark:border-gray-600 py-4 rounded-2xl font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection