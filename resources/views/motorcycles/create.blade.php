@extends('layouts.app')

@section('title', 'Register Motorcycle')
@section('page-title', 'Register Motorcycle Unit')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h2 class="text-base font-bold text-slate-900">Motorcycle Registration Form</h2>
            <p class="text-xs text-slate-500">
                Register a newly purchased motorcycle to initialize automated tracking references, QR codes, and independent OR/CR & Plate lifecycle monitoring.
            </p>
        </div>

        <form action="{{ route('motorcycles.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Select Customer -->
            <div>
                <label for="customer_id" class="block text-xs font-semibold text-slate-700 mb-1">
                    Registered Motorcycle Owner <span class="text-red-500">*</span>
                </label>
                <select name="customer_id" id="customer_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500">
                    <option value="">-- Choose Registered Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ (old('customer_id', $selectedCustomer?->id) == $c->id) ? 'selected' : '' }}>
                            {{ $c->full_name }} ({{ $c->customer_code }}) &mdash; {{ $c->mobile_number }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1">
                    Can't find customer? <a href="{{ route('customers.create') }}" class="text-red-600 font-bold hover:underline">Register customer first &rarr;</a>
                </p>
            </div>

            <!-- Branch -->
            <div>
                <label for="branch_id" class="block text-xs font-semibold text-slate-700 mb-1">
                    Selling Dealership Branch <span class="text-red-500">*</span>
                </label>
                <select name="branch_id" id="branch_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id', auth()->user()->branch_id) == $b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Vehicle Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="manufacturer" class="block text-xs font-semibold text-slate-700 mb-1">
                        Brand / Make <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="manufacturer" name="manufacturer" value="{{ old('manufacturer', 'Yamaha') }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="model" class="block text-xs font-semibold text-slate-700 mb-1">
                        Model Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="model" name="model" value="{{ old('model') }}" required
                           placeholder="e.g. NMAX 155 ABS"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="variant" class="block text-xs font-semibold text-slate-700 mb-1">Variant / Edition</label>
                    <input type="text" id="variant" name="variant" value="{{ old('variant') }}"
                           placeholder="e.g. Standard / Connected"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="color" class="block text-xs font-semibold text-slate-700 mb-1">
                        Color <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="color" name="color" value="{{ old('color') }}" required
                           placeholder="e.g. Matte Dark Green"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="year_model" class="block text-xs font-semibold text-slate-700 mb-1">Year Model</label>
                    <input type="number" id="year_model" name="year_model" value="{{ old('year_model', date('Y')) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                </div>
                <div>
                    <label for="purchase_date" class="block text-xs font-semibold text-slate-700 mb-1">
                        Purchase Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <!-- Technical Identification Numbers -->
            <div class="space-y-4 pt-2 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Technical Identifiers</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="engine_number" class="block text-xs font-semibold text-slate-700 mb-1">
                            Engine Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="engine_number" name="engine_number" value="{{ old('engine_number') }}" required
                               placeholder="e.g. G3M1E-0194821"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono uppercase focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="chassis_number" class="block text-xs font-semibold text-slate-700 mb-1">
                            Chassis / VIN Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="chassis_number" name="chassis_number" value="{{ old('chassis_number') }}" required
                               placeholder="e.g. MH3SG6410RJ019482"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono uppercase focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="dealer_transaction_ref" class="block text-xs font-semibold text-slate-700 mb-1">Dealer Sales Invoice Ref</label>
                        <input type="text" id="dealer_transaction_ref" name="dealer_transaction_ref" value="{{ old('dealer_transaction_ref') }}"
                               placeholder="e.g. INV-SJA-2026-0104"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                    </div>
                    <div>
                        <label for="registration_reference" class="block text-xs font-semibold text-slate-700 mb-1">LTO Application / MV Ref</label>
                        <input type="text" id="registration_reference" name="registration_reference" value="{{ old('registration_reference') }}"
                               placeholder="Optional LTO MV application ref"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                    </div>
                </div>
            </div>

            <!-- Notice on Tracking ID & Initial Life-cycle -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-start gap-3">
                <i data-lucide="info" class="w-5 h-5 text-slate-400 shrink-0 mt-0.5"></i>
                <p>
                    Saving will automatically generate a collision-safe tracking reference (e.g. <code>MR-SJA-2026-XXXXXX</code>) and an authenticated QR code. Initial tracking records for OR/CR and license plates will be created in <strong>Awaiting</strong> state.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('motorcycles.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm transition">
                    Register Unit & Generate QR Slip
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
