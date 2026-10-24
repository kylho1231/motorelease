<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MotoRelease') }} - Employee Login</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full antialiased flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        <!-- Logo -->
        <img src="{{ asset('images/logo-icon.svg') }}" alt="MotoRelease Emblem" class="mx-auto w-16 h-16 rounded-2xl shadow-xl shadow-red-900/40 object-contain mb-4">
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
            Moto<span class="text-red-500">Release</span> System
        </h2>
        <p class="mt-2 text-xs sm:text-sm text-slate-400">
            Dealership Document & Plate Releasing Portal
        </p>
        <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            San Jose de Buenavista, Antique Branch
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-slate-900/80 backdrop-blur-md py-8 px-6 sm:px-10 shadow-2xl rounded-2xl border border-slate-800">

            @if(session('status'))
                <div class="mb-5 p-3 rounded-xl bg-emerald-950/60 border border-emerald-800 text-emerald-300 text-xs flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3 rounded-xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <p class="flex items-center gap-1.5"><i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Dealership Work Email
                    </label>
                    <div class="relative">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               placeholder="e.g. staff@motorelease.ph"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Password
                    </label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           placeholder="••••••••"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-400 hover:text-slate-200">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-red-600 focus:ring-red-500">
                        <span>Remember me</span>
                    </label>
                    <span class="text-slate-500">Authorized personnel only</span>
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 focus:outline-hidden focus:ring-2 focus:ring-red-500 shadow-lg shadow-red-900/40 transition">
                    Sign In to Console
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-6 pt-6 border-t border-slate-800 text-center">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2.5">
                    Demo Accounts (One-Click Auto Fill)
                </p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button"
                            onclick="fillDemo('admin@motorelease.ph', 'Password123!')"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-red-950/60 border border-red-800/80 text-red-300 hover:bg-red-900/60 transition">
                        Admin
                    </button>
                    <button type="button"
                            onclick="fillDemo('manager@motorelease.ph', 'Password123!')"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-blue-950/60 border border-blue-800/80 text-blue-300 hover:bg-blue-900/60 transition">
                        Manager
                    </button>
                    <button type="button"
                            onclick="fillDemo('staff@motorelease.ph', 'Password123!')"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-950/60 border border-emerald-800/80 text-emerald-300 hover:bg-emerald-900/60 transition">
                        Staff
                    </button>
                </div>
            </div>

            <!-- Link to Customer Portal -->
            <div class="mt-5 text-center">
                <a href="{{ route('customer.track') }}" class="text-xs text-slate-400 hover:text-white transition flex items-center justify-center gap-1.5">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Switch to Customer Tracking Portal
                </a>
            </div>

        </div>

        <p class="mt-6 text-center text-xs text-slate-500">
            MotoRelease &copy; {{ date('Y') }} &bull; San Jose de Buenavista, Antique, Philippines
        </p>
    </div>

    <script>
        function fillDemo(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>
