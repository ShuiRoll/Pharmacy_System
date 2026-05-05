@extends('layouts.app')

@section('page_title', 'Dashboard')

@section('content')
@php($user = auth()->user())

<div class="relative overflow-hidden rounded-[2rem] bg-slate-950 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.30),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.18),_transparent_30%),linear-gradient(135deg,_#020617_0%,_#0f172a_45%,_#111827_100%)]"></div>
    <div class="absolute -top-24 -right-24 h-80 w-80 rounded-full bg-sky-500/20 blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl"></div>

    <div class="relative mx-auto px-6 py-10 sm:px-8 sm:py-12">
        <div class="grid w-full gap-8 lg:grid-cols-[1.15fr_0.85fr]">
            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-10">
                <div class="inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/80">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500/20 text-blue-200">
                        <i class="fa-solid fa-capsules"></i>
                    </span>
                    ClearStock Pharmacy Management
                </div>

                <div class="mt-8 space-y-5">
                    <h1 class="text-4xl font-bold tracking-tight text-white lg:text-5xl">Welcome back, {{ $user->name }}</h1>
                    <p class="max-w-xl text-base leading-7 text-white">
                        Manage inventory, suppliers, approvals, and POS workflows from one clean dashboard.
                    </p>
                </div>

                <div class="mt-10 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-white">Inventory</p>
                        <p class="mt-2 text-sm text-white">Items, locations, and stock signals</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-white">Operations</p>
                        <p class="mt-2 text-sm text-white">
                            {{ $user->role === 'admin' ? 'Suppliers, users, and controls' : 'Sales and return handling' }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="rounded-[2rem] border border-white/10 bg-slate-900/90 p-8 shadow-2xl shadow-black/30 backdrop-blur-xl">
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-white">Quick access</h2>
                    <p class="mt-2 text-sm text-white">Jump to your most-used modules.</p>
                </div>

                <div class="space-y-4">
                    @if($user->role === 'admin')
                        <a href="{{ route('items.index') }}" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-5 py-4 transition hover:border-blue-400 hover:bg-blue-500/10">
                            <div>
                                <p class="font-medium text-white">Item Hub</p>
                                <p class="text-sm text-white">Items, locations, and stock</p>
                            </div>
                            <i class="fas fa-arrow-right text-white transition group-hover:text-white"></i>
                        </a>

                        <a href="{{ route('inventory-adjustments.index') }}" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-5 py-4 transition hover:border-blue-400 hover:bg-blue-500/10">
                            <div>
                                <p class="font-medium text-white">Inventory Control</p>
                                <p class="text-sm text-white">Adjustments and watchlists</p>
                            </div>
                            <i class="fas fa-arrow-right text-white transition group-hover:text-white"></i>
                        </a>

                        <a href="{{ route('suppliers.index') }}" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-5 py-4 transition hover:border-blue-400 hover:bg-blue-500/10">
                            <div>
                                <p class="font-medium text-white">Supplier Hub</p>
                                <p class="text-sm text-white">Purchasing and receiving flow</p>
                            </div>
                            <i class="fas fa-arrow-right text-white transition group-hover:text-white"></i>
                        </a>

                        <a href="{{ route('users.index') }}" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-5 py-4 transition hover:border-blue-400 hover:bg-blue-500/10">
                            <div>
                                <p class="font-medium text-white">User Management</p>
                                <p class="text-sm text-white">Manage staff accounts</p>
                            </div>
                            <i class="fas fa-arrow-right text-white transition group-hover:text-white"></i>
                        </a>
                    @else
                        <a href="{{ route('sales.create') }}" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-5 py-4 transition hover:border-blue-400 hover:bg-blue-500/10">
                            <div>
                                <p class="font-medium text-white">Open POS</p>
                                <p class="text-sm text-white/80">Start a new sale</p>
                            </div>
                            <i class="fas fa-arrow-right text-white/80 transition group-hover:text-white"></i>
                        </a>

                        <a href="{{ route('sales.index') }}" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-5 py-4 transition hover:border-blue-400 hover:bg-blue-500/10">
                            <div>
                                <p class="font-medium text-white">Sales History</p>
                                <p class="text-sm text-white/80">View recent transactions</p>
                            </div>
                            <i class="fas fa-arrow-right text-white/80 transition group-hover:text-white"></i>
                        </a>

                        <a href="{{ route('sale-returns.index') }}" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-5 py-4 transition hover:border-blue-400 hover:bg-blue-500/10">
                            <div>
                                <p class="font-medium text-white">Returns</p>
                                <p class="text-sm text-white/80">Process return requests</p>
                            </div>
                            <i class="fas fa-arrow-right text-white/80 transition group-hover:text-white"></i>
                        </a>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection