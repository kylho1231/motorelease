@extends('layouts.app')

@section('title', 'License Plate Management')
@section('page-title', 'License Plate Tracking & Availability')

@section('content')
<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('plates.index') }}" class="w-full sm:w-auto flex-1 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[220px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search plate number, MV file, tracking ref, owner..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500">
            </div>

            <select name="status" class="px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-700">
                <option value="">All Plate Statuses</option>
                <option value="awaiting_physical_plate" {{ $status == 'awaiting_physical_plate' ? 'selected' : '' }}>Awaiting Plate</option>
                <option value="received_for_verification" {{ $status == 'received_for_verification' ? 'selected' : '' }}>Received for Verification</option>
                <option value="ready_for_pickup" {{ $status == 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                <option value="claimed" {{ $status == 'claimed' ? 'selected' : '' }}>Claimed</option>
                <option value="on_hold" {{ $status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
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
            @if($search || $status || $branchId)
                <a href="{{ route('plates.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>
    </div>

    <!-- License Plates Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">Tracking Ref</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Plate Number</th>
                        <th class="px-4 py-3.5">Motorcycle & Owner</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Physical Plate</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Status</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Storage Bin</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $rec)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <a href="{{ route('plates.show', $rec) }}" class="font-mono font-bold text-blue-600 hover:underline">
                                    {{ $rec->motorcycle->tracking_reference }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($rec->plate_number)
                                    <span class="font-mono font-black text-sm px-2.5 py-1 rounded bg-slate-100 text-slate-900 border border-slate-300">
                                        {{ $rec->plate_number }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Not Assigned</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-slate-800">{{ $rec->motorcycle->model }}</span>
                                <span class="block text-[11px] text-slate-500">{{ $rec->motorcycle->customer?->full_name }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="{{ $rec->is_plate_received ? 'check-circle' : 'clock' }}"
                                       class="w-3.5 h-3.5 {{ $rec->is_plate_received ? 'text-emerald-600' : 'text-slate-300' }}"></i>
                                    <span>{{ $rec->is_plate_received ? 'Received at Branch' : 'In Transit' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $rec->status_badge_class }}">
                                    {{ $rec->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">
                                {{ $rec->storage_bin_ref ?: 'Unassigned' }}
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-2">
                                <a href="{{ route('plates.show', $rec) }}" class="text-xs font-bold text-slate-700 hover:text-blue-600">
                                    Manage
                                </a>
                                @if($rec->status === 'ready_for_pickup')
                                    <a href="{{ route('releases.create', ['tracking_reference' => $rec->motorcycle->tracking_reference]) }}"
                                       class="px-2 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold">
                                        Claim
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                No license plate records found matching your query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $records->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
