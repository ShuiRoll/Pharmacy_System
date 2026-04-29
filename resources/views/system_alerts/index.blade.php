@extends('layouts.app')

@section('page_title', 'System Alerts')

@section('content')
<div class="space-y-8">
    <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-xl shadow-black/20 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80 dark:shadow-black/20">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <div class="mb-4 flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">
                    <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200">Inventory</span>
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-slate-200"><i class="fas fa-bell mr-1.5"></i>Alerts</span>
                </div>
                <h1 class="text-4xl font-semibold tracking-tight text-slate-800 dark:text-white">Inventory alerts</h1>
                <p class="mt-3 text-base leading-7 text-slate-700 dark:text-white">Track unresolved stock warnings and mark them resolved once the inventory issue has been handled.</p>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-xl shadow-black/20 dark:border-slate-800 dark:bg-slate-900/80 dark:shadow-black/20">
        <div class="overflow-x-auto px-6 py-6">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-slate-800 dark:bg-slate-800/70 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Alert Type</th>
                        <th class="px-6 py-4 font-semibold">Item</th>
                        <th class="px-6 py-4 font-semibold">Batch</th>
                        <th class="px-6 py-4 font-semibold">Generated</th>
                        <th class="px-6 py-4 text-center font-semibold">Status</th>
                        <th class="px-6 py-4 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($alerts as $alert)
                        <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/60">
                            <td class="px-6 py-5">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $alert->alert_type == 'Low_Stock' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-200' : 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-200' }}">
                                    {{ $alert->alert_type }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-slate-800 dark:text-white">{{ $alert->item->name ?? '—' }}</td>
                            <td class="px-6 py-5 text-slate-700 dark:text-white">{{ $alert->batch->lot_number ?? '—' }}</td>
                            <td class="px-6 py-5 text-slate-700 dark:text-white">{{ $alert->date_generated->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-5 text-center">
                                @if($alert->is_resolved)
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-200">Resolved</span>
                                @else
                                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-500/15 dark:text-rose-200">Active</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                @if(!$alert->is_resolved)
                                    <form action="{{ route('system-alerts.update', $alert) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="font-medium text-blue-600 transition hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-200">Mark Resolved</button>
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
