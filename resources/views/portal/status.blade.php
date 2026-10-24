@extends('layouts.portal')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 sm:space-y-8">

    <!-- Top Navigation Breadcrumb -->
    <div class="flex items-center justify-between text-xs">
        <a href="{{ route('customer.track') }}" class="inline-flex items-center gap-1.5 text-slate-400 hover:text-white transition font-medium text-[11px] sm:text-xs">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
            <span>Check Another Unit</span>
        </a>

        <div class="flex items-center gap-1.5 sm:gap-2">
            <span class="text-slate-500 font-mono text-[10px] sm:text-[11px]">REF:</span>
            <span class="font-mono text-[11px] sm:text-xs font-bold text-rose-400 bg-rose-950/70 border border-rose-800/60 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md sm:rounded-lg">
                {{ $motorcycle->tracking_reference }}
            </span>
        </div>
    </div>

    <!-- GetLayers Telemetry Deck: Vehicle & Customer Identity -->
    <div data-spotlight class="getlayers-glass rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 relative overflow-hidden group">
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-rose-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 sm:gap-5 pb-4 sm:pb-6 border-b border-slate-800/80 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-wider sm:tracking-widest text-rose-400 bg-rose-950/80 border border-rose-800/50 px-2 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full">
                        Registered Buyer
                    </span>
                    <span class="text-[11px] sm:text-xs text-slate-400 font-mono">{{ $motorcycle->customer?->masked_mobile }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white mt-1 sm:mt-1.5 tracking-tight">
                    {{ $motorcycle->customer?->first_name }} {{ substr($motorcycle->customer?->last_name ?? '', 0, 1) }}.
                </h1>
                <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">
                    Assigned Dealership: <span class="text-slate-200 font-bold">{{ $motorcycle->branch->name }}</span>
                </p>
            </div>

            <!-- Vehicle Model Hero Badge -->
            <div class="text-left sm:text-right bg-slate-950/60 border border-slate-800 p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl sm:min-w-44">
                <span class="block text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-slate-500">Motorcycle Unit</span>
                <span class="text-sm sm:text-base font-black text-white block">{{ $motorcycle->model }}</span>
                <span class="text-[11px] sm:text-xs text-rose-400 font-medium">{{ $motorcycle->color }} &bull; {{ $motorcycle->year_model }}</span>
            </div>
        </div>

        <!-- Masked Identifiers Grid (RA 10173 Compliant) -->
        <div class="mt-4 sm:mt-6 grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-4 text-xs relative z-10">
            <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800/80">
                <span class="text-slate-500 block text-[9px] sm:text-[10px] uppercase font-bold tracking-wider">Manufacturer</span>
                <strong class="text-white text-xs sm:text-sm font-bold block mt-0.5 truncate">{{ $motorcycle->manufacturer }}</strong>
            </div>

            <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800/80">
                <span class="text-slate-500 block text-[9px] sm:text-[10px] uppercase font-bold tracking-wider">Purchase Date</span>
                <strong class="text-white text-xs sm:text-sm font-bold block mt-0.5 truncate">{{ $motorcycle->purchase_date->format('M d, Y') }}</strong>
            </div>

            <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800/80">
                <span class="text-slate-500 block text-[9px] sm:text-[10px] uppercase font-bold tracking-wider">Engine No</span>
                <span class="font-mono text-slate-200 text-[11px] sm:text-xs font-bold block mt-0.5 truncate">{{ $motorcycle->masked_engine_number }}</span>
            </div>

            <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800/80">
                <span class="text-slate-500 block text-[9px] sm:text-[10px] uppercase font-bold tracking-wider">Chassis No</span>
                <span class="font-mono text-slate-200 text-[11px] sm:text-xs font-bold block mt-0.5 truncate">{{ $motorcycle->masked_chassis_number }}</span>
            </div>
        </div>
    </div>

    <!-- Dual Readiness Gauges: Independent OR/CR & Plate Status Deck -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

        <!-- 1. OR/CR Document Package Card -->
        <div data-spotlight class="getlayers-glass rounded-2xl sm:rounded-3xl p-4 sm:p-6 relative overflow-hidden flex flex-col justify-between group">
            <div class="space-y-3 sm:space-y-4">
                <div class="flex items-start justify-between gap-3 pb-3 sm:pb-4 border-b border-slate-800/80">
                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-rose-950/80 border border-rose-800/60 text-rose-400 flex items-center justify-center shadow-lg shadow-rose-950/50 shrink-0">
                            <i data-lucide="file-text" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-white">OR/CR Package</h2>
                            <p class="text-[11px] sm:text-xs text-slate-400">Official Receipt &amp; Reg. Certificate</p>
                        </div>
                    </div>

                    <span class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold border whitespace-nowrap {{ $motorcycle->orcrRecord?->status_badge_class }}">
                        {{ $motorcycle->orcrRecord?->status_label ?? 'Awaiting Documents' }}
                    </span>
                </div>

                <!-- Physical Verification Gate Checkpoints -->
                <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-950/70 border border-slate-800/80 space-y-2 sm:space-y-3">
                    <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-500 block">
                        Physical Vault Verification
                    </span>

                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2 text-slate-300">
                            <i data-lucide="{{ $motorcycle->orcrRecord?->is_or_received ? 'check-circle-2' : 'clock' }}"
                               class="w-4 h-4 {{ $motorcycle->orcrRecord?->is_or_received ? 'text-emerald-400' : 'text-slate-600' }}"></i>
                            Official Receipt (OR)
                        </span>
                        <strong class="{{ $motorcycle->orcrRecord?->is_or_received ? 'text-emerald-400' : 'text-slate-500 font-normal' }}">
                            {{ $motorcycle->orcrRecord?->is_or_received ? 'Verified in Branch Vault' : 'In Transit / LTO' }}
                        </strong>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2 text-slate-300">
                            <i data-lucide="{{ $motorcycle->orcrRecord?->is_cr_received ? 'check-circle-2' : 'clock' }}"
                               class="w-4 h-4 {{ $motorcycle->orcrRecord?->is_cr_received ? 'text-emerald-400' : 'text-slate-600' }}"></i>
                            Certificate of Reg. (CR)
                        </span>
                        <strong class="{{ $motorcycle->orcrRecord?->is_cr_received ? 'text-emerald-400' : 'text-slate-500 font-normal' }}">
                            {{ $motorcycle->orcrRecord?->is_cr_received ? 'Verified in Branch Vault' : 'In Transit / LTO' }}
                        </strong>
                    </div>
                </div>

                <!-- Callout Banner -->
                @if($motorcycle->isOrcrReady())
                    <div class="p-4 rounded-2xl bg-emerald-950/70 border border-emerald-800 text-emerald-200 text-xs flex items-start gap-3 shadow-lg shadow-emerald-950/40">
                        <i data-lucide="party-popper" class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="font-bold text-sm text-emerald-300 block">Ready for Collection!</strong>
                            <p class="mt-1 leading-relaxed text-[11px] text-emerald-200/90">
                                Both original documents are physically verified and ready at the counter. Please bring your 2 valid IDs.
                            </p>
                        </div>
                    </div>
                @elseif($motorcycle->isOrcrClaimed())
                    <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 text-slate-400 text-xs flex items-center gap-2.5">
                        <i data-lucide="check-check" class="w-4 h-4 text-blue-400 shrink-0"></i>
                        <span>Claimed on {{ $motorcycle->orcrRecord->claim_date?->format('M d, Y') }}.</span>
                    </div>
                @elseif($motorcycle->orcrRecord?->status === 'on_hold')
                    <div class="p-4 rounded-2xl bg-amber-950/60 border border-amber-800 text-amber-200 text-xs flex items-start gap-2.5">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-400 shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-amber-300 block">Temporarily On Hold:</strong>
                            <p class="mt-0.5 text-[11px]">{{ $motorcycle->orcrRecord->hold_reason ?? 'Clerical verification with LTO.' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Automatic SMS will be dispatched the moment both documents pass physical inspection at San Jose branch.
                    </p>
                @endif
            </div>
        </div>

        <!-- 2. Physical License Plate Card -->
        <div data-spotlight class="getlayers-glass rounded-2xl sm:rounded-3xl p-4 sm:p-6 relative overflow-hidden flex flex-col justify-between group">
            <div class="space-y-3 sm:space-y-4">
                <div class="flex items-start justify-between gap-3 pb-3 sm:pb-4 border-b border-slate-800/80">
                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-950/80 border border-blue-800/60 text-blue-400 flex items-center justify-center shadow-lg shadow-blue-950/50 shrink-0">
                            <i data-lucide="shield" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-white">License Plate</h2>
                            <p class="text-[11px] sm:text-xs text-slate-400">Metal / Temporary Plate</p>
                        </div>
                    </div>

                    <span class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold border whitespace-nowrap {{ $motorcycle->licensePlateRecord?->status_badge_class }}">
                        {{ $motorcycle->licensePlateRecord?->status_label ?? 'Awaiting Plate' }}
                    </span>
                </div>

                <!-- Plate Data Deck -->
                <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-950/70 border border-slate-800/80 space-y-2 sm:space-y-2.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400">Assigned Plate:</span>
                        <strong class="font-mono text-xs sm:text-sm {{ $motorcycle->licensePlateRecord?->plate_number ? 'text-white font-extrabold bg-slate-900 px-2 py-0.5 rounded-md sm:rounded-lg border border-slate-700' : 'text-slate-500 font-normal italic' }}">
                            {{ $motorcycle->licensePlateRecord?->plate_number ?: 'Awaiting assignment' }}
                        </strong>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400">Branch Stock:</span>
                        <strong class="{{ $motorcycle->licensePlateRecord?->is_plate_received ? 'text-emerald-400' : 'text-slate-500 font-normal' }}">
                            {{ $motorcycle->licensePlateRecord?->is_plate_received ? 'In Stock in Branch Bin' : 'Pending batch dispatch' }}
                        </strong>
                    </div>
                </div>

                <!-- Plate Callout Banner -->
                @if($motorcycle->isPlateReady())
                    <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-emerald-950/70 border border-emerald-800 text-emerald-200 text-xs flex items-start gap-2.5 sm:gap-3 shadow-lg shadow-emerald-950/40">
                        <i data-lucide="party-popper" class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-400 shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="font-bold text-xs sm:text-sm text-emerald-300 block">Metal Plate Ready for Collection!</strong>
                            <p class="mt-0.5 sm:mt-1 leading-relaxed text-[10px] sm:text-[11px] text-emerald-200/90">
                                Your physical motorcycle plate is in stock. Please bring your claim stub and 2 valid IDs.
                            </p>
                        </div>
                    </div>
                @elseif($motorcycle->isPlateClaimed())
                    <div class="p-3 sm:p-3.5 rounded-xl sm:rounded-2xl bg-slate-950/80 border border-slate-800 text-slate-400 text-xs flex items-center gap-2.5">
                        <i data-lucide="check-check" class="w-4 h-4 text-blue-400 shrink-0"></i>
                        <span>Claimed on {{ $motorcycle->licensePlateRecord->claim_date?->format('M d, Y') }}.</span>
                    </div>
                @else
                    <p class="text-[10px] sm:text-[11px] text-slate-500 leading-relaxed">
                        License plates are stamped and shipped in regional LTO batches. You will receive an SMS alert upon arrival.
                    </p>
                @endif
            </div>
        </div>

    </div>

    <!-- GetLayers Roadmap Ascent: Visual Lifecycle Spine -->
    <div class="getlayers-glass rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider sm:tracking-widest text-slate-300 flex items-center gap-2">
                <i data-lucide="git-commit" class="w-4 h-4 text-rose-500"></i>
                <span>Document Lifecycle Journey</span>
            </h3>
            <span class="text-[9px] sm:text-[10px] text-slate-500 font-mono">AUTOMATED</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5 sm:gap-4 relative">
            <!-- Step 1 -->
            <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800 text-xs space-y-1 relative">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-emerald-950 border border-emerald-500 text-emerald-400 font-bold flex items-center justify-center text-[10px]">
                        ✓
                    </span>
                    <strong class="text-slate-200 text-xs">1. Purchase</strong>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500">Motorcycle registered &amp; sales invoice transmitted.</p>
            </div>

            <!-- Step 2 -->
            <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800 text-xs space-y-1 relative">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full {{ ($motorcycle->orcrRecord?->is_or_received || $motorcycle->orcrRecord?->is_cr_received) ? 'bg-emerald-950 border-emerald-500 text-emerald-400' : 'bg-slate-900 border-slate-700 text-slate-500' }} border font-bold flex items-center justify-center text-[10px]">
                        {{ ($motorcycle->orcrRecord?->is_or_received || $motorcycle->orcrRecord?->is_cr_received) ? '✓' : '2' }}
                    </span>
                    <strong class="text-slate-200 text-xs">2. Branch Safe</strong>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500">Documents delivered to dealership branch safe.</p>
            </div>

            <!-- Step 3 -->
            <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800 text-xs space-y-1 relative">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full {{ $motorcycle->isOrcrReady() ? 'bg-emerald-950 border-emerald-500 text-emerald-400' : 'bg-slate-900 border-slate-700 text-slate-500' }} border font-bold flex items-center justify-center text-[10px]">
                        {{ $motorcycle->isOrcrReady() ? '✓' : '3' }}
                    </span>
                    <strong class="text-slate-200 text-xs">3. Vault Audit</strong>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500">Physical audit of both OR &amp; CR completed.</p>
            </div>

            <!-- Step 4 -->
            <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800 text-xs space-y-1 relative">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full {{ $motorcycle->isOrcrClaimed() ? 'bg-blue-950 border-blue-500 text-blue-400' : 'bg-slate-900 border-slate-700 text-slate-500' }} border font-bold flex items-center justify-center text-[10px]">
                        {{ $motorcycle->isOrcrClaimed() ? '✓' : '4' }}
                    </span>
                    <strong class="text-slate-200 text-xs">4. Handover</strong>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500">Claim receipt issued &amp; e-signature archived.</p>
            </div>
        </div>
    </div>

    <!-- Collection Concierge & Requirements Deck -->
    <div class="getlayers-glass rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-5">
        <div class="flex items-center justify-between pb-3 sm:pb-4 border-b border-slate-800/80">
            <h3 class="text-xs sm:text-sm font-extrabold text-white flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-rose-500"></i>
                <span>Dealership Location &amp; Pickup</span>
            </h3>
            <span class="text-[10px] sm:text-xs text-rose-400 font-bold">SAN JOSE BRANCH</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
            <!-- Branch Location Info -->
            <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-950/70 border border-slate-800/80 space-y-2 text-xs">
                <strong class="text-white font-bold text-xs sm:text-sm block">{{ $motorcycle->branch->name }}</strong>
                <p class="text-slate-400 text-[11px] sm:text-xs leading-relaxed">{{ $motorcycle->branch->full_address }}</p>
                <div class="pt-2 border-t border-slate-800/60 space-y-1 text-slate-400 text-[11px]">
                    <div>Hours: <strong class="text-slate-200">{{ $motorcycle->branch->operating_hours }}</strong></div>
                    <div>Hotline: <strong class="text-rose-400 font-mono">{{ $motorcycle->branch->contact_number }}</strong></div>
                </div>
            </div>

            <!-- Mandatory Requirements -->
            <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-950/70 border border-slate-800/80 text-xs space-y-2">
                <strong class="text-rose-400 font-bold uppercase tracking-wider text-[10px] sm:text-[11px] block">
                    Checklist to Bring:
                </strong>
                <ul class="space-y-1.5 text-slate-300 text-[11px]">
                    <li class="flex items-start gap-1.5 sm:gap-2">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-rose-400 shrink-0 mt-0.5"></i>
                        <span>Tracking reference slip (<strong>{{ $motorcycle->tracking_reference }}</strong>).</span>
                    </li>
                    <li class="flex items-start gap-1.5 sm:gap-2">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-rose-400 shrink-0 mt-0.5"></i>
                        <span>Two (2) original valid government-issued IDs of the registered owner.</span>
                    </li>
                    <li class="flex items-start gap-1.5 sm:gap-2">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-rose-400 shrink-0 mt-0.5"></i>
                        <span>If representative: Signed SPA / Authorization Letter &amp; ID photocopy.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
