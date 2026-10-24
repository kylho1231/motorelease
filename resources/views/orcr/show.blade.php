@extends('layouts.app')

@section('title', 'Manage OR/CR - ' . $orcrRecord->motorcycle->tracking_reference)
@section('page-title', 'OR/CR Verification & Lifecycle Management')

@section('content')
<div class="max-w-4xl space-y-6">

    <!-- Vehicle & Owner Summary Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h2 class="text-lg font-bold text-slate-900">{{ $orcrRecord->motorcycle->model }}</h2>
                <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded bg-red-50 text-red-700 border border-red-200">
                    {{ $orcrRecord->motorcycle->tracking_reference }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Owner: <strong>{{ $orcrRecord->motorcycle->customer?->full_name }}</strong> &bull;
                Mobile: <span class="font-mono">{{ $orcrRecord->motorcycle->customer?->mobile_number }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border whitespace-nowrap {{ $orcrRecord->status_badge_class }}">
                Current Status: {{ $orcrRecord->status_label }}
            </span>
            <a href="{{ route('motorcycles.show', $orcrRecord->motorcycle) }}" class="text-xs text-slate-500 hover:text-slate-900 font-semibold">
                View Motorcycle &rarr;
            </a>
        </div>
    </div>

    <!-- Physical Receipt Update Form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="border-b border-slate-100 pb-3 mb-5 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Physical Document Confirmation</h3>
                <p class="text-xs text-slate-500">Record physical delivery of Official Receipt (OR) and Certificate of Registration (CR)</p>
            </div>
        </div>

        <form action="{{ route('orcr.update_receipt', $orcrRecord) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- OR Section -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_or_received" value="1" {{ old('is_or_received', $orcrRecord->is_or_received) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wide">
                            Official Receipt (OR) Physically Received
                        </span>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="or_number" class="block text-[11px] font-semibold text-slate-600 mb-1">OR Number</label>
                        <input type="text" id="or_number" name="or_number" value="{{ old('or_number', $orcrRecord->or_number) }}"
                               placeholder="e.g. OR-2026-99214"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono">
                    </div>
                    <div>
                        <label for="or_received_date" class="block text-[11px] font-semibold text-slate-600 mb-1">Date Received at Dealership</label>
                        <input type="date" id="or_received_date" name="or_received_date"
                               value="{{ old('or_received_date', $orcrRecord->or_received_date?->format('Y-m-d') ?: date('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                </div>
            </div>

            <!-- CR Section -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_cr_received" value="1" {{ old('is_cr_received', $orcrRecord->is_cr_received) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wide">
                            Certificate of Registration (CR) Physically Received
                        </span>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="cr_number" class="block text-[11px] font-semibold text-slate-600 mb-1">CR Number</label>
                        <input type="text" id="cr_number" name="cr_number" value="{{ old('cr_number', $orcrRecord->cr_number) }}"
                               placeholder="e.g. CR-2026-44910"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono">
                    </div>
                    <div>
                        <label for="cr_received_date" class="block text-[11px] font-semibold text-slate-600 mb-1">Date Received at Dealership</label>
                        <input type="date" id="cr_received_date" name="cr_received_date"
                               value="{{ old('cr_received_date', $orcrRecord->cr_received_date?->format('Y-m-d') ?: date('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                </div>
            </div>

            <!-- Storage & Remarks -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="storage_location_ref" class="block text-xs font-semibold text-slate-700 mb-1">Filing Location / Storage Cabinet</label>
                    <input type="text" id="storage_location_ref" name="storage_location_ref" value="{{ old('storage_location_ref', $orcrRecord->storage_location_ref) }}"
                           placeholder="e.g. Cabinet 1, Shelf B (Envelope #34)"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                </div>
                <div>
                    <label for="remarks" class="block text-xs font-semibold text-slate-700 mb-1">Staff Remarks</label>
                    <input type="text" id="remarks" name="remarks" value="{{ old('remarks', $orcrRecord->remarks) }}"
                           placeholder="e.g. Clean dry seal verified"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition">
                    Save Physical Receipt Records
                </button>
            </div>
        </form>
    </div>

    <!-- Verification & Readiness Quality Gate -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h3 class="font-bold text-slate-900 text-sm mb-2 flex items-center gap-2">
            <i data-lucide="shield-alert" class="w-4 h-4 text-emerald-600"></i>
            Physical Verification Quality Gate
        </h3>
        <p class="text-xs text-slate-500 mb-4">
            Critical Rule: Never mark the complete OR/CR package as ready unless BOTH required documents are physically confirmed and verified.
        </p>

        @if($orcrRecord->isPhysicallyComplete())
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-3">
                <div class="flex items-center gap-2 font-bold text-emerald-800">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                    Both Official Receipt (OR) and Certificate of Registration (CR) are physically present.
                </div>
                <p>
                    Approving readiness will automatically trigger the customer availability SMS notification via the configured gateway.
                </p>

                @if($orcrRecord->status !== 'ready_for_pickup' && $orcrRecord->status !== 'claimed')
                    <form action="{{ route('orcr.verify_ready', $orcrRecord) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            Approve Verification & Queue SMS Notification
                        </button>
                    </form>
                @else
                    <span class="inline-block px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-800 font-bold text-xs">
                        &check; Verified & Ready for Pickup (Customer Notified)
                    </span>
                @endif
            </div>
        @else
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
                <div>
                    <strong class="font-bold">Verification Gate Incomplete:</strong>
                    <p class="mt-1">
                        Currently only {{ $orcrRecord->is_or_received ? 'Official Receipt (OR)' : ($orcrRecord->is_cr_received ? 'Certificate of Registration (CR)' : 'neither document') }} is physically confirmed. Both must be received before marking Ready for Pickup.
                    </p>
                </div>
            </div>
        @endif

        <!-- Put on hold form -->
        <div class="mt-6 pt-5 border-t border-slate-100" x-data="{ openHold: false }">
            <button @click="openHold = !openHold" class="text-xs font-semibold text-rose-600 hover:text-rose-700 flex items-center gap-1.5">
                <i data-lucide="pause-circle" class="w-4 h-4"></i>
                Flag Document Issue / Place On Hold
            </button>

            <div x-show="openHold" x-cloak class="mt-3 p-4 rounded-xl bg-rose-50 border border-rose-200">
                <form action="{{ route('orcr.put_on_hold', $orcrRecord) }}" method="POST" class="space-y-3">
                    @csrf
                    <label for="hold_reason" class="block text-xs font-bold text-rose-900">Reason for Placing On Hold</label>
                    <textarea id="hold_reason" name="hold_reason" required rows="2"
                              class="w-full px-3 py-2 rounded-xl border border-rose-300 text-xs text-rose-950 placeholder-rose-400"
                              placeholder="e.g. Name spelling discrepancy on CR requires endorsement to LTO..."></textarea>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openHold = false" class="px-3 py-1.5 text-xs text-slate-600">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl">Confirm Hold</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Status History Audit Trail -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm">OR/CR Lifecycle Audit Trail</h3>
        </div>

        @if($orcrRecord->statusHistories->isEmpty())
            <div class="p-6 text-center text-slate-400 text-xs">
                No state transition history logged yet.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($orcrRecord->statusHistories as $hist)
                    <div class="p-4 text-xs flex items-start justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800">{{ ucfirst(str_replace('_', ' ', $hist->to_status)) }}</span>
                                <span class="text-slate-400 text-[11px]">&larr; {{ ucfirst(str_replace('_', ' ', $hist->from_status ?? 'Created')) }}</span>
                            </div>
                            <p class="text-slate-600">{{ $hist->notes }}</p>
                            <p class="text-[11px] text-slate-400">By: {{ $hist->changedByUser?->name ?? 'System' }}</p>
                        </div>
                        <span class="text-slate-400 text-[11px] shrink-0">{{ $hist->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
