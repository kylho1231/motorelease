@extends('layouts.app')

@section('title', 'Dealership Dashboard')
@section('page-title', 'Dealership Management Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar & Branch Filter -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Operational Overview</h2>
            <p class="text-xs text-slate-500">Live statistics computed directly from verified dealership database records</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            @if(auth()->user()->isAdmin())
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <select name="branch_id" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 rounded-xl border border-slate-300 bg-white text-slate-700 focus:ring-2 focus:ring-red-500">
                        <option value="">All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $selectedBranchId == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <a href="{{ route('releases.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Claim Item
            </a>

            <a href="{{ route('motorcycles.create') }}" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                New Motorcycle
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <!-- Registered Customers -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Total Customers</span>
                <span class="p-2 rounded-xl bg-slate-100 text-slate-700"><i data-lucide="users" class="w-4 h-4"></i></span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-900">{{ number_format($totalCustomers) }}</span>
                <span class="text-[11px] text-slate-400">Registered</span>
            </div>
            <a href="{{ route('customers.index') }}" class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-red-600 hover:text-red-700">
                View all <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>

        <!-- Registered Motorcycles -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Motorcycles</span>
                <span class="p-2 rounded-xl bg-slate-100 text-slate-700"><i data-lucide="bike" class="w-4 h-4"></i></span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-900">{{ number_format($totalMotorcycles) }}</span>
                <span class="text-[11px] text-slate-400">Total Units</span>
            </div>
            <a href="{{ route('motorcycles.index') }}" class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-red-600 hover:text-red-700">
                Manage units <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>

        <!-- Overdue Unclaimed (>14 Days) -->
        <div class="bg-white p-4 rounded-2xl border border-amber-200 bg-amber-50/20 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-amber-800">Unclaimed (&gt;14d)</span>
                <span class="p-2 rounded-xl bg-amber-100 text-amber-700"><i data-lucide="clock" class="w-4 h-4"></i></span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-amber-900">{{ number_format($totalUnclaimedOverdue) }}</span>
                <span class="text-[11px] text-amber-700">Items Aging</span>
            </div>
            <a href="{{ route('reports.index') }}" class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-amber-700 hover:text-amber-800">
                Review aging <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>

        <!-- SMS Failures -->
        <div class="bg-white p-4 rounded-2xl border {{ $failedNotifications > 0 ? 'border-rose-300 bg-rose-50/30' : 'border-slate-200' }} shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold {{ $failedNotifications > 0 ? 'text-rose-800' : 'text-slate-500' }}">SMS Issues</span>
                <span class="p-2 rounded-xl {{ $failedNotifications > 0 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700' }}">
                    <i data-lucide="alert-octagon" class="w-4 h-4"></i>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold {{ $failedNotifications > 0 ? 'text-rose-700' : 'text-slate-900' }}">{{ $failedNotifications }}</span>
                <span class="text-[11px] text-slate-400">Failed / Retry</span>
            </div>
            <a href="{{ route('notifications.index', ['status' => 'failed']) }}" class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600 hover:text-rose-700">
                Inspect SMS <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>
    </div>

    <!-- Independent Document Status Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- OR/CR Documents Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-red-50 text-red-600"><i data-lucide="file-text" class="w-5 h-5"></i></span>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">OR/CR Document Lifecycle</h3>
                        <p class="text-[11px] text-slate-500">Official Receipt & Certificate of Registration</p>
                    </div>
                </div>
                <a href="{{ route('orcr.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-700">View All</a>
            </div>

            <div class="mt-4 grid grid-cols-4 gap-2 text-center">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="block text-lg font-extrabold text-slate-700">{{ $orcrPending }}</span>
                    <span class="block text-[10px] font-semibold uppercase text-slate-400">Pending</span>
                </div>
                <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-100">
                    <span class="block text-lg font-extrabold text-emerald-700">{{ $orcrReady }}</span>
                    <span class="block text-[10px] font-semibold uppercase text-emerald-600">Ready</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="block text-lg font-extrabold text-slate-800">{{ $orcrClaimed }}</span>
                    <span class="block text-[10px] font-semibold uppercase text-slate-500">Claimed</span>
                </div>
                <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-100">
                    <span class="block text-lg font-extrabold text-rose-700">{{ $orcrOnHold }}</span>
                    <span class="block text-[10px] font-semibold uppercase text-rose-600">On Hold</span>
                </div>
            </div>

            <div class="mt-4 text-xs text-slate-500 flex items-center justify-between bg-slate-50 p-2.5 rounded-xl">
                <span>Rule: Requires BOTH OR & CR physical receipt</span>
                <a href="{{ route('orcr.index', ['status' => 'ready_for_pickup']) }}" class="font-bold text-emerald-700 hover:underline">
                    {{ $orcrReady }} ready for pickup
                </a>
            </div>
        </div>

        <!-- License Plates Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-blue-50 text-blue-600"><i data-lucide="shield" class="w-5 h-5"></i></span>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">License Plate Lifecycle</h3>
                        <p class="text-[11px] text-slate-500">Independent Physical Metal License Plates</p>
                    </div>
                </div>
                <a href="{{ route('plates.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View All</a>
            </div>

            <div class="mt-4 grid grid-cols-4 gap-2 text-center">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="block text-lg font-extrabold text-slate-700">{{ $platesPending }}</span>
                    <span class="block text-[10px] font-semibold uppercase text-slate-400">Pending</span>
                </div>
                <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-100">
                    <span class="block text-lg font-extrabold text-emerald-700">{{ $platesReady }}</span>
                    <span class="block text-[10px] font-semibold uppercase text-emerald-600">Ready</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="block text-lg font-extrabold text-slate-800">{{ $platesClaimed }}</span>
                    <span class="block text-[10px] font-semibold uppercase text-slate-500">Claimed</span>
                </div>
                <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-100">
                    <span class="block text-lg font-extrabold text-rose-700">{{ $platesOnHold }}</span>
                    <span class="block text-[10px] font-semibold uppercase text-rose-600">On Hold</span>
                </div>
            </div>

            <div class="mt-4 text-xs text-slate-500 flex items-center justify-between bg-slate-50 p-2.5 rounded-xl">
                <span>Decoupled from OR/CR document status</span>
                <a href="{{ route('plates.index', ['status' => 'ready_for_pickup']) }}" class="font-bold text-emerald-700 hover:underline">
                    {{ $platesReady }} ready for pickup
                </a>
            </div>
        </div>
    </div>

    <!-- Active Tables & Feeds -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Ready for Pickup Action Queue (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Verified Ready Items (Awaiting Customer Collection)</h3>
                    <p class="text-xs text-slate-500">Customer notified via SMS; ready for release checkout</p>
                </div>
                <a href="{{ route('releases.create') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                    Release <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            @if($itemsReadyList->isEmpty())
                <div class="p-8 text-center text-slate-400 text-sm">
                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                    No verified items currently waiting for claim.
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($itemsReadyList as $bike)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 transition">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-slate-900">{{ $bike->model }}</span>
                                    <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $bike->tracking_reference }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">
                                    Owner: <strong class="text-slate-700">{{ $bike->customer?->full_name }}</strong> &bull;
                                    Contact: <span class="font-mono text-slate-600">{{ $bike->customer?->mobile_number }}</span>
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($bike->isOrcrReady())
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                        <i data-lucide="file-check" class="w-3 h-3"></i> OR/CR
                                    </span>
                                @endif
                                @if($bike->isPlateReady())
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                        <i data-lucide="shield-check" class="w-3 h-3"></i> Plate
                                    </span>
                                @endif

                                <a href="{{ route('releases.create', ['tracking_reference' => $bike->tracking_reference]) }}"
                                   class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                                    Release
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Electronic Releases History (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Recent Releases</h3>
                <a href="{{ route('releases.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700">View All</a>
            </div>

            @if($recentReleases->isEmpty())
                <div class="p-8 text-center text-slate-400 text-sm">
                    No release transactions logged yet.
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentReleases as $rel)
                        <div class="p-3.5 hover:bg-slate-50 transition">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-mono font-bold text-slate-800">{{ $rel->transaction_code }}</span>
                                <span class="text-slate-400 text-[11px]">{{ $rel->released_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs font-semibold text-slate-700 mt-1">
                                Claimant: {{ $rel->claimant_name }} ({{ ucfirst($rel->claimant_type) }})
                            </p>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Verified by: {{ $rel->processedByUser->name }}
                            </p>
                            <a href="{{ route('releases.show', $rel) }}" class="mt-1.5 inline-block text-[11px] font-bold text-red-600 hover:underline">
                                View Receipt &rarr;
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Recent Registrations & Audit Log -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Motorcycle Registrations (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Recent Motorcycle Registrations</h3>
                <a href="{{ route('motorcycles.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700">View All</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-3">Tracking Ref</th>
                            <th class="px-4 py-3">Model</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">OR/CR</th>
                            <th class="px-4 py-3">Plate</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentMotorcycles as $bm)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                    {{ $bm->tracking_reference }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $bm->model }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $bm->customer?->full_name }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $bm->orcrRecord?->status_badge_class }}">
                                        {{ $bm->orcrRecord?->status_label ?? 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $bm->licensePlateRecord?->status_badge_class }}">
                                        {{ $bm->licensePlateRecord?->status_label ?? 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('motorcycles.show', $bm) }}" class="text-red-600 font-bold hover:underline">
                                        Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dealership Activity Feed (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <h3 class="font-bold text-slate-900 text-sm mb-4 flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-slate-400"></i>
                Audit & Activity Stream
            </h3>

            <div class="space-y-3.5">
                @foreach($recentAuditLogs as $log)
                    <div class="text-xs border-l-2 border-red-500 pl-3 py-0.5">
                        <div class="flex items-center justify-between text-slate-400 text-[10px]">
                            <span>{{ $log->user?->name ?? 'System' }}</span>
                            <span>{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="font-bold text-slate-800 text-[11px] mt-0.5">{{ $log->action }}</p>
                        <p class="text-slate-600 text-[11px] truncate">{{ $log->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection
