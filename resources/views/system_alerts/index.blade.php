@extends('layouts.app')

@section('page_title', 'System Alerts')

@section('content')
<div class="space-y-8">
    <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-xl shadow-black/20 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80 dark:shadow-black/20">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <div class="mb-4 flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/60">
                    <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200">Inventory</span>
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-white/80"><i class="fas fa-bell mr-1.5"></i>Alerts</span>
                </div>
                <h1 class="text-4xl font-semibold tracking-tight text-white">Inventory alerts</h1>
                <p class="mt-3 text-base leading-7 text-white/80">Track unresolved stock warnings and mark them resolved once the inventory issue has been handled.</p>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-xl shadow-black/20 dark:border-slate-800 dark:bg-slate-900/80 dark:shadow-black/20">
        <div class="overflow-x-auto px-6 py-6">
            <table class="w-full text-sm">
                <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-[0.2em] text-white/80">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Alert Type</th>
                        <th class="px-6 py-4 font-semibold">Item</th>
                        <th class="px-6 py-4 font-semibold">Batch</th>
                        <th class="px-6 py-4 font-semibold">Generated</th>
                        <th class="px-6 py-4 text-center font-semibold">Status</th>
                        <th class="px-6 py-4 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($alerts as $alert)
                        @php($isLowStockAlert = str_replace('_', ' ', strtolower($alert->alert_type)) === 'low stock')
                        <tr class="transition hover:bg-white/5">
                            <td class="px-6 py-5">
                                <span class="status-pill {{ $isLowStockAlert ? 'status-danger' : 'status-warning' }}">
                                    {{ $alert->alert_type }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-white">{{ $alert->item->name ?? '—' }}</td>
                            <td class="px-6 py-5 text-white/80">{{ $alert->batch->lot_number ?? '—' }}</td>
                            <td class="px-6 py-5 text-white/80">{{ $alert->date_generated->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-5 text-center">
                                @if($alert->is_resolved)
                                    <span class="status-pill status-success">Resolved</span>
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="table-action action-success">Mark Resolved</button>
                            </td>
                            <td class="px-6 py-5 text-right">
                                @if(!$alert->is_resolved)
                                    @if($isLowStockAlert && $alert->item)
                                        <a href="{{ route('purchase-orders.create', ['item_id' => $alert->itemID]) }}" class="mr-4 inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                                            <i class="fas fa-plus"></i>
                                            Create Order
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-white/80">No active alerts.</td>
                                    @endif
                                    <form action="{{ route('system-alerts.update', $alert) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="table-action action-success">Mark Resolved</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-700 dark:text-white">No active alerts.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
