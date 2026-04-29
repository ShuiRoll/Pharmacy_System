@extends('layouts.app')

@section('page_title', 'Archives')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Archives</h1>
        <p class="text-gray-600 dark:text-gray-400">Soft-deleted records stay here until restored.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="rounded-3xl bg-white dark:bg-gray-800 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Items</p>
            <p class="mt-2 text-3xl font-semibold">{{ $archivedItems->count() }}</p>
        </div>
        <div class="rounded-3xl bg-white dark:bg-gray-800 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Suppliers</p>
            <p class="mt-2 text-3xl font-semibold">{{ $archivedSuppliers->count() }}</p>
        </div>
        <div class="rounded-3xl bg-white dark:bg-gray-800 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Locations</p>
            <p class="mt-2 text-3xl font-semibold">{{ $archivedLocations->count() }}</p>
        </div>
        <div class="rounded-3xl bg-white dark:bg-gray-800 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Purchase Orders</p>
            <p class="mt-2 text-3xl font-semibold">{{ $archivedPurchaseOrders->count() }}</p>
        </div>
        <div class="rounded-3xl bg-white dark:bg-gray-800 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Users</p>
            <p class="mt-2 text-3xl font-semibold">{{ $archivedUsers->count() }}</p>
        </div>
    </div>

    @if($archivedItems->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold">Archived Items</h2>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50 dark:bg-gray-700">
                    <th class="text-left p-6 font-medium">SKU</th>
                    <th class="text-left p-6 font-medium">Medicine</th>
                    <th class="text-left p-6 font-medium">Deleted At</th>
                    <th class="w-40 text-right p-6 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($archivedItems as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="p-6 font-mono">{{ $item->item_code }}</td>
                    <td class="p-6 font-medium">{{ $item->name }}</td>
                    <td class="p-6 text-sm text-gray-500">{{ $item->deleted_at?->format('M d, Y H:i') }}</td>
                    <td class="p-6 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'items', 'id' => $item->itemID]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:underline">Restore</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($archivedSuppliers->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold">Archived Suppliers</h2>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50 dark:bg-gray-700">
                    <th class="text-left p-6 font-medium">Supplier</th>
                    <th class="text-left p-6 font-medium">Contact Person</th>
                    <th class="text-left p-6 font-medium">Deleted At</th>
                    <th class="w-40 text-right p-6 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($archivedSuppliers as $supplier)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="p-6 font-medium">{{ $supplier->supplier_name }}</td>
                    <td class="p-6">{{ $supplier->contact_person ?? '—' }}</td>
                    <td class="p-6 text-sm text-gray-500">{{ $supplier->deleted_at?->format('M d, Y H:i') }}</td>
                    <td class="p-6 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'suppliers', 'id' => $supplier->supplierID]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:underline">Restore</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($archivedLocations->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold">Archived Locations</h2>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50 dark:bg-gray-700">
                    <th class="text-left p-6 font-medium">Location</th>
                    <th class="text-left p-6 font-medium">Deleted At</th>
                    <th class="w-40 text-right p-6 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($archivedLocations as $location)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="p-6 font-medium">{{ $location->name }}</td>
                    <td class="p-6 text-sm text-gray-500">{{ $location->deleted_at?->format('M d, Y H:i') }}</td>
                    <td class="p-6 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'locations', 'id' => $location->locationID]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:underline">Restore</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($archivedPurchaseOrders->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold">Archived Purchase Orders</h2>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50 dark:bg-gray-700">
                    <th class="text-left p-6 font-medium">PO Number</th>
                    <th class="text-left p-6 font-medium">Supplier</th>
                    <th class="text-left p-6 font-medium">Deleted At</th>
                    <th class="w-40 text-right p-6 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($archivedPurchaseOrders as $po)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="p-6 font-mono">PO-{{ str_pad($po->poID, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="p-6">{{ $po->supplier->supplier_name ?? '—' }}</td>
                    <td class="p-6 text-sm text-gray-500">{{ $po->deleted_at?->format('M d, Y H:i') }}</td>
                    <td class="p-6 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'purchase-orders', 'id' => $po->poID]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:underline">Restore</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($archivedUsers->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold">Archived Users</h2>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50 dark:bg-gray-700">
                    <th class="text-left p-6 font-medium">Name</th>
                    <th class="text-left p-6 font-medium">Email</th>
                    <th class="text-center p-6 font-medium">Role</th>
                    <th class="text-left p-6 font-medium">Deleted At</th>
                    <th class="w-40 text-right p-6 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($archivedUsers as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="p-6 font-medium">{{ $user->name }}</td>
                    <td class="p-6 text-gray-600">{{ $user->email }}</td>
                    <td class="p-6 text-center">{{ ucfirst($user->role) }}</td>
                    <td class="p-6 text-sm text-gray-500">{{ $user->deleted_at?->format('M d, Y H:i') }}</td>
                    <td class="p-6 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'users', 'id' => $user->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:underline">Restore</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
