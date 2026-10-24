@extends('layouts.app')

@section('title', 'OR/CR Documents Management')
@section('page-title', 'OR/CR Documents Tracking')

@section('content')
<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('orcr.index') }}" class="w-full sm:w-auto flex-1 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[220px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search OR#, CR#, tracking ref, customer..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-red-500">
            </div>

            <select name="status" class="px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-700">
                <option value="">All Document Statuses</option>
                <option value="awaiting_documents" {{ $status == 'awaiting_documents' ? 'selected' : '' }}>Awaiting Documents</option>
                <option value="partially_received" {{ $status == 'partially_received' ? 'selected' : '' }}>Partially Received</option>
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
                <a href="{{ route('orcr.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>
    </div>

    <!-- OR/CR Records Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5">Tracking Ref</th>
                        <th class="px-4 py-3.5">Vehicle & Customer</th>
                        <th class="px-4 py-3.5">Official Receipt (OR)</th>
                        <th class="px-4 py-3.5">Registration (CR)</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Storage Location</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $rec)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3.5">
                                <a href="{{ route('orcr.show', $rec) }}" class="font-mono font-bold text-red-600 hover:underline">
                                    {{ $rec->motorcycle->tracking_reference }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-slate-800">{{ $rec->motorcycle->model }}</span>
                                <span class="block text-[11px] text-slate-500">{{ $rec->motorcycle->customer?->full_name }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="{{ $rec->is_or_received ? 'check-circle' : 'clock' }}"
                                       class="w-3.5 h-3.5 {{ $rec->is_or_received ? 'text-emerald-600' : 'text-slate-300' }}"></i>
                                    <span class="font-mono font-bold {{ $rec->is_or_received ? 'text-slate-800' : 'text-slate-400' }}">
                                        {{ $rec->or_number ?: ($rec->is_or_received ? 'Received' : 'Pending') }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="{{ $rec->is_cr_received ? 'check-circle' : 'clock' }}"
                                       class="w-3.5 h-3.5 {{ $rec->is_cr_received ? 'text-emerald-600' : 'text-slate-300' }}"></i>
                                    <span class="font-mono font-bold {{ $rec->is_cr_received ? 'text-slate-800' : 'text-slate-400' }}">
                                        {{ $rec->cr_number ?: ($rec->is_cr_received ? 'Received' : 'Pending') }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold border {{ $rec->status_badge_class }}">
                                    {{ $rec->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">
                                {{ $rec->storage_location_ref ?: 'Unassigned' }}
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-2">
                                <a href="{{ route('orcr.show', $rec) }}" class="text-xs font-bold text-slate-700 hover:text-red-600">
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
                                No OR/CR records found matching your filters.
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
