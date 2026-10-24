@extends('layouts.app')

@section('title', 'Operational Reports')
@section('page-title', 'Dealership Operational Reports & Analytics')

@section('content')
<div class="space-y-6">

    <!-- Reports Filter & Export Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900">Document Availability & Releasing Analytics</h2>
            <p class="text-xs text-slate-500">Track cycle times, unclaimed aging items, and branch release performance</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('reports.export_csv', ['type' => 'releases', 'branch_id' => $branchId]) }}"
               class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition flex items-center gap-1.5 shadow-xs">
                <i data-lucide="download" class="w-3.5 h-3.5"></i> Export Releases CSV
            </a>

            <a href="{{ route('reports.export_csv', ['type' => 'inventory', 'branch_id' => $branchId]) }}"
               class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i> Export Inventory CSV
            </a>
        </div>
    </div>

    <!-- Cycle Time Turnaround KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">

        <!-- Turnaround Receipt to SMS -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-semibold text-slate-500">Dealership Receipt to SMS</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $avgDaysReceiptToVerified }}</span>
                <span class="text-xs text-slate-500 font-semibold">avg. days</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">From physical doc arrival to verified customer SMS</p>
        </div>

        <!-- Notification to Collection -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-semibold text-slate-500">Notification to Claim</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-emerald-700">{{ $avgDaysVerifiedToClaim }}</span>
                <span class="text-xs text-slate-500 font-semibold">avg. days</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">From readiness alert to customer collection</p>
        </div>

        <!-- OR/CR Release Ratio -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-semibold text-slate-500">OR/CR Handover Rate</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-red-600">
                    {{ $totalRegistered > 0 ? round(($orcrClaimed / $totalRegistered) * 100, 1) : 0 }}%
                </span>
                <span class="text-xs text-slate-500 font-semibold">claimed</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">{{ $orcrClaimed }} of {{ $totalRegistered }} total units released</p>
        </div>

        <!-- SMS Delivery Success Rate -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-semibold text-slate-500">SMS Gateway Success</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-blue-600">
                    {{ $totalSms > 0 ? round(($deliveredSms / $totalSms) * 100, 1) : 100 }}%
                </span>
                <span class="text-xs text-slate-500 font-semibold">delivered</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">{{ $deliveredSms }} delivered &bull; {{ $failedSms }} failed</p>
        </div>

    </div>

    <!-- Overall Inventory & Status Funnel -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- OR/CR Documents Funnel -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <h3 class="font-bold text-slate-900 text-sm mb-4">OR/CR Documents Pipeline</h3>

            <div class="space-y-3 text-xs">
                <div>
                    <div class="flex justify-between text-slate-600 mb-1">
                        <span>Awaiting Documents / Processing</span>
                        <strong class="text-slate-800">{{ $orcrPending }} units</strong>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $totalRegistered > 0 ? ($orcrPending / $totalRegistered) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-slate-600 mb-1">
                        <span>Verified & Ready for Customer Claim</span>
                        <strong class="text-emerald-700">{{ $orcrReady }} units</strong>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $totalRegistered > 0 ? ($orcrReady / $totalRegistered) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-slate-600 mb-1">
                        <span>Claimed by Owner / Representative</span>
                        <strong class="text-slate-900">{{ $orcrClaimed }} units</strong>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-slate-700 h-2 rounded-full" style="width: {{ $totalRegistered > 0 ? ($orcrClaimed / $totalRegistered) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- License Plate Pipeline -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <h3 class="font-bold text-slate-900 text-sm mb-4">License Plate Pipeline</h3>

            <div class="space-y-3 text-xs">
                <div>
                    <div class="flex justify-between text-slate-600 mb-1">
                        <span>Awaiting Metal Plate from LTO</span>
                        <strong class="text-slate-800">{{ $platesPending }} units</strong>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $totalRegistered > 0 ? ($platesPending / $totalRegistered) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-slate-600 mb-1">
                        <span>Verified Plate Ready for Collection</span>
                        <strong class="text-emerald-700">{{ $platesReady }} units</strong>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $totalRegistered > 0 ? ($platesReady / $totalRegistered) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-slate-600 mb-1">
                        <span>Physical Plate Claimed</span>
                        <strong class="text-slate-900">{{ $platesClaimed }} units</strong>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-slate-700 h-2 rounded-full" style="width: {{ $totalRegistered > 0 ? ($platesClaimed / $totalRegistered) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Aging & Unclaimed Follow-up Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Unclaimed Items Aging Report (&gt; 14 Days Ready)</h3>
                <p class="text-xs text-slate-500">Items verified ready for collection where customer has not yet visited dealership</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                {{ $unclaimedAgingOrcr->count() + $unclaimedAgingPlates->count() }} Aging Unit(s)
            </span>
        </div>

        @if($unclaimedAgingOrcr->isEmpty() && $unclaimedAgingPlates->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                No items currently aging past 14 days without pickup. Great job!
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-3">Tracking Ref</th>
                            <th class="px-4 py-3">Item Type</th>
                            <th class="px-4 py-3">Customer & Contact</th>
                            <th class="px-4 py-3">Ready Since</th>
                            <th class="px-4 py-3">Days Unclaimed</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($unclaimedAgingOrcr as $rec)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $rec->motorcycle->tracking_reference }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">OR/CR Package</td>
                                <td class="px-4 py-3">
                                    {{ $rec->motorcycle->customer?->full_name }}
                                    <span class="block text-[10px] font-mono text-slate-400">{{ $rec->motorcycle->customer?->mobile_number }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $rec->verified_at?->format('M d, Y') }}</td>
                                <td class="px-4 py-3 font-bold text-amber-700">{{ $rec->verified_at ? now()->diffInDays($rec->verified_at) : 0 }} days</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('motorcycles.show', $rec->motorcycle) }}" class="text-red-600 font-bold hover:underline">View & Remind</a>
                                </td>
                            </tr>
                        @endforeach
                        @foreach($unclaimedAgingPlates as $rec)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $rec->motorcycle->tracking_reference }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">License Plate ({{ $rec->plate_number ?: 'Assigned' }})</td>
                                <td class="px-4 py-3">
                                    {{ $rec->motorcycle->customer?->full_name }}
                                    <span class="block text-[10px] font-mono text-slate-400">{{ $rec->motorcycle->customer?->mobile_number }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $rec->verified_at?->format('M d, Y') }}</td>
                                <td class="px-4 py-3 font-bold text-amber-700">{{ $rec->verified_at ? now()->diffInDays($rec->verified_at) : 0 }} days</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('motorcycles.show', $rec->motorcycle) }}" class="text-blue-600 font-bold hover:underline">View & Remind</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Branch Performance Breakdown -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm">Dealership Branch Performance Summary</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3">Branch Name</th>
                        <th class="px-4 py-3">Municipality</th>
                        <th class="px-4 py-3">Customers</th>
                        <th class="px-4 py-3">Motorcycles</th>
                        <th class="px-4 py-3">Total Releases Handed Over</th>
                        <th class="px-4 py-3 text-right">Branch Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($branchSummaries as $b)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $b->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $b->municipality }}, {{ $b->province }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $b->customers_count }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $b->motorcycles_count }}</td>
                            <td class="px-4 py-3 font-bold text-emerald-700">{{ $b->release_transactions_count }} transactions</td>
                            <td class="px-4 py-3 text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $b->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $b->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
