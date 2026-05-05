@extends('layouts.app')

@section('page_title', 'Archives')

@section('content')
<div class="space-y-8">
    <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-xl shadow-black/20 backdrop-blur">
        <div>
            <h1 class="text-3xl font-bold text-white">Archives</h1>
            <p class="text-white/80">Soft-deleted records stay here until restored.</p>
        </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-white/80">Items</p>
            <p class="mt-2 text-3xl font-semibold text-white">{{ $archivedItems->count() }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-white/80">Suppliers</p>
            <p class="mt-2 text-3xl font-semibold text-white">{{ $archivedSuppliers->count() }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-white/80">Locations</p>
            <p class="mt-2 text-3xl font-semibold text-white">{{ $archivedLocations->count() }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-white/80">Purchase Orders</p>
            <p class="mt-2 text-3xl font-semibold text-white">{{ $archivedPurchaseOrders->count() }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <p class="text-sm text-white/80">Users</p>
            <p class="mt-2 text-3xl font-semibold text-white">{{ $archivedUsers->count() }}</p>
        </div>
    </div>
    </div>

    @if($archivedItems->isNotEmpty())
    <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-xl shadow-black/20 backdrop-blur">
        <div class="px-6 py-4 border-b border-white/10">
            <h2 class="text-xl font-semibold text-white">Archived Items</h2>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-slate-900/50">
                    <th class="text-left p-6 font-medium text-white/80">SKU</th>
                    <th class="text-left p-6 font-medium text-white/80">Medicine</th>
                    <th class="text-left p-6 font-medium text-white/80">Deleted At</th>
                    <th class="w-40 text-right p-6 font-medium text-white/80">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @foreach($archivedItems as $item)
                <tr class="hover:bg-white/5 transition">
                    <td class="p-6 font-mono text-white">{{ $item->item_code }}</td>
                    <td class="p-6 font-medium text-white">{{ $item->name }}</td>
                    <td class="p-6 text-sm text-white/80">{{ $item->deleted_at?->format('M d, Y H:i') }}</td>
                    <td class="p-6 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'items', 'id' => $item->itemID]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="table-action">Restore</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @endif

    @if($archivedSuppliers->isNotEmpty())
    <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-xl shadow-black/20 backdrop-blur">
        <div class="px-6 py-4 border-b border-white/10">
            <h2 class="text-xl font-semibold text-white">Archived Suppliers</h2>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-slate-900/50">
                    <th class="text-left p-6 font-medium text-white/80">Supplier</th>
                    <th class="text-left p-6 font-medium text-white/80">Contact Person</th>
                    <th class="text-left p-6 font-medium text-white/80">Deleted At</th>
                    <th class="w-40 text-right p-6 font-medium text-white/80">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @foreach($archivedSuppliers as $supplier)
                <tr class="hover:bg-white/5 transition">
                    <td class="p-6 font-medium text-white">{{ $supplier->supplier_name }}</td>
                    <td class="p-6 text-white">{{ $supplier->contact_person ?? '—' }}</td>
                    <td class="p-6 text-sm text-white/80">{{ $supplier->deleted_at?->format('M d, Y H:i') }}</td>
                    <td class="p-6 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'suppliers', 'id' => $supplier->supplierID]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="table-action">Restore</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @endif

    @if($archivedLocations->isNotEmpty())
    <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-xl shadow-black/20 backdrop-blur">
        <div class="px-6 py-4 border-b border-white/10">
            <h2 class="text-xl font-semibold text-white">Archived Locations</h2>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-slate-900/50">
                    <th class="text-left p-6 font-medium text-white/80">Location</th>
                    <th class="text-left p-6 font-medium text-white/80">Deleted At</th>
                    <th class="w-40 text-right p-6 font-medium text-white/80">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @foreach($archivedLocations as $location)
                <tr class="hover:bg-white/5 transition">
                    <td class="p-6 font-medium text-white">{{ $location->name }}</td>
                    <td class="p-6 text-sm text-white/80">{{ $location->deleted_at?->format('M d, Y H:i') }}</td>
                    <td class="p-6 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'locations', 'id' => $location->locationID]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="table-action">Restore</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @endif

    @if($archivedPurchaseOrders->isNotEmpty())
    <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-xl shadow-black/20 backdrop-blur">
        <div class="px-6 py-4 border-b border-white/10">
            <h2 class="text-xl font-semibold text-white">Archived Purchase Orders</h2>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-slate-900/50">
                    <th class="text-left p-6 font-medium text-white/80">PO Number</th>
                    <th class="text-left p-6 font-medium text-white/80">Supplier</th>
                    <th class="text-left p-6 font-medium text-white/80">Deleted At</th>
                    <th class="w-40 text-right p-6 font-medium text-white/80">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @foreach($archivedPurchaseOrders as $po)
                <tr class="hover:bg-white/5 transition">
                    <td class="p-6 font-mono text-white">PO-{{ str_pad($po->poID, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="p-6 text-white">{{ $po->supplier->supplier_name ?? '—' }}</td>
                    <td class="p-6 text-sm text-white/80">{{ $po->deleted_at?->format('M d, Y H:i') }}</td>
                    <td class="p-6 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'purchase-orders', 'id' => $po->poID]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="table-action">Restore</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @endif

    @if($archivedUsers->isNotEmpty())
    <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-xl shadow-black/20 backdrop-blur">
        <div class="px-6 py-4 border-b border-white/10">
            <h2 class="text-xl font-semibold text-white">Archived Users</h2>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-slate-900/50">
                    <th class="text-left p-6 font-medium text-white/80">Name</th>
                    <th class="text-left p-6 font-medium text-white/80">Email</th>
                    <th class="text-center p-6 font-medium text-white/80">Role</th>
                    <th class="text-left p-6 font-medium text-white/80">Deleted At</th>
                    <th class="w-40 text-right p-6 font-medium text-white/80">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @foreach($archivedUsers as $user)
                <tr class="hover:bg-white/5 transition">
                    <td class="p-6 font-medium text-white">{{ $user->name }}</td>
                    <td class="p-6 text-white/80">{{ $user->email }}</td>
                    <td class="p-6 text-center text-white">{{ ucfirst($user->role) }}</td>
                    <td class="p-6 text-sm text-white/80">{{ $user->deleted_at?->format('M d, Y H:i') }}</td>
                    <td class="p-6 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'users', 'id' => $user->id]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="table-action">Restore</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @endif
</div>
@endsection
