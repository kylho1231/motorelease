@extends('layouts.app')

@section('title', 'Process Document Release')
@section('page-title', 'Document & License Plate Handover Checkout')

@section('content')
<div class="max-w-3xl space-y-6">

    <!-- Step 1: Scan or Search Tracking Reference -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-1">Step 1: Look Up Motorcycle Record</h2>
        <p class="text-xs text-slate-500 mb-4">
            Scan customer QR tracking slip with a barcode reader or type the tracking reference number.
        </p>

        <form method="GET" action="{{ route('releases.create') }}" class="flex items-center gap-2">
            <div class="relative flex-1">
                <i data-lucide="qr-code" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                <input type="text" name="tracking_reference" value="{{ $ref }}" required autofocus
                       placeholder="e.g. MR-SJA-2026-000003"
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold uppercase focus:ring-2 focus:ring-emerald-500">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition">
                Find Record
            </button>
        </form>
    </div>

    @if($ref && !$motorcycle)
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <span>No motorcycle transaction found matching reference "<strong>{{ $ref }}</strong>". Please double-check the tracking slip code.</span>
        </div>
    @endif

    @if($motorcycle)
        <!-- Step 2: Verification & Release Form -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="border-b border-slate-100 pb-4 mb-6">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Step 2: Physical Handover Verification</span>
                <h2 class="text-lg font-extrabold text-slate-900 mt-0.5">{{ $motorcycle->model }}</h2>
                <p class="text-xs text-slate-500">
                    Registered Owner: <strong class="text-slate-800">{{ $motorcycle->customer?->full_name }}</strong> &bull;
                    Branch: {{ $motorcycle->branch->name }}
                </p>
            </div>

            <form action="{{ route('releases.store') }}" method="POST" class="space-y-6" x-data="{ claimantType: 'owner' }">
                @csrf
                <input type="hidden" name="motorcycle_id" value="{{ $motorcycle->id }}">

                <!-- Select Items to Claim -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Select Item(s) Being Handed Over Today <span class="text-red-500">*</span>
                    </label>

                    <!-- OR/CR Card -->
                    @php
                        $orcr = $motorcycle->orcrRecord;
                        $orcrCanClaim = $orcr && $orcr->status === 'ready_for_pickup';
                        $orcrClaimed = $orcr && $orcr->status === 'claimed';
                    @endphp
                    <div class="p-4 rounded-xl border {{ $orcrCanClaim ? 'border-emerald-300 bg-emerald-50/30' : ($orcrClaimed ? 'border-slate-200 bg-slate-50' : 'border-amber-200 bg-amber-50/20') }} flex items-start justify-between gap-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="claim_items[]" value="orcr"
                                   {{ $orcrCanClaim ? 'checked' : 'disabled' }}
                                   class="mt-1 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Official Receipt & Certificate of Registration (OR/CR)</span>
                                <span class="text-[11px] text-slate-500 block">
                                    Storage: {{ $orcr?->storage_location_ref ?: 'Unassigned' }} &bull; OR#: {{ $orcr?->or_number ?: 'N/A' }} &bull; CR#: {{ $orcr?->cr_number ?: 'N/A' }}
                                </span>
                            </div>
                        </label>

                        <div>
                            @if($orcrClaimed)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">Already Claimed</span>
                            @elseif($orcrCanClaim)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Ready for Claim</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Not Ready ({{ $orcr?->status_label ?? 'Pending' }})</span>
                            @endif
                        </div>
                    </div>

                    <!-- License Plate Card -->
                    @php
                        $plate = $motorcycle->licensePlateRecord;
                        $plateCanClaim = $plate && $plate->status === 'ready_for_pickup';
                        $plateClaimed = $plate && $plate->status === 'claimed';
                    @endphp
                    <div class="p-4 rounded-xl border {{ $plateCanClaim ? 'border-blue-300 bg-blue-50/30' : ($plateClaimed ? 'border-slate-200 bg-slate-50' : 'border-amber-200 bg-amber-50/20') }} flex items-start justify-between gap-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="claim_items[]" value="license_plate"
                                   {{ $plateCanClaim ? 'checked' : 'disabled' }}
                                   class="mt-1 w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Physical Motorcycle License Plate</span>
                                <span class="text-[11px] text-slate-500 block">
                                    Bin: {{ $plate?->storage_bin_ref ?: 'Unassigned' }} &bull; Plate#: <strong class="font-mono text-slate-800">{{ $plate?->plate_number ?: 'Assigned' }}</strong>
                                </span>
                            </div>
                        </label>

                        <div>
                            @if($plateClaimed)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">Already Claimed</span>
                            @elseif($plateCanClaim)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Ready for Claim</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Not Ready ({{ $plate?->status_label ?? 'Pending' }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Claimant Verification Information -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Claimant & Identity Verification</h3>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Who is physically claiming the items today?</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="p-3 rounded-xl border cursor-pointer flex items-center gap-2.5"
                                   :class="claimantType === 'owner' ? 'border-emerald-600 bg-emerald-50/50 text-emerald-950 font-bold' : 'border-slate-200 bg-white text-slate-700'">
                                <input type="radio" name="claimant_type" value="owner" x-model="claimantType" class="text-emerald-600">
                                <span class="text-xs">Registered Owner Themselves</span>
                            </label>
                            <label class="p-3 rounded-xl border cursor-pointer flex items-center gap-2.5"
                                   :class="claimantType === 'authorized_representative' ? 'border-emerald-600 bg-emerald-50/50 text-emerald-950 font-bold' : 'border-slate-200 bg-white text-slate-700'">
                                <input type="radio" name="claimant_type" value="authorized_representative" x-model="claimantType" class="text-emerald-600">
                                <span class="text-xs">Authorized Representative</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="claimant_name" class="block text-xs font-semibold text-slate-700 mb-1">
                                Full Name of Person Claiming <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="claimant_name" name="claimant_name"
                                   value="{{ old('claimant_name', $motorcycle->customer?->full_name) }}" required
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs">
                        </div>
                        <div>
                            <label for="claimant_contact" class="block text-xs font-semibold text-slate-700 mb-1">
                                Contact Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="claimant_contact" name="claimant_contact"
                                   value="{{ old('claimant_contact', $motorcycle->customer?->mobile_number) }}" required
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="id_type_presented" class="block text-xs font-semibold text-slate-700 mb-1">
                                Valid Government ID Presented <span class="text-red-500">*</span>
                            </label>
                            <select name="id_type_presented" id="id_type_presented" required
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs">
                                <option value="Driver's License">Driver's License (LTO)</option>
                                <option value="Philippine Passport">Philippine Passport (DFA)</option>
                                <option value="Philippine National ID">Philippine National ID (PhilSys)</option>
                                <option value="UMID">Unified Multi-Purpose ID (UMID)</option>
                                <option value="PRC Professional ID">PRC Professional ID</option>
                                <option value="Postal ID">Postal ID</option>
                                <option value="Voter's Certification">Voter's ID / Certification</option>
                            </select>
                        </div>
                        <div>
                            <label for="id_number_masked" class="block text-xs font-semibold text-slate-700 mb-1">
                                ID Reference Number (Masked) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="id_number_masked" name="id_number_masked" value="{{ old('id_number_masked') }}" required
                                   placeholder="e.g. DL-****-8941"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-mono">
                            <p class="text-[10px] text-slate-400 mt-1">Per Data Privacy Act, do not store photocopies of IDs.</p>
                        </div>
                    </div>

                    <!-- Authorization letter checkbox for representative -->
                    <div x-show="claimantType === 'authorized_representative'" x-cloak
                         class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs space-y-2">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="authorization_letter_checked" value="1"
                                   class="mt-1 w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-slate-300">
                            <span class="text-amber-900 leading-relaxed">
                                <strong>Representative Verification:</strong> I confirm that I have physically inspected and verified a signed authorization letter from <strong>{{ $motorcycle->customer?->full_name }}</strong> together with a photocopy of their valid government ID.
                            </span>
                        </label>
                    </div>

                    <div>
                        <label for="remarks" class="block text-xs font-semibold text-slate-700 mb-1">Release Remarks (Optional)</label>
                        <input type="text" id="remarks" name="remarks" value="{{ old('remarks') }}"
                               placeholder="e.g. Original tracking slip surrendered"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <a href="{{ route('releases.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                        Cancel
                    </a>
                    <button type="submit"
                            @if(!$orcrCanClaim && !$plateCanClaim) disabled @endif
                            class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                        <i data-lucide="check-check" class="w-4 h-4"></i>
                        Confirm Electronic Release
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
