<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MotoRelease') }} - Customer Tracking Portal</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles and Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full antialiased text-slate-800 flex flex-col justify-between">

    <!-- Customer Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('customer.track') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo-icon.svg') }}" alt="MotoRelease" class="w-10 h-10 rounded-xl shadow-md shadow-red-900/20 object-contain">
                <div>
                    <span class="text-base font-extrabold tracking-tight text-slate-900">
                        Moto<span class="text-red-600">Release</span>
                    </span>
                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Document Tracking Portal</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    San Jose de Buenavista, Antique
                </span>

                @if(session('customer_tracking_ref'))
                    <form action="{{ route('customer.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition flex items-center gap-1.5">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            Exit Tracking
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-medium text-slate-500 hover:text-slate-900 flex items-center gap-1">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                        Staff Portal
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 py-8 px-4 sm:px-6">
        <div class="max-w-4xl mx-auto">
            @if(session('status'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-xs">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center gap-3 shadow-xs">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Customer Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 px-4 text-center text-xs text-slate-500">
        <div class="max-w-4xl mx-auto space-y-2">
            <p class="font-medium text-slate-700">MotoRelease &mdash; San Jose de Buenavista, Antique, Philippines</p>
            <p class="text-[11px] text-slate-400">
                Notice: This portal displays physical document and license plate availability verified directly at the dealership branch.
                This is an independent customer notification system and is not an official LTO government portal.
            </p>
            <p class="text-[11px] text-slate-400">
                Data Privacy Act of 2012 (RA 10173) Compliant &bull; Sensitive motorcycle identifiers are masked for security.
            </p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.renderLucideIcons) {
                window.renderLucideIcons();
            }
        });
    </script>
</body>
</html>
