@extends('layouts.portal')

@section('content')
<div class="max-w-md mx-auto space-y-6">

    <div class="text-center space-y-2 mb-6">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
            <span class="w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
            Live Document Availability Check
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Track Your OR/CR & Plate
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
            Enter your official tracking reference number from your claim slip and the last 4 digits of your registered phone number.
        </p>
    </div>

    <!-- Tracking Verification Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xl">
        <form action="{{ route('customer.verify') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="tracking_reference" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Tracking Reference Code
                </label>
                <div class="relative">
                    <input type="text" id="tracking_reference" name="tracking_reference"
                           value="{{ old('tracking_reference', $ref) }}" required autofocus
                           placeholder="e.g. MR-SJA-2026-000003"
                           class="w-full pl-4 pr-10 py-3 rounded-2xl border border-slate-300 text-sm font-mono font-bold uppercase placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    <div class="absolute right-3.5 top-3.5 text-slate-400">
                        <i data-lucide="qr-code" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Printed on your official dealership claim stub.</p>
            </div>

            <div>
                <label for="pin" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Security Verification PIN
                </label>
                <div class="relative">
                    <input type="password" id="pin" name="pin" maxlength="4" required inputmode="numeric"
                           placeholder="••••"
                           class="w-full pl-4 pr-10 py-3 rounded-2xl border border-slate-300 text-base font-mono tracking-widest placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    <div class="absolute right-3.5 top-3.5 text-slate-400">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">
                    Your security PIN is the <strong>last 4 digits</strong> of your registered mobile number.
                </p>
            </div>

            <button type="submit"
                    class="w-full py-3.5 px-4 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 shadow-lg shadow-red-900/30 transition flex items-center justify-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i>
                Check Document Availability
            </button>
        </form>

        <!-- Quick Demo Hint for Testing -->
        <div class="mt-6 pt-5 border-t border-slate-100 text-center">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Sample Tracking References:</p>
            <div class="space-y-1 text-xs">
                <div class="flex items-center justify-between p-1.5 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="font-mono text-slate-700">MR-SJA-2026-000003</span>
                    <button type="button" onclick="fillTest('MR-SJA-2026-000003', '8921')" class="text-red-600 font-bold hover:underline">Use Demo (Ready)</button>
                </div>
                <div class="flex items-center justify-between p-1.5 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="font-mono text-slate-700">MR-SJA-2026-000002</span>
                    <button type="button" onclick="fillTest('MR-SJA-2026-000002', '3412')" class="text-red-600 font-bold hover:underline">Use Demo (Partial)</button>
                </div>
                <div class="flex items-center justify-between p-1.5 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="font-mono text-slate-700">MR-SJA-2026-000005</span>
                    <button type="button" onclick="fillTest('MR-SJA-2026-000005', '2388')" class="text-red-600 font-bold hover:underline">Use Demo (Claimed)</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy & Security Notice -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 text-xs text-slate-500 flex items-start gap-3 shadow-xs">
        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
        <div class="space-y-1">
            <strong class="text-slate-800">Your Privacy is Protected</strong>
            <p class="leading-relaxed">
                In compliance with Philippine Republic Act 10173, sensitive vehicle identifiers (engine, chassis, and ID numbers) are masked on public displays.
            </p>
        </div>
    </div>

</div>

<script>
    function fillTest(ref, pin) {
        document.getElementById('tracking_reference').value = ref;
        document.getElementById('pin').value = pin;
    }
</script>
@endsection
