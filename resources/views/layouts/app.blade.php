<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ClearStock') }} - @yield('page_title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <div class="relative min-h-screen overflow-x-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.30),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.18),_transparent_30%),linear-gradient(135deg,_#020617_0%,_#0f172a_45%,_#111827_100%)]"></div>
        <div class="relative min-h-screen">
        @php
            $user = auth()->user();
            $activeAlertCount = $user->role === 'admin' && \Illuminate\Support\Facades\Schema::hasTable('system_alerts')
                ? \App\Models\SystemAlert::where('is_resolved', false)->count()
                : 0;
        @endphp
        <nav class="sticky top-0 z-40 w-full border-b border-white/10 bg-slate-950/75 backdrop-blur-xl">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="grid gap-4 py-4 xl:grid-cols-[minmax(260px,1fr)_auto_minmax(260px,1fr)] xl:items-center">
                    <div class="flex min-w-0 items-center gap-3 xl:justify-self-start">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500 text-base font-bold text-white shadow-lg shadow-blue-500/20">
                            CS
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.35em] text-slate-400">ClearStock</p>
                            <h1 class="truncate text-base font-semibold text-white">Pharmacy Inventory Management</h1>
                        </div>
                        <span class="hidden shrink-0 rounded-lg border border-blue-500/30 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200 2xl:inline-flex">
                            {{ $user->role === 'admin' ? 'Admin Workspace' : 'POS Workspace' }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-2 text-sm font-medium xl:justify-self-center">
                        @if($user->role === 'admin')
                            <a href="{{ route('dashboard') }}" class="rounded-lg border px-4 py-2 transition {{ request()->routeIs('dashboard') ? 'border-blue-500/50 bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'border-white/10 bg-white/5 text-slate-300 hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white' }}">Dashboard</a>
                            <a href="{{ route('items.index') }}" class="rounded-lg border px-4 py-2 transition {{ request()->routeIs('items.index', 'items.create', 'items.edit', 'items.destroy', 'items.show', 'locations.*') ? 'border-blue-500/50 bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'border-white/10 bg-white/5 text-slate-300 hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white' }}">Items</a>
                            <a href="{{ route('inventory-adjustments.index') }}" class="rounded-lg border px-4 py-2 transition {{ request()->routeIs('inventory-adjustments.*', 'cycle-counts.*', 'items.low-stock', 'items.near-expiry', 'system-alerts.*', 'outbound.*') ? 'border-blue-500/50 bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'border-white/10 bg-white/5 text-slate-300 hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white' }}">Inventory</a>
                            <a href="{{ route('suppliers.index') }}" class="rounded-lg border px-4 py-2 transition {{ request()->routeIs('suppliers.*', 'purchase-orders.*', 'inbound.*') ? 'border-blue-500/50 bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'border-white/10 bg-white/5 text-slate-300 hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white' }}">Suppliers</a>
                            <a href="{{ route('users.index') }}" class="rounded-lg border px-4 py-2 transition {{ request()->routeIs('users.*') ? 'border-blue-500/50 bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'border-white/10 bg-white/5 text-slate-300 hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white' }}">Users</a>
                            <a href="{{ route('archives.index') }}" class="rounded-lg border px-4 py-2 transition {{ request()->routeIs('archives.*') ? 'border-blue-500/50 bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'border-white/10 bg-white/5 text-slate-300 hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white' }}">Archives</a>
                        @else
                            <a href="{{ route('sales.create') }}" class="rounded-lg border px-4 py-2 transition {{ request()->routeIs('sales.create') ? 'border-blue-500/50 bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'border-white/10 bg-white/5 text-slate-300 hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white' }}">POS</a>
                            <a href="{{ route('sales.index') }}" class="rounded-lg border px-4 py-2 transition {{ request()->routeIs('sales.index') ? 'border-blue-500/50 bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'border-white/10 bg-white/5 text-slate-300 hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white' }}">Sales</a>
                            <a href="{{ route('sale-returns.index') }}" class="rounded-lg border px-4 py-2 transition {{ request()->routeIs('sale-returns.*') ? 'border-blue-500/50 bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'border-white/10 bg-white/5 text-slate-300 hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white' }}">Returns</a>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-3 xl:justify-self-end">
                        @if($user->role === 'admin')
                            <a href="{{ route('system-alerts.index') }}"
                               title="System alerts"
                               class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border transition {{ request()->routeIs('system-alerts.*') ? 'border-blue-500/50 bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'border-white/15 bg-white/5 text-slate-200 hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-white' }}">
                                <i class="fas fa-bell"></i>
                                @if($activeAlertCount > 0)
                                    <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white ring-2 ring-slate-950">
                                        {{ $activeAlertCount > 9 ? '9+' : $activeAlertCount }}
                                    </span>
                                @endif
                            </a>
                        @endif

                        <div class="flex min-w-0 items-center justify-between gap-3 rounded-lg border border-white/15 bg-white/5 px-3 py-2 shadow-sm backdrop-blur">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-cyan-500 text-sm font-semibold text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="hidden min-w-0 leading-tight sm:block">
                                <p class="truncate text-sm font-semibold text-white">{{ $user->name }}</p>
                                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">{{ $user->role }}</p>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="rounded-lg border border-white/15 bg-white/5 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-red-400/40 hover:bg-red-500/10 hover:text-red-200">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        @if(session('success') || session('error'))
            <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                <div class="space-y-3">
                    @if(session('success'))
                        <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-5 py-4 text-emerald-100 shadow-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="rounded-2xl border border-red-500/20 bg-red-500/10 px-5 py-4 text-red-100 shadow-sm">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <main class="relative mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @yield('content')
        </main>
        </div>
    </div>
</body>
</html>
