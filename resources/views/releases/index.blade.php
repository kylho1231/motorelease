@extends('layouts.app')

@section('title', 'Release Records')
@section('page-title', 'Document & Plate Release History')

@section('content')
<div class="space-y-6">

    <!-- Top Toolbar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('releases.index') }}" class="w-full sm:w-auto flex-1 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[220px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search transaction code, claimant name, tracking ref..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>

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
            @if($search || $branchId)
                <a href="{{ route('releases.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>

        <a href="{{ route('releases.create') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm transition">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            Process New Claim
        </a>
    </div>

    <!-- Releases Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5">Transaction Code</th>
                        <th class="px-4 py-3.5">Claimant & Contact</th>
                        <th class="px-4 py-3.5">ID Verification</th>
                        <th class="px-4 py-3.5">Claimed Items</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Released At</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Staff In-Charge</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3.5 font-mono font-bold text-slate-900 whitespace-nowrap">
                                <a href="{{ route('releases.show', $tx) }}" class="hover:text-emerald-600">
                                    {{ $tx->transaction_code }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5">
                                <strong class="text-slate-800">{{ $tx->claimant_name }}</strong>
                                <span class="block text-[10px] text-slate-400 capitalize">Role: {{ str_replace('_', ' ', $tx->claimant_type) }} &bull; {{ $tx->claimant_contact }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-600">
                                <div>{{ $tx->id_type_presented }}</div>
                                <span class="font-mono text-[10px] text-slate-400">{{ $tx->id_number_masked }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="space-y-1">
                                    @foreach($tx->releaseItems as $item)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 whitespace-nowrap">
                                            {{ $item->item_type_label }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-600">
                                {{ $tx->released_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-500">
                                {{ $tx->processedByUser->name }}
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('releases.show', $tx) }}" class="inline-flex items-center text-xs font-bold text-slate-700 hover:text-emerald-600 whitespace-nowrap">View</a>
                                <a href="{{ route('releases.print_receipt', $tx) }}" target="_blank" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 whitespace-nowrap" title="Print Receipt">
                                    <i data-lucide="printer" class="w-3.5 h-3.5 inline mr-1"></i> Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                No release records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
