<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MotoRelease') }} - Employee Login</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full antialiased flex flex-col justify-center py-12 sm:px-6 lg:px-8 getlayers-mesh-bg text-slate-100 selection:bg-rose-500 selection:text-white relative">

    <!-- Atmospheric Grid -->
    <div class="fixed inset-0 getlayers-speed-grid opacity-25 pointer-events-none z-0"></div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4 relative z-10 space-y-3">
        <!-- Logo Emblem -->
        <div class="relative inline-block group">
            <div class="absolute -inset-2 bg-gradient-to-r from-rose-600 to-red-600 rounded-3xl blur-md opacity-60 group-hover:opacity-100 transition duration-300"></div>
            <img src="{{ asset('images/logo-icon.svg') }}" alt="MotoRelease Emblem" class="relative w-18 h-18 rounded-2xl shadow-2xl object-contain">
        </div>

        <div>
            <h2 class="text-3xl font-black tracking-tight text-white">
                Moto<span class="text-rose-500">Release</span> Console
            </h2>
            <p class="mt-1 text-xs text-slate-400 font-medium">
                Dealership Document &amp; Plate Releasing Management
            </p>
        </div>

        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-slate-900/90 text-slate-300 border border-slate-700/80 shadow-inner">
            <span class="relative flex h-2 w-2">
                <span class="getlayers-radar absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="text-[11px] font-bold">San Jose de Buenavista, Antique</span>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 relative z-10">
        <div data-spotlight class="getlayers-glass py-9 px-6 sm:px-10 rounded-3xl relative overflow-hidden">
            <!-- Top Neon Accent Line -->
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-rose-500 to-transparent opacity-80"></div>

            @if(session('status'))
                <div class="mb-5 p-3 rounded-xl bg-emerald-950/60 border border-emerald-800 text-emerald-300 text-xs flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-emerald-400"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3 rounded-xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <p class="flex items-center gap-1.5"><i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-400"></i> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Dealership Work Email
                    </label>
                    <div class="relative">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               placeholder="staff@motorelease.ph"
                               class="w-full px-4 py-3 rounded-2xl bg-slate-950/80 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-hidden focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition shadow-inner">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Password
                    </label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-950/80 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-hidden focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition shadow-inner">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-400 hover:text-slate-200">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-rose-600 focus:ring-rose-500">
                        <span>Remember me</span>
                    </label>
                    <span class="text-[11px] text-slate-500">Authorized personnel only</span>
                </div>

                <button type="submit"
                        class="w-full py-3.5 px-4 rounded-2xl text-sm font-black uppercase tracking-wider text-white bg-gradient-to-r from-rose-600 via-red-600 to-rose-700 hover:from-rose-500 hover:to-red-500 shadow-xl shadow-rose-950/60 active:scale-[0.99] transition duration-200">
                    Sign In to Console
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-7 pt-6 border-t border-slate-800/80 text-center">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">
                    One-Click Testing Accounts
                </p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button"
                            onclick="fillDemo('admin@motorelease.ph', 'Password123!')"
                            class="px-2.5 py-2 rounded-xl text-xs font-bold bg-rose-950/70 border border-rose-800/70 text-rose-300 hover:bg-rose-900/60 transition shadow-xs">
                        Admin
                    </button>
                    <button type="button"
                            onclick="fillDemo('manager@motorelease.ph', 'Password123!')"
                            class="px-2.5 py-2 rounded-xl text-xs font-bold bg-blue-950/70 border border-blue-800/70 text-blue-300 hover:bg-blue-900/60 transition shadow-xs">
                        Manager
                    </button>
                    <button type="button"
                            onclick="fillDemo('staff@motorelease.ph', 'Password123!')"
                            class="px-2.5 py-2 rounded-xl text-xs font-bold bg-emerald-950/70 border border-emerald-800/70 text-emerald-300 hover:bg-emerald-900/60 transition shadow-xs">
                        Staff
                    </button>
                </div>
            </div>

            <!-- Link to Customer Portal -->
            <div class="mt-6 text-center">
                <a href="{{ route('customer.track') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition inline-flex items-center gap-1.5">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-rose-400"></i>
                    Switch to Customer Tracking Portal
                </a>
            </div>

        </div>

        <p class="mt-6 text-center text-xs text-slate-500">
            MotoRelease &copy; 2026 &bull; San Jose de Buenavista, Antique, Philippines
        </p>
    </div>

    <script>
        function fillDemo(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
        document.addEventListener('DOMContentLoaded', () => {
            if (window.renderLucideIcons) {
                window.renderLucideIcons();
            }
        });
    </script>
</body>
</html>
