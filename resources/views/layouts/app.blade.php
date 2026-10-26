<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MotoRelease') }} - @yield('title', 'Dealership Management')</title>

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
<body class="h-full antialiased text-slate-800 flex" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden transition-opacity"></div>

    <!-- Sidebar Navigation -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 border-r border-slate-800">
        
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 bg-slate-950 border-b border-slate-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo-icon.svg') }}" alt="MotoRelease" class="w-10 h-10 rounded-xl shadow-lg shadow-red-900/40 object-contain">
                <div>
                    <span class="text-lg font-bold tracking-tight text-white flex items-center gap-1">
                        Moto<span class="text-red-500">Release</span>
                    </span>
                    <span class="block text-[10px] text-slate-400 uppercase tracking-widest font-semibold">Dealership Ops</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <!-- Dealership Branch Badge -->
        <div class="px-6 py-3.5 bg-slate-950/60 border-b border-slate-800/80 flex items-center gap-2.5">
            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
            <div class="truncate text-xs">
                <p class="font-semibold text-slate-200 truncate">{{ auth()->user()->branch?->name ?? 'All Dealership Branches' }}</p>
                <p class="text-[11px] text-slate-400">San Jose de Buenavista, Antique</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1">
            <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Operations</p>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                Dashboard
            </a>

            <a href="{{ route('releases.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-sm hover:from-emerald-500 hover:to-teal-500">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Process Release Claim
            </a>

            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('customers.*') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="users" class="w-4 h-4"></i>
                Customers
            </a>

            <a href="{{ route('motorcycles.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('motorcycles.*') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="bike" class="w-4 h-4"></i>
                Motorcycles
            </a>

            <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-2">Tracking & Releasing</p>

            <a href="{{ route('orcr.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('orcr.*') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="flex items-center gap-3">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    OR/CR Records
                </span>
                @php
                    $pendingOrcrCount = \App\Models\OrcrRecord::whereIn('status', ['awaiting_documents', 'partially_received'])->count();
                @endphp
                @if($pendingOrcrCount > 0)
                    <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-slate-800 text-amber-400 border border-amber-500/30">{{ $pendingOrcrCount }}</span>
                @endif
            </a>

            <a href="{{ route('plates.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('plates.*') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="flex items-center gap-3">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                    License Plates
                </span>
                @php
                    $pendingPlateCount = \App\Models\LicensePlateRecord::whereIn('status', ['awaiting_physical_plate', 'received_for_verification'])->count();
                @endphp
                @if($pendingPlateCount > 0)
                    <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-slate-800 text-blue-400 border border-blue-500/30">{{ $pendingPlateCount }}</span>
                @endif
            </a>

            <a href="{{ route('releases.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('releases.index') || request()->routeIs('releases.show') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                Release Records
            </a>

            <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('notifications.*') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="flex items-center gap-3">
                    <i data-lucide="message-square" class="w-4 h-4"></i>
                    SMS Delivery Logs
                </span>
                @php
                    $failedCount = \App\Models\Notification::where('status', 'failed')->count();
                @endphp
                @if($failedCount > 0)
                    <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-rose-950 text-rose-300 border border-rose-800">{{ $failedCount }}</span>
                @endif
            </a>

            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-2">Management</p>

                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('reports.*') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                    Operational Reports
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-2">Administration</p>

                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('users.*') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="user-cog" class="w-4 h-4"></i>
                    Staff Accounts
                </a>

                <a href="{{ route('settings.branches') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('settings.branches') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="building-2" class="w-4 h-4"></i>
                    Dealership Branches
                </a>

                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('settings.index') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                    System Settings & SMS
                </a>

                <a href="{{ route('settings.audit_logs') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('settings.audit_logs') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    Audit Trail
                </a>
            @endif

            <!-- Public Portal Link -->
            <div class="pt-6">
                <a href="{{ route('customer.track') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 rounded-lg text-xs font-medium text-slate-400 bg-slate-950/80 hover:bg-slate-800 hover:text-slate-200 border border-slate-800">
                    <span class="flex items-center gap-2">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        Customer Portal View
                    </span>
                    <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                </a>
            </div>
        </nav>

        <!-- User Profile Footer -->
        <div class="p-4 bg-slate-950 border-t border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-sm text-slate-200 shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="truncate text-xs">
                    <p class="font-semibold text-slate-200 truncate">{{ auth()->user()->name }}</p>
                    <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-bold uppercase tracking-wider
                        {{ auth()->user()->isAdmin() ? 'bg-red-950 text-red-400 border border-red-800' : (auth()->user()->isManager() ? 'bg-blue-950 text-blue-400 border border-blue-800' : 'bg-slate-800 text-slate-300') }}">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-red-400 hover:bg-slate-900 rounded-lg transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Workspace Column -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

        <!-- Top Header Navigation -->
        <header class="h-14 sm:h-16 bg-white border-b border-slate-200 sticky top-0 z-30 flex items-center justify-between px-3 sm:px-8 shadow-xs">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100 shrink-0 min-h-[44px] min-w-[44px] flex items-center justify-center cursor-pointer" aria-label="Open sidebar menu">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="min-w-0">
                    <h1 class="text-xs sm:text-base lg:text-lg font-bold text-slate-900 truncate">@yield('page-title', 'Dealership Management')</h1>
                </div>
            </div>

            <!-- Header Right Actions -->
            <div class="flex items-center gap-3">
                <!-- Quick Link to Customer Lookup -->
                <a href="{{ route('releases.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition">
                    <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                    Scan / Claim Item
                </a>

                <!-- Profile Dropdown Menu -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition text-left">
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden md:block text-xs font-semibold text-slate-700">{{ auth()->user()->name }}</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                    </button>

                    <div x-show="open" x-cloak
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50 text-xs">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="font-bold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-slate-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50">
                            <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                            My Account & Password
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 text-left">
                                <i data-lucide="log-out" class="w-4 h-4 text-red-500"></i>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Notifications Alert Center -->
        <main class="flex-1 p-4 sm:p-8">
            <div class="max-w-7xl mx-auto space-y-6">

                @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start justify-between shadow-xs" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center gap-3">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                            <p class="text-sm font-medium">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-800"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-start justify-between shadow-xs" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center gap-3">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0"></i>
                            <p class="text-sm font-medium">{{ session('warning') }}</p>
                        </div>
                        <button @click="show = false" class="text-amber-500 hover:text-amber-800"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-xs" x-data="{ show: true }" x-show="show">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-bold">Please correct the following errors:</p>
                                    <ul class="list-disc list-inside text-xs mt-1 space-y-0.5 text-rose-700">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button @click="show = false" class="text-rose-500 hover:text-rose-800"><i data-lucide="x" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                @endif

                <!-- Dynamic Content Injection -->
                @yield('content')

            </div>
        </main>

        <!-- Dealership System Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 px-6 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p><strong>MotoRelease</strong> &copy; {{ date('Y') }} — Independent Dealership Document Tracking & Releasing System.</p>
            <p class="text-slate-400">Compliant with Philippine Data Privacy Act of 2012 (RA 10173). Not officially affiliated with LTO.</p>
        </footer>
    </div>

    <!-- Re-render Lucide icons on Alpine changes -->
    <script>
        document.addEventListener('alpine:initialized', () => {
            if (window.renderLucideIcons) {
                window.renderLucideIcons();
            }
        });
    </script>
</body>
</html>
