@extends('layouts.app')

@section('title', 'Motorcycle - ' . $motorcycle->tracking_reference)
@section('page-title', 'Motorcycle Tracking & Release Details')

@section('content')
<div class="space-y-6">

    <!-- Top Summary Banner -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <!-- QR Code Preview Thumbnail -->
            <div class="w-20 h-20 bg-white border border-slate-200 rounded-2xl p-1.5 shadow-xs shrink-0 flex items-center justify-center">
                <img src="{{ $qrDataUri }}" alt="Tracking QR Code" class="w-full h-full object-contain">
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h2 class="text-xl font-extrabold text-slate-900">{{ $motorcycle->model }}</h2>
                    <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded-lg bg-red-50 text-red-700 border border-red-200">
                        {{ $motorcycle->tracking_reference }}
                    </span>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-bold
                        {{ $motorcycle->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                        {{ ucfirst($motorcycle->status) }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Owner: <a href="{{ route('customers.show', $motorcycle->customer) }}" class="font-bold text-slate-800 hover:text-red-600 underline">
                        {{ $motorcycle->customer?->full_name }}
                    </a> &bull; Mobile: <span class="font-mono">{{ $motorcycle->customer?->mobile_number }}</span>
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Branch: {{ $motorcycle->branch->name }} &bull; Purchased: {{ $motorcycle->purchase_date->format('M d, Y') }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('motorcycles.print_slip', $motorcycle) }}" target="_blank"
               class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition flex items-center gap-1.5">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i> Print Customer QR Slip
            </a>

            @if($motorcycle->isOrcrReady() || $motorcycle->isPlateReady())
                <a href="{{ route('releases.create', ['tracking_reference' => $motorcycle->tracking_reference]) }}"
                   class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Release Items
                </a>
            @endif

            <a href="{{ route('motorcycles.edit', $motorcycle) }}" class="p-2 rounded-xl border border-slate-300 text-slate-600 hover:text-slate-900 transition" title="Edit Vehicle">
                <i data-lucide="settings" class="w-4 h-4"></i>
            </a>
        </div>
    </div>

    <!-- Independent Status Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- 1. OR/CR Lifecycle Box -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-xl bg-red-50 text-red-600"><i data-lucide="file-text" class="w-5 h-5"></i></span>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">OR/CR Document Status</h3>
                            <p class="text-[11px] text-slate-500">Official Receipt & Certificate of Registration</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded text-xs font-bold border {{ $motorcycle->orcrRecord?->status_badge_class }}">
                        {{ $motorcycle->orcrRecord?->status_label ?? 'Awaiting' }}
                    </span>
                </div>

                <div class="mt-4 space-y-3 text-xs">
                    <!-- Physical Receipt Checklist -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <p class="font-bold uppercase tracking-wider text-slate-500 text-[10px]">Physical Receipt Checklist</p>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <i data-lucide="{{ $motorcycle->orcrRecord?->is_or_received ? 'check-circle-2' : 'circle' }}"
                                   class="w-4 h-4 {{ $motorcycle->orcrRecord?->is_or_received ? 'text-emerald-600' : 'text-slate-300' }}"></i>
                                Official Receipt (OR): <strong class="font-mono">{{ $motorcycle->orcrRecord?->or_number ?: 'Not Yet Recorded' }}</strong>
                            </span>
                            <span class="text-slate-400 text-[11px]">{{ $motorcycle->orcrRecord?->or_received_date?->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <i data-lucide="{{ $motorcycle->orcrRecord?->is_cr_received ? 'check-circle-2' : 'circle' }}"
                                   class="w-4 h-4 {{ $motorcycle->orcrRecord?->is_cr_received ? 'text-emerald-600' : 'text-slate-300' }}"></i>
                                Certificate of Registration (CR): <strong class="font-mono">{{ $motorcycle->orcrRecord?->cr_number ?: 'Not Yet Recorded' }}</strong>
                            </span>
                            <span class="text-slate-400 text-[11px]">{{ $motorcycle->orcrRecord?->cr_received_date?->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-slate-600">
                        <div>
                            <span class="text-slate-400 text-[11px] block">Storage Reference:</span>
                            <span class="font-medium text-slate-800">{{ $motorcycle->orcrRecord?->storage_location_ref ?: 'Not Assigned' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[11px] block">Verified By / At:</span>
                            <span class="font-medium text-slate-800">
                                {{ $motorcycle->orcrRecord?->verifiedByUser?->name ?? 'Unverified' }}
                                @if($motorcycle->orcrRecord?->verified_at)
                                    ({{ $motorcycle->orcrRecord->verified_at->format('M d, Y') }})
                                @endif
                            </span>
                        </div>
                    </div>

                    @if($motorcycle->orcrRecord?->status === 'on_hold')
                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                            <strong>Hold Reason:</strong> {{ $motorcycle->orcrRecord->hold_reason }}
                        </div>
                    @endif

                    @if($motorcycle->orcrRecord?->status === 'claimed')
                        <div class="p-3 rounded-xl bg-slate-100 border border-slate-300 text-slate-700">
                            <strong>Claimed:</strong> {{ $motorcycle->orcrRecord->claim_date?->format('M d, Y h:i A') }}
                            by {{ $motorcycle->orcrRecord->releasedByUser?->name ?? 'Authorized Staff' }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- OR/CR Actions -->
            <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                <a href="{{ route('orcr.show', $motorcycle->orcrRecord) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                    Manage Receipt & Details &rarr;
                </a>

                @if($motorcycle->orcrRecord?->status !== 'claimed')
                    @if($motorcycle->orcrRecord?->status !== 'ready_for_pickup')
                        <form action="{{ route('orcr.verify_ready', $motorcycle->orcrRecord) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition flex items-center gap-1.5">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Verify & Notify Ready
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        <!-- 2. License Plate Lifecycle Box -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-xl bg-blue-50 text-blue-600"><i data-lucide="shield" class="w-5 h-5"></i></span>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">License Plate Status</h3>
                            <p class="text-[11px] text-slate-500">Physical Metal Plate Availability</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded text-xs font-bold border {{ $motorcycle->licensePlateRecord?->status_badge_class }}">
                        {{ $motorcycle->licensePlateRecord?->status_label ?? 'Awaiting' }}
                    </span>
                </div>

                <div class="mt-4 space-y-3 text-xs">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <p class="font-bold uppercase tracking-wider text-slate-500 text-[10px]">Physical Plate Verification</p>
                        <div class="flex items-center justify-between">
                            <span>
                                Plate Number: <strong class="font-mono text-sm {{ $motorcycle->licensePlateRecord?->plate_number ? 'text-slate-900 font-bold' : 'text-slate-400 font-normal' }}">
                                    {{ $motorcycle->licensePlateRecord?->plate_number ?: 'Not Yet Assigned' }}
                                </strong>
                            </span>
                            <span class="text-slate-400 text-[11px]">
                                {{ $motorcycle->licensePlateRecord?->received_date ? 'Received: ' . $motorcycle->licensePlateRecord->received_date->format('M d, Y') : 'In transit' }}
                            </span>
                        </div>
                        <div class="text-slate-500">
                            MV File No: <span class="font-mono text-slate-700">{{ $motorcycle->licensePlateRecord?->mv_file_number ?: 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-slate-600">
                        <div>
                            <span class="text-slate-400 text-[11px] block">Storage Bin Ref:</span>
                            <span class="font-medium text-slate-800">{{ $motorcycle->licensePlateRecord?->storage_bin_ref ?: 'Not Assigned' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[11px] block">Verified By:</span>
                            <span class="font-medium text-slate-800">
                                {{ $motorcycle->licensePlateRecord?->verifiedByUser?->name ?? 'Unverified' }}
                            </span>
                        </div>
                    </div>

                    @if($motorcycle->licensePlateRecord?->status === 'on_hold')
                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                            <strong>Hold Reason:</strong> {{ $motorcycle->licensePlateRecord->hold_reason }}
                        </div>
                    @endif

                    @if($motorcycle->licensePlateRecord?->status === 'claimed')
                        <div class="p-3 rounded-xl bg-slate-100 border border-slate-300 text-slate-700">
                            <strong>Claimed:</strong> {{ $motorcycle->licensePlateRecord->claim_date?->format('M d, Y h:i A') }}
                            by {{ $motorcycle->licensePlateRecord->releasedByUser?->name ?? 'Authorized Staff' }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- License Plate Actions -->
            <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                <a href="{{ route('plates.show', $motorcycle->licensePlateRecord) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                    Manage Receipt & Details &rarr;
                </a>

                @if($motorcycle->licensePlateRecord?->status !== 'claimed')
                    @if($motorcycle->licensePlateRecord?->status !== 'ready_for_pickup')
                        <form action="{{ route('plates.verify_ready', $motorcycle->licensePlateRecord) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition flex items-center gap-1.5">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Verify & Notify Ready
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

    </div>

    <!-- Vehicle Technical Identifiers -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h3 class="font-bold text-slate-900 text-sm mb-4">Vehicle Technical Details</h3>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block">Manufacturer / Brand</span>
                <span class="font-bold text-slate-800">{{ $motorcycle->manufacturer }}</span>
            </div>
            <div>
                <span class="text-slate-400 block">Model & Variant</span>
                <span class="font-bold text-slate-800">{{ $motorcycle->model }} ({{ $motorcycle->variant ?: 'Standard' }})</span>
            </div>
            <div>
                <span class="text-slate-400 block">Color & Year</span>
                <span class="font-bold text-slate-800">{{ $motorcycle->color }} ({{ $motorcycle->year_model }})</span>
            </div>
            <div>
                <span class="text-slate-400 block">Sales Invoice Ref</span>
                <span class="font-mono font-bold text-slate-800">{{ $motorcycle->dealer_transaction_ref ?: 'N/A' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block">Engine Number</span>
                <span class="font-mono font-bold text-slate-900">{{ $motorcycle->engine_number }}</span>
            </div>
            <div>
                <span class="text-slate-400 block">Chassis / VIN Number</span>
                <span class="font-mono font-bold text-slate-900">{{ $motorcycle->chassis_number }}</span>
            </div>
            <div>
                <span class="text-slate-400 block">Customer Security PIN</span>
                <span class="font-mono font-bold text-slate-600">Last 4 digits of phone</span>
            </div>
            <div>
                <span class="text-slate-400 block">Registration Status</span>
                <span class="font-bold text-slate-800 uppercase">{{ $motorcycle->status }}</span>
            </div>
        </div>
    </div>

    <!-- Notification Activity Log for this Vehicle -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">SMS Notification Activity</h3>
            <span class="text-xs text-slate-500">{{ $motorcycle->notifications->count() }} message(s)</span>
        </div>

        @if($motorcycle->notifications->isEmpty())
            <div class="p-6 text-center text-slate-400 text-xs">
                No SMS availability alerts triggered yet for this vehicle.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($motorcycle->notifications as $notif)
                    <div class="p-4 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 transition">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800">{{ $notif->event_label }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $notif->status_badge_class }}">
                                    {{ ucfirst($notif->status) }}
                                </span>
                                <span class="text-slate-400 text-[11px]">{{ $notif->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                            <p class="text-slate-600 italic">"{{ $notif->message_body }}"</p>
                        </div>

                        <div class="shrink-0 flex items-center gap-2">
                            <form action="{{ route('notifications.resend', $notif) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                    Resend SMS
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
