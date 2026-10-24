@extends('layouts.app')

@section('title', 'Release Transaction - ' . $release->transaction_code)
@section('page-title', 'Electronic Release Transaction Record')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="border-b border-slate-100 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Electronic Release Receipt</span>
                <h2 class="text-xl font-extrabold text-slate-900 font-mono mt-0.5">{{ $release->transaction_code }}</h2>
                <p class="text-xs text-slate-500">Released on {{ $release->released_at->format('F d, Y h:i A') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('releases.print_receipt', $release) }}" target="_blank"
                   class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i> Print Official Receipt
                </a>
            </div>
        </div>

        <!-- Claimant Information Box -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs mb-6">
            <div>
                <span class="text-slate-400 block text-[11px]">Claimant Full Name:</span>
                <strong class="text-slate-900 text-sm">{{ $release->claimant_name }}</strong>
                <span class="block text-slate-500 capitalize">Relationship: {{ str_replace('_', ' ', $release->claimant_type) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Contact Number:</span>
                <span class="font-mono text-slate-800 font-bold">{{ $release->claimant_contact }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Identity Verification:</span>
                <span class="font-medium text-slate-800">{{ $release->id_type_presented }}</span>
                <span class="block font-mono text-slate-500">Number: {{ $release->id_number_masked }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Released By (Employee):</span>
                <strong class="text-slate-800">{{ $release->processedByUser->name }}</strong>
                <span class="block text-slate-500">{{ $release->branch->name }}</span>
            </div>
        </div>

        <!-- Released Items Table -->
        <h3 class="font-bold text-slate-900 text-sm mb-3">Items Handed Over In This Transaction</h3>
        <div class="border border-slate-200 rounded-xl overflow-hidden mb-6">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase">
                    <tr>
                        <th class="px-4 py-3">Item Description</th>
                        <th class="px-4 py-3">Vehicle Details</th>
                        <th class="px-4 py-3">Identifiers</th>
                        <th class="px-4 py-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($release->releaseItems as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-bold text-slate-900">
                                {{ $item->item_type_label }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $item->motorcycle->model }}
                                <span class="block font-mono text-[10px] text-slate-400">{{ $item->motorcycle->tracking_reference }}</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-700">
                                {{ $item->item_identifier }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-300">
                                    Claimed
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($release->remarks)
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 mb-6">
                <strong>Staff Remarks:</strong> {{ $release->remarks }}
            </div>
        @endif

        <div class="flex items-center justify-between text-xs text-slate-500 pt-4 border-t border-slate-100">
            <a href="{{ route('releases.index') }}" class="font-semibold text-slate-700 hover:text-red-600">
                &larr; Back to Release History
            </a>
            <span>Protected against double claiming by electronic transaction lock.</span>
        </div>
    </div>

</div>
@endsection
