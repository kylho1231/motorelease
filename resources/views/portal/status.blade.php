@extends('layouts.portal')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Top Greeting & Unit Banner -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Welcome back</span>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                    Hello, {{ $motorcycle->customer?->first_name }} {{ substr($motorcycle->customer?->last_name ?? '', 0, 1) }}.
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Notification Contact: <span class="font-mono text-slate-700">{{ $motorcycle->customer?->masked_mobile }}</span>
                </p>
            </div>

            <div class="text-left sm:text-right">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tracking Reference</span>
                <span class="font-mono text-base font-extrabold text-red-600">{{ $motorcycle->tracking_reference }}</span>
            </div>
        </div>

        <!-- Masked Motorcycle Specifications -->
        <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[11px]">Motorcycle Model</span>
                <strong class="text-slate-900 text-sm font-bold block">{{ $motorcycle->model }}</strong>
                <span class="text-slate-500">{{ $motorcycle->color }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Year Model</span>
                <strong class="text-slate-900 text-sm font-bold block">{{ $motorcycle->year_model }}</strong>
                <span class="text-slate-500">{{ $motorcycle->manufacturer }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Masked Engine No</span>
                <span class="font-mono text-slate-800 font-bold block mt-1">{{ $motorcycle->masked_engine_number }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Masked Chassis No</span>
                <span class="font-mono text-slate-800 font-bold block mt-1">{{ $motorcycle->masked_chassis_number }}</span>
            </div>
        </div>
    </div>

    <!-- Independent Status Cards -->
    <div class="space-y-4">

        <!-- 1. OR/CR Document Status Card -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-lg">
            <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-2xl bg-red-50 text-red-600">
                        <i data-lucide="file-text" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">OR/CR Document Package</h2>
                        <p class="text-xs text-slate-500">Official Receipt & Certificate of Registration</p>
                    </div>
                </div>

                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $motorcycle->orcrRecord?->status_badge_class }}">
                    {{ $motorcycle->orcrRecord?->status_label ?? 'Awaiting Documents' }}
                </span>
            </div>

            <!-- Detailed Document Status Indicator -->
            <div class="mt-5 space-y-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Physical Availability at Dealership</span>

                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2">
                            <i data-lucide="{{ $motorcycle->orcrRecord?->is_or_received ? 'check-circle-2' : 'clock' }}"
                               class="w-4 h-4 {{ $motorcycle->orcrRecord?->is_or_received ? 'text-emerald-600' : 'text-slate-300' }}"></i>
                            Official Receipt (OR)
                        </span>
                        <strong class="{{ $motorcycle->orcrRecord?->is_or_received ? 'text-emerald-700' : 'text-slate-400 font-normal' }}">
                            {{ $motorcycle->orcrRecord?->is_or_received ? 'Physically Verified at Branch' : 'Processing with LTO' }}
                        </strong>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2">
                            <i data-lucide="{{ $motorcycle->orcrRecord?->is_cr_received ? 'check-circle-2' : 'clock' }}"
                               class="w-4 h-4 {{ $motorcycle->orcrRecord?->is_cr_received ? 'text-emerald-600' : 'text-slate-300' }}"></i>
                            Certificate of Registration (CR)
                        </span>
                        <strong class="{{ $motorcycle->orcrRecord?->is_cr_received ? 'text-emerald-700' : 'text-slate-400 font-normal' }}">
                            {{ $motorcycle->orcrRecord?->is_cr_received ? 'Physically Verified at Branch' : 'Processing with LTO' }}
                        </strong>
                    </div>
                </div>

                @if($motorcycle->isOrcrReady())
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-start gap-3">
                        <i data-lucide="party-popper" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="font-bold text-sm block">Your OR/CR is Ready for Collection!</strong>
                            <p class="mt-0.5 text-emerald-800 leading-relaxed">
                                Both original documents have been verified and placed in secure storage. You may visit the dealership branch during operating hours to collect.
                            </p>
                        </div>
                    </div>
                @elseif($motorcycle->isOrcrClaimed())
                    <div class="p-4 rounded-2xl bg-slate-100 border border-slate-300 text-slate-700 text-xs flex items-center gap-3">
                        <i data-lucide="check-check" class="w-5 h-5 text-slate-600 shrink-0"></i>
                        <span>This OR/CR package was claimed from the dealership on {{ $motorcycle->orcrRecord->claim_date?->format('F d, Y') }}.</span>
                    </div>
                @elseif($motorcycle->orcrRecord?->status === 'on_hold')
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
                        <div>
                            <strong>Document Temporarily On Hold:</strong>
                            <p class="mt-0.5">Your documents are undergoing clerical verification with the LTO. Our staff will update you once cleared.</p>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-500">
                        Estimated availability varies by LTO batch processing. You will receive an automatic SMS once both documents arrive and are verified.
                    </p>
                @endif
            </div>
        </div>

        <!-- 2. License Plate Status Card -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-lg">
            <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-2xl bg-blue-50 text-blue-600">
                        <i data-lucide="shield" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Physical License Plate</h2>
                        <p class="text-xs text-slate-500">Metal Motorcycle License Plate</p>
                    </div>
                </div>

                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $motorcycle->licensePlateRecord?->status_badge_class }}">
                    {{ $motorcycle->licensePlateRecord?->status_label ?? 'Awaiting Plate' }}
                </span>
            </div>

            <div class="mt-5 space-y-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Plate Number:</span>
                        <strong class="font-mono text-sm {{ $motorcycle->licensePlateRecord?->plate_number ? 'text-slate-900 font-extrabold' : 'text-slate-400 font-normal italic' }}">
                            {{ $motorcycle->licensePlateRecord?->plate_number ?: 'Awaiting assignment from LTO' }}
                        </strong>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Physical Plate at Dealership:</span>
                        <strong class="{{ $motorcycle->licensePlateRecord?->is_plate_received ? 'text-emerald-700' : 'text-slate-400 font-normal' }}">
                            {{ $motorcycle->licensePlateRecord?->is_plate_received ? 'Confirmed in Dealership Bin' : 'In production / transit' }}
                        </strong>
                    </div>
                </div>

                @if($motorcycle->isPlateReady())
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-start gap-3">
                        <i data-lucide="party-popper" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="font-bold text-sm block">Your Physical Plate is Ready for Pickup!</strong>
                            <p class="mt-0.5 text-emerald-800 leading-relaxed">
                                The metal license plate is in stock at our branch. Please present your valid ID when collecting.
                            </p>
                        </div>
                    </div>
                @elseif($motorcycle->isPlateClaimed())
                    <div class="p-4 rounded-2xl bg-slate-100 border border-slate-300 text-slate-700 text-xs flex items-center gap-3">
                        <i data-lucide="check-check" class="w-5 h-5 text-slate-600 shrink-0"></i>
                        <span>This license plate was claimed from the dealership on {{ $motorcycle->licensePlateRecord->claim_date?->format('F d, Y') }}.</span>
                    </div>
                @else
                    <p class="text-xs text-slate-500">
                        License plates are produced and distributed in batches by the LTO plate-making plant. You will receive an SMS as soon as your plate arrives at the branch.
                    </p>
                @endif
            </div>
        </div>

    </div>

    <!-- Branch Pickup Guidelines & Location -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-lg space-y-4">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <i data-lucide="map-pin" class="w-4 h-4 text-red-600"></i>
            Collection Branch & Pickup Requirements
        </h3>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
            <div>
                <strong class="text-slate-900 font-bold text-sm">{{ $motorcycle->branch->name }}</strong>
                <p class="text-slate-600 mt-0.5">{{ $motorcycle->branch->full_address }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-slate-500 pt-2 border-t border-slate-200">
                <span>Operating Hours: <strong>{{ $motorcycle->branch->operating_hours }}</strong></span>
                <span>Hotline: <strong>{{ $motorcycle->branch->contact_number }}</strong></span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-red-50/60 border border-red-100 text-xs text-slate-700 space-y-2">
            <strong class="text-red-900 font-bold block uppercase tracking-wider text-[11px]">Requirements to Bring When Claiming:</strong>
            <ul class="list-disc list-inside space-y-1">
                <li>Your printed tracking slip or tracking reference number (<strong>{{ $motorcycle->tracking_reference }}</strong>).</li>
                <li>Two (2) original valid government-issued IDs of the registered owner.</li>
                <li>Official delivery receipt or dealership sales invoice.</li>
                <li>If representative: Signed authorization letter and owner's ID photocopy.</li>
            </ul>
        </div>
    </div>

</div>
@endsection
