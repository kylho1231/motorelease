<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MotoRelease') }} - Customer Tracking Portal</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles and Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full antialiased text-slate-200 getlayers-mesh-bg flex flex-col justify-between selection:bg-rose-500 selection:text-white">

    <!-- GetLayers Cinematic Atmospheric Overlay -->
    <div class="fixed inset-0 getlayers-speed-grid opacity-30 pointer-events-none z-0"></div>

    <!-- Customer Header -->
    <header class="bg-slate-950/80 backdrop-blur-xl border-b border-slate-800/80 sticky top-0 z-40 shadow-2xl">
        <div class="max-w-6xl mx-auto px-3 sm:px-6 h-14 sm:h-16 lg:h-18 flex items-center justify-between relative z-10">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3.5 group min-w-0">
                <div class="relative shrink-0">
                    <div class="absolute -inset-1 bg-gradient-to-r from-rose-600 to-red-600 rounded-xl sm:rounded-2xl blur-xs opacity-70 group-hover:opacity-100 transition duration-300"></div>
                    <img src="{{ asset('images/logo-icon.svg') }}" alt="MotoRelease" class="relative w-8 h-8 sm:w-10 sm:h-10 lg:w-11 lg:h-11 rounded-lg sm:rounded-xl shadow-xl object-contain">
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="text-sm sm:text-base lg:text-lg font-black tracking-tight text-white whitespace-nowrap">
                            Moto<span class="text-rose-500">Release</span>
                        </span>
                        <span class="px-1.5 py-0.2 sm:px-2 sm:py-0.5 rounded-full text-[8px] sm:text-[9px] font-black uppercase tracking-wider sm:tracking-widest bg-rose-950/80 text-rose-300 border border-rose-800/60 shadow-xs">Yamaha</span>
                    </div>
                    <span class="hidden md:block text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate">Document &amp; License Plate Tracking</span>
                </div>
            </a>

            <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                <div class="hidden lg:inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-900/90 text-slate-300 border border-slate-700/80 shadow-inner">
                    <span class="relative flex h-2 w-2">
                        <span class="getlayers-radar absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="tracking-wide">San Jose de Buenavista, Antique</span>
                </div>

                <a href="{{ route('home') }}" class="text-xs font-semibold px-2.5 sm:px-3 py-1.5 rounded-lg sm:rounded-xl {{ request()->is('/') ? 'bg-slate-800/90 text-white border-slate-700' : 'text-slate-400 hover:text-white hover:bg-slate-900/80 border-transparent' }} border transition flex items-center gap-1 sm:gap-1.5">
                    <i data-lucide="home" class="w-3.5 h-3.5 text-rose-400"></i>
                    <span class="hidden sm:inline">Home</span>
                </a>

                <a href="{{ route('customer.track') }}" class="text-xs font-semibold px-2.5 sm:px-3.5 py-1.5 rounded-lg sm:rounded-xl {{ request()->is('track*') ? 'bg-rose-600 text-white shadow-lg shadow-rose-950/50' : 'bg-slate-900/90 hover:bg-slate-800 text-slate-200 border border-slate-800 hover:border-slate-700' }} transition flex items-center gap-1 sm:gap-1.5">
                    <i data-lucide="search" class="w-3.5 h-3.5 {{ request()->is('track*') ? 'text-white' : 'text-rose-400' }}"></i>
                    <span>Track<span class="hidden sm:inline"> Status</span></span>
                </a>

                @if(session('customer_tracking_ref'))
                    <form action="{{ route('customer.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-bold px-2.5 sm:px-3 py-1.5 rounded-lg sm:rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700 text-slate-300 hover:text-white transition flex items-center gap-1 sm:gap-1.5 shadow-md">
                            <i data-lucide="log-out" class="w-3.5 h-3.5 text-rose-400"></i>
                            <span class="hidden sm:inline">Exit</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 @yield('main_padding', 'py-6 px-3.5 sm:py-10 sm:px-6') relative z-10">
        <div class="@yield('container_class', 'max-w-4xl mx-auto')">
            @if(session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-950/60 border border-emerald-800/80 text-emerald-200 text-sm flex items-center gap-3 shadow-lg shadow-emerald-950/40 backdrop-blur-md">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 p-4 rounded-2xl bg-amber-950/60 border border-amber-800/80 text-amber-200 text-sm flex items-center gap-3 shadow-lg shadow-amber-950/40 backdrop-blur-md">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-400 shrink-0"></i>
                    <span class="font-medium">{{ session('warning') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-950/60 border border-rose-800/80 text-rose-200 text-sm shadow-lg shadow-rose-950/40 backdrop-blur-md">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-400 shrink-0 mt-0.5"></i>
                        <ul class="list-disc list-inside space-y-1 font-medium">
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
    <footer class="bg-slate-950/90 border-t border-slate-900 py-10 px-4 sm:px-6 text-center text-xs text-slate-400 relative z-10 backdrop-blur-xl">
        <div class="max-w-6xl mx-auto space-y-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-slate-900/80 pb-6">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-icon.svg') }}" alt="Yamaha" class="w-8 h-8 rounded-lg object-contain">
                    <div class="text-left">
                        <span class="text-sm font-black text-white">Moto<span class="text-rose-500">Release</span></span>
                        <p class="text-[10px] text-slate-500 font-medium">Yamaha Authorized Dealership &bull; Document Logistics Hub</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-xs font-medium text-slate-400">
                    <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                    <span>&bull;</span>
                    <a href="{{ route('customer.track') }}" class="hover:text-white transition">Track OR/CR &amp; Plate</a>
                    <span>&bull;</span>
                    <span class="text-slate-500">Hotline: 0917-123-4567</span>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                <p class="font-bold text-slate-300">San Jose de Buenavista, Antique, Philippines &mdash; Mon - Sat 8:00 AM - 5:00 PM</p>
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
            </div>
            <p class="text-[11px] text-slate-500 max-w-3xl mx-auto leading-relaxed">
                Notice: Displays verified physical availability of motorcycle documents (LTO Official Receipt &amp; Certificate of Registration) and license plates in the dealership branch vault. This is an independent customer notification system and is not an official LTO government portal.
            </p>
            <div class="pt-2 flex flex-wrap items-center justify-center gap-3 sm:gap-4 text-[10px] text-slate-500 font-mono">
                <span>RA 10173 PRIVACY COMPLIANT</span>
                <span>&bull;</span>
                <span>MASKED ENGINE &amp; CHASSIS IDS</span>
                <span>&bull;</span>
                <span>PHYSICAL VAULT VERIFICATION</span>
                <span>&bull;</span>
                <span>ENCRYPTED SECURE SESSION</span>
            </div>
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
