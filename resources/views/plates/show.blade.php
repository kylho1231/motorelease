@extends('layouts.app')

@section('title', 'Manage Plate - ' . $plateRecord->motorcycle->tracking_reference)
@section('page-title', 'License Plate Physical Verification & Lifecycle')

@section('content')
<div class="max-w-4xl space-y-6">

    <!-- Top Banner -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h2 class="text-lg font-bold text-slate-900">{{ $plateRecord->motorcycle->model }}</h2>
                <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                    {{ $plateRecord->motorcycle->tracking_reference }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Owner: <strong>{{ $plateRecord->motorcycle->customer?->full_name }}</strong> &bull;
                Mobile: <span class="font-mono">{{ $plateRecord->motorcycle->customer?->mobile_number }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border whitespace-nowrap {{ $plateRecord->status_badge_class }}">
                Current Status: {{ $plateRecord->status_label }}
            </span>
            <a href="{{ route('motorcycles.show', $plateRecord->motorcycle) }}" class="text-xs text-slate-500 hover:text-slate-900 font-semibold">
                View Motorcycle &rarr;
            </a>
        </div>
    </div>

    <!-- Independent OR/CR Reference Status Note -->
    <div class="p-3.5 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-600 flex items-center justify-between">
        <span><strong>Independent Tracking Notice:</strong> OR/CR and license plates operate on separate lifecycles.</span>
        <span>
            Current OR/CR Status:
            <strong class="font-bold text-slate-800">{{ $plateRecord->motorcycle->orcrRecord?->status_label ?? 'Pending' }}</strong>
        </span>
    </div>

    <!-- Physical Plate Receipt Update Form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="border-b border-slate-100 pb-3 mb-5">
            <h3 class="font-bold text-slate-900 text-sm">Physical License Plate Receipt</h3>
            <p class="text-xs text-slate-500">Record receipt and storage location of the metal embossed license plate</p>
        </div>

        <form action="{{ route('plates.update_receipt', $plateRecord) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_plate_received" value="1" {{ old('is_plate_received', $plateRecord->is_plate_received) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wide">
                        Physical License Plate Received at Dealership Branch
                    </span>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="plate_number" class="block text-[11px] font-semibold text-slate-600 mb-1">
                            Assigned Plate Number
                        </label>
                        <input type="text" id="plate_number" name="plate_number" value="{{ old('plate_number', $plateRecord->plate_number) }}"
                               placeholder="e.g. 542-EAF"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono font-bold uppercase focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="mv_file_number" class="block text-[11px] font-semibold text-slate-600 mb-1">
                            LTO MV File Number
                        </label>
                        <input type="text" id="mv_file_number" name="mv_file_number" value="{{ old('mv_file_number', $plateRecord->mv_file_number) }}"
                               placeholder="e.g. 0601-0000094123"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono">
                    </div>
                    <div>
                        <label for="received_date" class="block text-[11px] font-semibold text-slate-600 mb-1">
                            Date Received
                        </label>
                        <input type="date" id="received_date" name="received_date"
                               value="{{ old('received_date', $plateRecord->received_date?->format('Y-m-d') ?: date('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="storage_bin_ref" class="block text-xs font-semibold text-slate-700 mb-1">Plate Bin / Rack Location</label>
                    <input type="text" id="storage_bin_ref" name="storage_bin_ref" value="{{ old('storage_bin_ref', $plateRecord->storage_bin_ref) }}"
                           placeholder="e.g. Plate Bin #04 - Slot 18"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                </div>
                <div>
                    <label for="remarks" class="block text-xs font-semibold text-slate-700 mb-1">Staff Remarks</label>
                    <input type="text" id="remarks" name="remarks" value="{{ old('remarks', $plateRecord->remarks) }}"
                           placeholder="e.g. Plate inspected, matches chassis"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition">
                    Save License Plate Records
                </button>
            </div>
        </form>
    </div>

    <!-- Verification Gate -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h3 class="font-bold text-slate-900 text-sm mb-2 flex items-center gap-2">
            <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
            Physical Plate Verification & Ready Notice
        </h3>
        <p class="text-xs text-slate-500 mb-4">
            Verify plate number, emboss quality, and physical presence at the branch before publishing availability.
        </p>

        @if($plateRecord->is_plate_received)
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs space-y-3">
                <div class="flex items-center gap-2 font-bold text-blue-800">
                    <i data-lucide="check-circle" class="w-4 h-4 text-blue-600"></i>
                    Physical license plate is confirmed at the dealership branch.
                </div>
                <p>
                    Approving readiness will automatically trigger the customer license plate SMS notification.
                </p>

                @if($plateRecord->status !== 'ready_for_pickup' && $plateRecord->status !== 'claimed')
                    <form action="{{ route('plates.verify_ready', $plateRecord) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            Approve Verification & Queue Plate SMS
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
                    <strong class="font-bold">Physical Plate Not Yet Received:</strong>
                    <p class="mt-1">
                        Plate must be physically delivered and marked as received in the section above before it can be verified for customer pickup.
                    </p>
                </div>
            </div>
        @endif

        <!-- Put on hold form -->
        <div class="mt-6 pt-5 border-t border-slate-100" x-data="{ openHold: false }">
            <button @click="openHold = !openHold" class="text-xs font-semibold text-rose-600 hover:text-rose-700 flex items-center gap-1.5">
                <i data-lucide="pause-circle" class="w-4 h-4"></i>
                Flag Plate Issue / Place On Hold
            </button>

            <div x-show="openHold" x-cloak class="mt-3 p-4 rounded-xl bg-rose-50 border border-rose-200">
                <form action="{{ route('plates.put_on_hold', $plateRecord) }}" method="POST" class="space-y-3">
                    @csrf
                    <label for="hold_reason" class="block text-xs font-bold text-rose-900">Reason for Plate Hold</label>
                    <textarea id="hold_reason" name="hold_reason" required rows="2"
                              class="w-full px-3 py-2 rounded-xl border border-rose-300 text-xs text-rose-950 placeholder-rose-400"
                              placeholder="e.g. Emboss defect, returned to supplier for correction..."></textarea>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openHold = false" class="px-3 py-1.5 text-xs text-slate-600">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl">Confirm Hold</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- License Plate History -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm">License Plate Lifecycle Audit Trail</h3>
        </div>

        @if($plateRecord->statusHistories->isEmpty())
            <div class="p-6 text-center text-slate-400 text-xs">
                No state transition history logged yet.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($plateRecord->statusHistories as $hist)
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
