@extends('layouts.app')

@section('page_title', 'Edit Supplier')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Edit Supplier</h1>
        <p class="text-white/80">{{ $supplier->supplier_name }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-10">
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-medium mb-2">Supplier Name <span class="text-red-500">*</span></label>
                    <input type="text" name="supplier_name" 
                           value="{{ old('supplier_name', $supplier->supplier_name) }}" 
                           required
                           class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none focus:border-blue-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2">Contact Person</label>
                        <input type="text" name="contact_person" 
                               value="{{ old('contact_person', $supplier->contact_person) }}"
                               class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Contact Number</label>
                        <input type="text" name="contact_number" 
                               value="{{ old('contact_number', $supplier->contact_number) }}"
                               class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl focus:outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            <div class="mt-12 flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-2xl transition">
                    Update Supplier
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