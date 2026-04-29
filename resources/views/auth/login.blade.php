<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ClearStock') }} - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.30),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.18),_transparent_30%),linear-gradient(135deg,_#020617_0%,_#0f172a_45%,_#111827_100%)]"></div>

        <div class="relative mx-auto flex min-h-screen max-w-7xl items-center justify-center px-6 py-12">
            <div class="grid w-full max-w-5xl gap-8 lg:grid-cols-[1.15fr_0.85fr]">
                <div class="rounded-lg border border-white/10 bg-white/5 p-10 shadow-2xl shadow-black/20 backdrop-blur-xl">
                    <div class="inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500/20 text-blue-200">
                            <i class="fa-solid fa-capsules"></i>
                        </span>
                        ClearStock Pharmacy Management
                    </div>

                    <div class="mt-8 space-y-5">
                        <h1 class="text-4xl font-bold tracking-tight text-white lg:text-5xl">Inventory and POS in one clean workflow.</h1>
                        <p class="max-w-xl text-base leading-7 text-white">
                            Sign in to manage stock, receiving, approvals, and the POS area from the same system.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-white">Admin</p>
                            <p class="mt-2 text-sm text-white">Inventory, suppliers, users, and reports</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-white">Users</p>
                            <p class="mt-2 text-sm text-white">Sales and returns</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-white/10 bg-slate-900/90 p-8 shadow-2xl shadow-black/30 backdrop-blur-xl">
                    <div class="mb-8">
                        <h2 class="text-2xl font-semibold text-white">Sign in</h2>
                        <p class="mt-2 text-sm text-white">Use your pharmacy account credentials.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-6 rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.attempt') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label class="mb-2 block text-sm font-medium text-white">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-white placeholder:text-slate-500 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-white">Password</label>
                            <div class="relative">
                                <input id="login-password" type="password" name="password" required
                                       class="w-full rounded-lg border border-white/10 bg-white/5 px-5 py-4 pr-12 text-white placeholder:text-slate-500 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                <button type="button" data-password-toggle="#login-password" aria-label="Show password" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <label class="flex items-center gap-3 text-sm text-white">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/20 bg-white/5 text-blue-500 focus:ring-blue-500">
                            Remember me
                        </label>

                        <button type="submit" class="w-full rounded-2xl bg-blue-600 px-5 py-4 font-semibold text-white transition hover:bg-blue-500">
                            Log in
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
