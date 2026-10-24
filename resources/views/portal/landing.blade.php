@extends('layouts.portal')

@section('container_class', 'max-w-6xl mx-auto space-y-12 sm:space-y-18 lg:space-y-24')

@section('content')

<!-- =====================================================================
     HERO SECTION: 3D Holographic Vault Telemetry & Fast-Track Search
     ===================================================================== -->
<div class="relative pt-2 sm:pt-6 lg:pt-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-8 items-center">
        
        <!-- Left Hero Column: Value Proposition & Direct Tracking -->
        <div class="lg:col-span-7 space-y-4 sm:space-y-6 text-left">
            
            <!-- Yamaha Authorized Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 sm:px-4 sm:py-2 rounded-full text-xs font-bold bg-slate-900/90 text-rose-300 border border-rose-500/30 shadow-lg shadow-rose-950/40 backdrop-blur-md">
                <span class="relative flex h-2 w-2">
                    <span class="getlayers-radar absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                </span>
                <span class="tracking-wider uppercase text-[10px] sm:text-[11px] font-black">Official Yamaha 3S Dealership &bull; Antique</span>
            </div>

            <!-- Cinematic Headline -->
            <h1 class="text-2xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-white tracking-tight leading-tight sm:leading-[1.1]">
                Track Your Yamaha <br class="hidden sm:inline">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-500 via-red-500 to-amber-400">
                    OR/CR &amp; Plate
                </span> <br>
                in Real-Time.
            </h1>

            <p class="text-xs sm:text-sm lg:text-base text-slate-400 leading-relaxed max-w-xl">
                Verify the live physical availability of your official LTO documents and license plate inside our branch vault. No wasted trips &mdash; track instantly with your claim code and registered mobile PIN.
            </p>

            <!-- Direct Quick-Search / Fast Track Form -->
            <div class="p-1.5 sm:p-2.5 rounded-xl sm:rounded-2xl bg-slate-900/90 border border-slate-800 shadow-2xl backdrop-blur-xl max-w-xl">
                <form action="{{ route('customer.track') }}" method="GET" class="flex flex-col sm:flex-row gap-1.5 sm:gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="qr-code" class="w-4 h-4 text-rose-400"></i>
                        </div>
                        <input type="text" name="ref" placeholder="Enter Reference (e.g. MR-SJA-2026-000003)"
                               class="w-full pl-9 sm:pl-10 pr-3 py-2.5 sm:py-3 rounded-lg sm:rounded-xl bg-slate-950/90 border border-slate-800 text-white font-mono text-xs font-semibold placeholder-slate-500 focus:outline-hidden focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition shadow-inner">
                    </div>
                    <button type="submit"
                            class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg sm:rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white text-xs font-black uppercase tracking-wider shadow-lg shadow-rose-950/50 hover:shadow-rose-900/60 active:scale-[0.98] transition flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                        <span>Check Status</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            </div>

            <!-- Quick Demo References Pills -->
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 pt-0.5 sm:pt-1 text-xs text-slate-400 max-w-xl">
                <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-500">Try Sample:</span>
                <a href="{{ route('customer.track', ['ref' => 'MR-SJA-2026-000003']) }}"
                   class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md sm:rounded-lg bg-slate-900/80 hover:bg-slate-800 text-emerald-300 border border-emerald-800/40 text-[10px] sm:text-[11px] font-mono transition flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Ready (NMAX 155)</span>
                </a>
                <a href="{{ route('customer.track', ['ref' => 'MR-SJA-2026-000002']) }}"
                   class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md sm:rounded-lg bg-slate-900/80 hover:bg-slate-800 text-amber-300 border border-amber-800/40 text-[10px] sm:text-[11px] font-mono transition flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    <span>Partial (Aerox 155)</span>
                </a>
            </div>

            <!-- Key Proof Points -->
            <div class="pt-3 sm:pt-4 grid grid-cols-3 gap-2 sm:gap-4 border-t border-slate-800/60 max-w-xl">
                <div>
                    <span class="block text-base sm:text-xl lg:text-2xl font-black text-white">100%</span>
                    <span class="text-[9px] sm:text-xs text-slate-400 uppercase tracking-wider font-semibold">Vault Verified</span>
                </div>
                <div>
                    <span class="block text-base sm:text-xl lg:text-2xl font-black text-rose-400">Instant</span>
                    <span class="text-[9px] sm:text-xs text-slate-400 uppercase tracking-wider font-semibold">SMS Dispatch</span>
                </div>
                <div>
                    <span class="block text-base sm:text-xl lg:text-2xl font-black text-emerald-400">RA 10173</span>
                    <span class="text-[9px] sm:text-xs text-slate-400 uppercase tracking-wider font-semibold">Privacy Safe</span>
                </div>
            </div>

        </div>

        <!-- Right Hero Column: 3D Perspective Tilt Card with Live Telemetry -->
        <div class="lg:col-span-5 relative mt-4 lg:mt-0">
            
            <!-- Ambient Glow Aura -->
            <div class="absolute -inset-4 bg-gradient-to-tr from-rose-600/30 via-red-600/20 to-amber-500/10 rounded-3xl blur-2xl opacity-60 animate-pulse-glow pointer-events-none"></div>

            <!-- Floating Pill Badge (Top Left) -->
            <div class="absolute -top-4 -left-3 z-20 hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900/90 border border-slate-700/80 shadow-2xl backdrop-blur-md animate-float-slow text-[10px] text-slate-200 font-bold">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                <span>Dealership Vault Secured</span>
            </div>

            <!-- Floating Pill Badge (Bottom Right) -->
            <div class="absolute -bottom-4 -right-3 z-20 hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900/90 border border-slate-700/80 shadow-2xl backdrop-blur-md animate-float-reverse text-[10px] text-slate-200 font-bold">
                <i data-lucide="bell-ring" class="w-3.5 h-3.5 text-rose-400"></i>
                <span>Automated SMS Alert Ready</span>
            </div>

            <!-- Interactive 3D Card -->
            <div data-tilt class="getlayers-glass rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-7 relative border border-slate-700/70 shadow-2xl backdrop-blur-2xl transition-all duration-300">
                
                <!-- Card Header -->
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3 sm:pb-4 mb-4 sm:mb-5">
                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <img src="{{ asset('images/logo-icon.svg') }}" alt="Yamaha Logo" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl object-contain shadow-md">
                        <div>
                            <span class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-white block">Document Vault HUD</span>
                            <span class="text-[9px] sm:text-[10px] text-slate-400 font-mono">LIVE DEALERSHIP TELEMETRY</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-widest bg-emerald-950/80 text-emerald-300 border border-emerald-800/60 shadow-xs flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        LIVE
                    </span>
                </div>

                <!-- Motorcycle Model Snapshot -->
                <div class="p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl bg-slate-950/70 border border-slate-800/90 mb-4 sm:mb-5 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-slate-500 block">Unit Registered</span>
                        <h4 class="text-xs sm:text-sm font-extrabold text-white">Yamaha NMAX 155 ABS</h4>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 font-mono">Ref: MR-SJA-2026-000003</span>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 overflow-hidden shrink-0">
                        <img src="{{ asset('images/motorcycles/yamaha-nmax.jpg') }}" alt="NMAX" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Document Checklist Meters -->
                <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                    
                    <!-- LTO Official Receipt (OR) -->
                    <div class="p-2.5 sm:p-3 rounded-lg sm:rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-center justify-between">
                        <div class="flex items-center gap-2 sm:gap-2.5">
                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-emerald-950 text-emerald-400 border border-emerald-800/60 flex items-center justify-center">
                                <i data-lucide="check" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
                            </div>
                            <div>
                                <span class="text-[11px] sm:text-xs font-bold text-white block">Official Receipt (OR)</span>
                                <span class="text-[9px] sm:text-[10px] text-slate-500">Verified &bull; In Branch Safe</span>
                            </div>
                        </div>
                        <span class="text-[9px] sm:text-[10px] font-extrabold px-1.5 py-0.5 sm:px-2 sm:py-0.5 rounded bg-emerald-900/50 text-emerald-300 border border-emerald-700/50">
                            Available
                        </span>
                    </div>

                    <!-- LTO Certificate of Registration (CR) -->
                    <div class="p-2.5 sm:p-3 rounded-lg sm:rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-center justify-between">
                        <div class="flex items-center gap-2 sm:gap-2.5">
                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-emerald-950 text-emerald-400 border border-emerald-800/60 flex items-center justify-center">
                                <i data-lucide="check" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
                            </div>
                            <div>
                                <span class="text-[11px] sm:text-xs font-bold text-white block">Certificate of Reg. (CR)</span>
                                <span class="text-[9px] sm:text-[10px] text-slate-500">Original Copy Verified</span>
                            </div>
                        </div>
                        <span class="text-[9px] sm:text-[10px] font-extrabold px-1.5 py-0.5 sm:px-2 sm:py-0.5 rounded bg-emerald-900/50 text-emerald-300 border border-emerald-700/50">
                            Available
                        </span>
                    </div>

                    <!-- LTO License Plate -->
                    <div class="p-2.5 sm:p-3 rounded-lg sm:rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-center justify-between">
                        <div class="flex items-center gap-2 sm:gap-2.5">
                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-rose-950 text-rose-400 border border-rose-800/60 flex items-center justify-center">
                                <i data-lucide="award" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
                            </div>
                            <div>
                                <span class="text-[11px] sm:text-xs font-bold text-white block">LTO Metal Plate</span>
                                <span class="text-[9px] sm:text-[10px] text-slate-500">Drawer #A-14 &bull; Inspected</span>
                            </div>
                        </div>
                        <span class="text-[9px] sm:text-[10px] font-extrabold px-1.5 py-0.5 sm:px-2 sm:py-0.5 rounded bg-rose-900/50 text-rose-300 border border-rose-700/50">
                            Ready
                        </span>
                    </div>

                </div>

                <!-- Call to Action Button inside Tilt Card -->
                <a href="{{ route('customer.track') }}"
                   class="w-full py-2.5 sm:py-3 px-3 sm:px-4 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-extrabold uppercase tracking-wider text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-slate-600 transition flex items-center justify-center gap-2">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-rose-400"></i>
                    <span>Track Your Motorcycle Now</span>
                </a>

            </div>

        </div>

    </div>
</div>


<!-- =====================================================================
     SECTION 2: 5 YAMAHA MOTORCYCLES SHOWCASE WITH 3D HOVER CARDS
     ===================================================================== -->
<div class="space-y-6 sm:space-y-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-3 sm:gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 sm:gap-2 text-rose-400 text-[10px] sm:text-xs font-bold uppercase tracking-widest mb-1 sm:mb-2">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                <span>Yamaha Authorized Fleet</span>
            </div>
            <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
                Featured Yamaha Motorcycles in Antique
            </h2>
            <p class="text-[11px] sm:text-xs md:text-sm text-slate-400 max-w-xl mt-1">
                All models purchased from our San Jose dealership are equipped with digital claim slips and real-time OR/CR status tracking.
            </p>
        </div>
        <a href="{{ route('customer.track') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-400 hover:text-rose-300 transition shrink-0">
            <span>Track purchased unit</span>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </a>
    </div>

    <!-- 5 Showcase Motorcycle Grid with 3D Tilt Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">

        <!-- 1. Yamaha NMAX 155 ABS -->
        <div data-tilt class="getlayers-glass rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-800 hover:border-rose-500/40 transition duration-300 group flex flex-col justify-between">
            <div>
                <div class="relative w-full h-40 sm:h-48 rounded-xl sm:rounded-2xl overflow-hidden bg-slate-950 mb-3 sm:mb-4 border border-slate-800/80">
                    <img src="{{ asset('images/motorcycles/yamaha-nmax.jpg') }}" alt="Yamaha NMAX 155" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-2.5 left-2.5 sm:top-3 sm:left-3 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-slate-950/80 text-white backdrop-blur-md border border-slate-800">
                        Maxi-Scooter
                    </div>
                    <div class="absolute top-2.5 right-2.5 sm:top-3 sm:right-3 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-rose-600/90 text-white backdrop-blur-md">
                        155cc VVA
                    </div>
                </div>

                <div class="space-y-1 mb-2.5 sm:mb-3">
                    <h3 class="text-base sm:text-lg font-black text-white group-hover:text-rose-400 transition">Yamaha NMAX 155 ABS</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                        The ultimate luxury maxi-scooter with Dual-Channel ABS, Traction Control, and Y-Connect smartphone integration.
                    </p>
                </div>

                <div class="flex flex-wrap gap-1 sm:gap-1.5 mb-3 sm:mb-4">
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">Dual ABS</span>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">Smart Key</span>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">7.1L Fuel Tank</span>
                </div>
            </div>

            <div class="pt-2.5 sm:pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-[10px] sm:text-[11px] text-emerald-400 font-bold flex items-center gap-1">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Vault Tracking Active
                </span>
                <a href="{{ route('customer.track', ['ref' => 'MR-SJA-2026-000003']) }}" class="text-[10px] sm:text-[11px] font-bold text-rose-400 hover:text-white transition">
                    View Demo &rarr;
                </a>
            </div>
        </div>

        <!-- 2. Yamaha Mio Aerox 155 -->
        <div data-tilt class="getlayers-glass rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-800 hover:border-rose-500/40 transition duration-300 group flex flex-col justify-between">
            <div>
                <div class="relative w-full h-40 sm:h-48 rounded-xl sm:rounded-2xl overflow-hidden bg-slate-950 mb-3 sm:mb-4 border border-slate-800/80">
                    <img src="{{ asset('images/motorcycles/yamaha-aerox.jpg') }}" alt="Yamaha Mio Aerox 155" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-2.5 left-2.5 sm:top-3 sm:left-3 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-slate-950/80 text-white backdrop-blur-md border border-slate-800">
                        Supersport Automatic
                    </div>
                    <div class="absolute top-2.5 right-2.5 sm:top-3 sm:right-3 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-rose-600/90 text-white backdrop-blur-md">
                        155cc VVA
                    </div>
                </div>

                <div class="space-y-1 mb-2.5 sm:mb-3">
                    <h3 class="text-base sm:text-lg font-black text-white group-hover:text-rose-400 transition">Yamaha Mio Aerox 155</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                        Aggressive supersport scooter engineered with Yamaha R-Series DNA, wide tires, and lightning acceleration.
                    </p>
                </div>

                <div class="flex flex-wrap gap-1 sm:gap-1.5 mb-3 sm:mb-4">
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">Blue Core</span>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">Front ABS</span>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">5.5L Tank</span>
                </div>
            </div>

            <div class="pt-2.5 sm:pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-[10px] sm:text-[11px] text-amber-400 font-bold flex items-center gap-1">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i> OR/CR Processing
                </span>
                <a href="{{ route('customer.track', ['ref' => 'MR-SJA-2026-000002']) }}" class="text-[10px] sm:text-[11px] font-bold text-rose-400 hover:text-white transition">
                    View Demo &rarr;
                </a>
            </div>
        </div>

        <!-- 3. Yamaha Sniper 155R -->
        <div data-tilt class="getlayers-glass rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-800 hover:border-rose-500/40 transition duration-300 group flex flex-col justify-between">
            <div>
                <div class="relative w-full h-40 sm:h-48 rounded-xl sm:rounded-2xl overflow-hidden bg-slate-950 mb-3 sm:mb-4 border border-slate-800/80">
                    <img src="{{ asset('images/motorcycles/yamaha-sniper.jpg') }}" alt="Yamaha Sniper 155" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-2.5 left-2.5 sm:top-3 sm:left-3 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-slate-950/80 text-white backdrop-blur-md border border-slate-800">
                        King of Underbone
                    </div>
                    <div class="absolute top-2.5 right-2.5 sm:top-3 sm:right-3 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-rose-600/90 text-white backdrop-blur-md">
                        155cc Manual
                    </div>
                </div>

                <div class="space-y-1 mb-2.5 sm:mb-3">
                    <h3 class="text-base sm:text-lg font-black text-white group-hover:text-rose-400 transition">Yamaha Sniper 155R</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                        Precision racetrack control with Assist &amp; Slipper Clutch, 6-speed gearbox, and race-tuned aerodynamic chassis.
                    </p>
                </div>

                <div class="flex flex-wrap gap-1 sm:gap-1.5 mb-3 sm:mb-4">
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">6-Speed</span>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">Slipper Clutch</span>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">Dual Piston</span>
                </div>
            </div>

            <div class="pt-2.5 sm:pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-[10px] sm:text-[11px] text-blue-400 font-bold flex items-center gap-1">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Plate Released
                </span>
                <a href="{{ route('customer.track', ['ref' => 'MR-SJA-2026-000005']) }}" class="text-[10px] sm:text-[11px] font-bold text-rose-400 hover:text-white transition">
                    View Demo &rarr;
                </a>
            </div>
        </div>

        <!-- 4. Yamaha Mio Fazzio Hybrid -->
        <div data-tilt class="getlayers-glass rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-800 hover:border-rose-500/40 transition duration-300 group flex flex-col justify-between">
            <div>
                <div class="relative w-full h-40 sm:h-48 rounded-xl sm:rounded-2xl overflow-hidden bg-slate-950 mb-3 sm:mb-4 border border-slate-800/80">
                    <img src="{{ asset('images/motorcycles/yamaha-fazzio.jpg') }}" alt="Yamaha Mio Fazzio" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-2.5 left-2.5 sm:top-3 sm:left-3 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-slate-950/80 text-white backdrop-blur-md border border-slate-800">
                        Neo-Retro
                    </div>
                    <div class="absolute top-2.5 right-2.5 sm:top-3 sm:right-3 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-teal-500 text-white backdrop-blur-md">
                        125cc Hybrid
                    </div>
                </div>

                <div class="space-y-1 mb-2.5 sm:mb-3">
                    <h3 class="text-base sm:text-lg font-black text-white group-hover:text-rose-400 transition">Yamaha Mio Fazzio Hybrid</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                        Stylish neo-retro commuter combining Blue Core Hybrid power assist, oval LED lights, and digital cockpit.
                    </p>
                </div>

                <div class="flex flex-wrap gap-1 sm:gap-1.5 mb-3 sm:mb-4">
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">Hybrid Power</span>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">Smart Motor</span>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">USB Charger</span>
                </div>
            </div>

            <div class="pt-2.5 sm:pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-[10px] sm:text-[11px] text-emerald-400 font-bold flex items-center gap-1">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Vault Tracking Active
                </span>
                <a href="{{ route('customer.track') }}" class="text-[10px] sm:text-[11px] font-bold text-rose-400 hover:text-white transition">
                    Track Unit &rarr;
                </a>
            </div>
        </div>

        <!-- 5. Yamaha MT-15 / XSR155 Street -->
        <div data-tilt class="getlayers-glass rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-800 hover:border-rose-500/40 transition duration-300 group flex flex-col justify-between md:col-span-2 lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 items-center">
                <div class="relative w-full h-44 sm:h-52 md:h-60 rounded-xl sm:rounded-2xl overflow-hidden bg-slate-950 border border-slate-800/80">
                    <img src="{{ asset('images/motorcycles/yamaha-mt15.jpg') }}" alt="Yamaha MT-15" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-2.5 left-2.5 sm:top-3 sm:left-3 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-slate-950/80 text-white backdrop-blur-md border border-slate-800">
                        Hyper Naked Street
                    </div>
                    <div class="absolute top-2.5 right-2.5 sm:top-3 sm:right-3 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-rose-600/90 text-white backdrop-blur-md">
                        155cc VVA USD
                    </div>
                </div>

                <div class="space-y-2 sm:space-y-3">
                    <div class="space-y-1">
                        <h3 class="text-base sm:text-xl font-black text-white group-hover:text-rose-400 transition">Yamaha MT-15 / XSR155</h3>
                        <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                            Dark Side of Japan attitude featuring upside-down (USD) gold front forks, aluminum swingarm, Deltabox chassis, and VVA torque delivery.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-1 sm:gap-1.5">
                        <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">USD Forks</span>
                        <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">Deltabox Frame</span>
                        <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">Liquid-Cooled VVA</span>
                        <span class="px-1.5 sm:px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] font-medium text-slate-300">Slipper Clutch</span>
                    </div>

                    <div class="pt-2.5 sm:pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                        <span class="text-[10px] sm:text-[11px] text-emerald-400 font-bold flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Full Tracking Integration
                        </span>
                        <a href="{{ route('customer.track') }}" class="text-xs font-bold px-3 py-1.5 rounded-lg sm:rounded-xl bg-rose-600 hover:bg-rose-500 text-white transition">
                            Track Reference &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- =====================================================================
     SECTION 3: HOW TO GET YOUR PIN & TRACK YOUR DOCUMENTS
     ===================================================================== -->
<div class="p-4 sm:p-8 lg:p-12 rounded-2xl sm:rounded-3xl getlayers-glass border border-slate-800/90 shadow-2xl relative overflow-hidden">
    <!-- Background Gradient Accent -->
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-rose-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-2xl mx-auto text-center space-y-2 sm:space-y-3 mb-6 sm:mb-10">
        <span class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-rose-400">Customer Guide &amp; Instructions</span>
        <h2 class="text-xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight">
            How Do I Track &amp; Get My PIN?
        </h2>
        <p class="text-[11px] sm:text-xs md:text-sm text-slate-400 leading-relaxed">
            We prioritize your vehicle identity security. Our system requires two-factor validation: your Tracking Reference and your 4-digit Security PIN.
        </p>
    </div>

    <!-- 3-Step Interactive Process -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-6 relative">
        
        <!-- Step 1 -->
        <div class="p-4 sm:p-6 rounded-xl sm:rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-2 sm:space-y-3 relative group hover:border-rose-500/40 transition">
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-rose-400 font-black text-xs sm:text-sm">
                01
            </div>
            <h3 class="text-sm sm:text-base font-extrabold text-white">Find Your Tracking Code</h3>
            <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                Check your printed <strong>MotoRelease Claim Slip</strong> or official delivery invoice. Your reference looks like <code class="text-rose-300 font-mono text-[10px] sm:text-[11px] bg-slate-900 px-1 py-0.5 rounded">MR-SJA-2026-000003</code>.
            </p>
        </div>

        <!-- Step 2 (The PIN answer) -->
        <div class="p-4 sm:p-6 rounded-xl sm:rounded-2xl bg-slate-950/80 border-2 border-rose-500/50 space-y-2 sm:space-y-3 relative group shadow-xl shadow-rose-950/40">
            <div class="absolute -top-2.5 right-3 sm:-top-3 sm:right-4 px-2 py-0.5 rounded-full text-[8px] sm:text-[9px] font-black uppercase tracking-wider bg-rose-600 text-white">
                Verification PIN
            </div>
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-rose-950/80 border border-rose-800/80 flex items-center justify-center text-rose-300 font-black text-xs sm:text-sm">
                02
            </div>
            <h3 class="text-sm sm:text-base font-extrabold text-white">Use Your 4-Digit PIN</h3>
            <p class="text-[11px] sm:text-xs text-slate-300 leading-relaxed">
                Your PIN is the <strong class="text-white">last 4 digits</strong> of your registered mobile phone number (e.g. 0917-xxx-<span class="text-emerald-400 font-mono font-black">1234</span> &rarr; PIN: <strong class="text-emerald-400 font-mono">1234</strong>). It is also printed on your physical claim stub.
            </p>
        </div>

        <!-- Step 3 -->
        <div class="p-4 sm:p-6 rounded-xl sm:rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-2 sm:space-y-3 relative group hover:border-emerald-500/40 transition">
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-emerald-400 font-black text-xs sm:text-sm">
                03
            </div>
            <h3 class="text-sm sm:text-base font-extrabold text-white">Live Vault Verification</h3>
            <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                View whether your OR/CR documents are inside the branch drawer, check if your physical plate is received, and claim them when ready!
            </p>
        </div>

    </div>

    <!-- Need Help Callout -->
    <div class="mt-6 sm:mt-8 p-3 sm:p-4 rounded-xl bg-slate-900/90 border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 text-xs">
        <div class="flex items-center gap-2.5 sm:gap-3">
            <div class="p-1.5 sm:p-2 rounded-lg bg-rose-950/80 border border-rose-800/60 text-rose-400 shrink-0">
                <i data-lucide="help-circle" class="w-4 h-4"></i>
            </div>
            <span class="text-[11px] sm:text-xs text-slate-300">
                Cannot find your reference code or changed your mobile number?
            </span>
        </div>
        <a href="tel:09171234567" class="w-full sm:w-auto px-3.5 sm:px-4 py-2 rounded-lg sm:rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold transition flex items-center justify-center gap-2 shrink-0 text-xs">
            <i data-lucide="phone-call" class="w-3.5 h-3.5 text-rose-400"></i>
            <span>Call Dealership Desk: 0917-123-4567</span>
        </a>
    </div>
</div>


<!-- =====================================================================
     SECTION 4: TRUST & SYSTEM SECURITY ASSURANCE
     ===================================================================== -->
<div class="space-y-6 sm:space-y-8">
    <div class="text-center space-y-1.5 sm:space-y-2">
        <span class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-slate-400">Dealership Standard</span>
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
            Why Yamaha MotoRelease in San Jose Antique?
        </h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-5">
        
        <div class="p-4 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-1.5 sm:space-y-2.5">
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-rose-950/80 border border-rose-800/60 flex items-center justify-center text-rose-400">
                <i data-lucide="lock" class="w-4 h-4"></i>
            </div>
            <h4 class="text-xs sm:text-sm font-extrabold text-white">Physical Vault Verification</h4>
            <p class="text-[10px] sm:text-xs text-slate-400 leading-relaxed">
                Status only flips to "Ready" after the original paper document and metal plate are physically filed inside our branch drawer safe.
            </p>
        </div>

        <div class="p-4 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-1.5 sm:space-y-2.5">
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-emerald-950/80 border border-emerald-800/60 flex items-center justify-center text-emerald-400">
                <i data-lucide="message-square" class="w-4 h-4"></i>
            </div>
            <h4 class="text-xs sm:text-sm font-extrabold text-white">Automated SMS Alerts</h4>
            <p class="text-[10px] sm:text-xs text-slate-400 leading-relaxed">
                Receive proactive text messages to your phone the minute your LTO documents or metal plate are logged into the system.
            </p>
        </div>

        <div class="p-4 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-1.5 sm:space-y-2.5">
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-blue-950/80 border border-blue-800/60 flex items-center justify-center text-blue-400">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
            </div>
            <h4 class="text-xs sm:text-sm font-extrabold text-white">RA 10173 Privacy Protected</h4>
            <p class="text-[10px] sm:text-xs text-slate-400 leading-relaxed">
                Engine and chassis numbers are cryptographically masked to protect your vehicle from identity cloning and fraud.
            </p>
        </div>

        <div class="p-4 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-1.5 sm:space-y-2.5">
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-amber-950/80 border border-amber-800/60 flex items-center justify-center text-amber-400">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
            </div>
            <h4 class="text-xs sm:text-sm font-extrabold text-white">San Jose Antique Branch</h4>
            <p class="text-[10px] sm:text-xs text-slate-400 leading-relaxed">
                Trade Town, San Jose de Buenavista, Antique. Open Monday to Saturday from 8:00 AM to 5:00 PM for document releasing.
            </p>
        </div>

    </div>
</div>


<!-- =====================================================================
     SECTION 5: BOTTOM CALL TO ACTION
     ===================================================================== -->
<div class="p-6 sm:p-8 lg:p-12 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-rose-950/60 via-slate-900 to-slate-950 border border-rose-800/40 shadow-2xl text-center space-y-4 sm:space-y-5 relative overflow-hidden">
    <div class="relative z-10 max-w-xl mx-auto space-y-3 sm:space-y-4">
        <img src="{{ asset('images/logo-icon.svg') }}" alt="Yamaha Emblem" class="w-10 h-10 sm:w-14 sm:h-14 mx-auto rounded-xl sm:rounded-2xl shadow-xl">
        <h2 class="text-lg sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
            Ready to Check Your Motorcycle Documents?
        </h2>
        <p class="text-[11px] sm:text-xs md:text-sm text-slate-400 leading-relaxed">
            Enter your tracking reference and 4-digit mobile PIN to view real-time document availability.
        </p>
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-2.5 sm:gap-3">
            <a href="{{ route('customer.track') }}"
               class="w-full sm:w-auto px-6 sm:px-8 py-2.5 sm:py-3.5 rounded-lg sm:rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white text-xs font-black uppercase tracking-wider shadow-xl shadow-rose-950/60 transition flex items-center justify-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Open Tracking Portal</span>
            </a>
            <a href="tel:09171234567"
               class="w-full sm:w-auto px-5 sm:px-6 py-2.5 sm:py-3.5 rounded-lg sm:rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700 text-xs font-bold transition flex items-center justify-center gap-2">
                <i data-lucide="phone" class="w-4 h-4 text-rose-400"></i>
                <span>Branch Contact Desk</span>
            </a>
        </div>
    </div>
</div>

@endsection
