@extends('layouts.app')

@section('title', 'Motorcycle Units')
@section('page-title', 'Motorcycle Registration & Tracking Records')

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Toolbar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('motorcycles.index') }}" class="w-full sm:w-auto flex-1 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[220px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search tracking ref, model, engine, chassis, plate, customer..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-red-500">
            </div>

            <!-- OR/CR Status Filter -->
            <select name="orcr_status" class="px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-700">
                <option value="">All OR/CR Statuses</option>
                <option value="awaiting_documents" {{ $orcrStatus == 'awaiting_documents' ? 'selected' : '' }}>Awaiting Documents</option>
                <option value="partially_received" {{ $orcrStatus == 'partially_received' ? 'selected' : '' }}>Partially Received</option>
                <option value="ready_for_pickup" {{ $orcrStatus == 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                <option value="claimed" {{ $orcrStatus == 'claimed' ? 'selected' : '' }}>Claimed</option>
                <option value="on_hold" {{ $orcrStatus == 'on_hold' ? 'selected' : '' }}>On Hold</option>
            </select>

            <!-- Plate Status Filter -->
            <select name="plate_status" class="px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-700">
                <option value="">All Plate Statuses</option>
                <option value="awaiting_physical_plate" {{ $plateStatus == 'awaiting_physical_plate' ? 'selected' : '' }}>Awaiting Plate</option>
                <option value="received_for_verification" {{ $plateStatus == 'received_for_verification' ? 'selected' : '' }}>Received for Verification</option>
                <option value="ready_for_pickup" {{ $plateStatus == 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                <option value="claimed" {{ $plateStatus == 'claimed' ? 'selected' : '' }}>Claimed</option>
            </select>

            @if(auth()->user()->isAdmin())
                <select name="branch_id" class="px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-700">
                    <option value="">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            @endif

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition">
                Filter
            </button>
            @if($search || $branchId || $orcrStatus || $plateStatus)
                <a href="{{ route('motorcycles.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800">Reset</a>
            @endif
        </form>

        <a href="{{ route('motorcycles.create') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Register Motorcycle
        </a>
    </div>

    <!-- Motorcycles Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5">Tracking Ref</th>
                        <th class="px-4 py-3.5">Model & Color</th>
                        <th class="px-4 py-3.5">Owner</th>
                        <th class="px-4 py-3.5">Engine / Chassis</th>
                        <th class="px-4 py-3.5">OR/CR Status</th>
                        <th class="px-4 py-3.5">License Plate</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($motorcycles as $bike)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3.5">
                                <a href="{{ route('motorcycles.show', $bike) }}" class="font-mono font-bold text-red-600 hover:underline">
                                    {{ $bike->tracking_reference }}
                                </a>
                                <span class="block text-[10px] text-slate-400 mt-0.5">{{ $bike->purchase_date->format('M d, Y') }}</span>
                            </td>
                            <td class="px-4 py-3.5 font-bold text-slate-800">
                                {{ $bike->model }}
                                <span class="block text-[11px] font-normal text-slate-500">{{ $bike->color }} ({{ $bike->year_model }})</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <a href="{{ route('customers.show', $bike->customer) }}" class="font-bold text-slate-800 hover:text-red-600">
                                    {{ $bike->customer?->full_name }}
                                </a>
                                <span class="block text-[10px] font-mono text-slate-500">{{ $bike->customer?->mobile_number }}</span>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-[11px] text-slate-600">
                                <div>Eng: {{ $bike->masked_engine_number }}</div>
                                <div>Cha: {{ $bike->masked_chassis_number }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold border {{ $bike->orcrRecord?->status_badge_class }}">
                                    {{ $bike->orcrRecord?->status_label ?? 'Awaiting' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold border {{ $bike->licensePlateRecord?->status_badge_class }}">
                                    {{ $bike->licensePlateRecord?->status_label ?? 'Awaiting' }}
                                </span>
                                @if($bike->licensePlateRecord?->plate_number)
                                    <span class="block text-[10px] font-mono font-bold text-slate-700 mt-0.5">
                                        {{ $bike->licensePlateRecord->plate_number }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1.5">
                                <a href="{{ route('motorcycles.show', $bike) }}" class="text-xs font-bold text-slate-700 hover:text-red-600">View</a>
                                <a href="{{ route('motorcycles.print_slip', $bike) }}" target="_blank" class="text-xs font-semibold text-slate-500 hover:text-slate-900" title="Print Tracking Stub">
                                    <i data-lucide="printer" class="w-3.5 h-3.5 inline"></i> Slip
                                </a>
                                @if($bike->isOrcrReady() || $bike->isPlateReady())
                                    <a href="{{ route('releases.create', ['tracking_reference' => $bike->tracking_reference]) }}"
                                       class="px-2 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold">
                                        Release
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                No motorcycle records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($motorcycles->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $motorcycles->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
