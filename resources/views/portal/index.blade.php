@extends('layouts.portal')

@section('content')
<div class="max-w-lg mx-auto space-y-6 sm:space-y-8">

    <!-- Cinematic Hero Section -->
    <div class="text-center space-y-2.5 sm:space-y-3">
        <div class="inline-flex items-center gap-2 px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full text-xs font-bold bg-slate-900/90 text-rose-300 border border-rose-500/30 shadow-lg shadow-rose-950/40 backdrop-blur-md">
            <span class="relative flex h-2 w-2">
                <span class="getlayers-radar absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
            </span>
            <span class="tracking-wide uppercase text-[9px] sm:text-[10px] font-extrabold">Live Document Availability Engine</span>
        </div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight">
            Track Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-500 via-red-400 to-orange-400">OR/CR &amp; Plate</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 leading-relaxed max-w-sm sm:max-w-md mx-auto">
            Verify real-time document release status directly from the dealership vault in San Jose de Buenavista.
        </p>
    </div>

    <!-- GetLayers Spotlight Tracking Card -->
    <div data-spotlight class="getlayers-glass rounded-2xl sm:rounded-3xl p-4 sm:p-7 lg:p-9 relative overflow-hidden group">
        <!-- Subtle Top Glow Bar -->
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-rose-500 to-transparent opacity-80"></div>

        <form action="{{ route('customer.verify') }}" method="POST" class="space-y-4 sm:space-y-6">
            @csrf

            <!-- Tracking Reference Input -->
            <div class="space-y-1.5 sm:space-y-2">
                <div class="flex items-center justify-between">
                    <label for="tracking_reference" class="block text-[11px] sm:text-xs font-extrabold uppercase tracking-wider sm:tracking-widest text-slate-300">
                        Tracking Reference Code
                    </label>
                    <span class="text-[9px] sm:text-[10px] text-slate-500 font-mono">FROM CLAIM SLIP</span>
                </div>
                <div class="relative">
                    <input type="text" id="tracking_reference" name="tracking_reference"
                           value="{{ old('tracking_reference', $ref) }}" required autofocus
                           placeholder="MR-SJA-2026-000003"
                           class="w-full pl-3.5 sm:pl-4 pr-10 sm:pr-12 py-2.5 sm:py-3.5 rounded-xl sm:rounded-2xl bg-slate-950/80 border border-slate-700/80 text-white font-mono text-xs sm:text-sm font-bold tracking-wide placeholder-slate-600 focus:outline-hidden focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition shadow-inner">
                    <div class="absolute right-3.5 sm:right-4 top-2.5 sm:top-3.5 text-slate-500 pointer-events-none">
                        <i data-lucide="qr-code" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </div>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500">Scan QR code on your slip or type reference manually.</p>
            </div>

            <!-- PIN Input -->
            <div class="space-y-1.5 sm:space-y-2">
                <div class="flex items-center justify-between">
                    <label for="pin" class="block text-[11px] sm:text-xs font-extrabold uppercase tracking-wider sm:tracking-widest text-slate-300">
                        Verification PIN
                    </label>
                    <span class="text-[9px] sm:text-[10px] text-rose-400 font-bold uppercase tracking-wider">LAST 4 DIGITS OF PHONE</span>
                </div>
                <div class="relative">
                    <input type="password" id="pin" name="pin" maxlength="4" required inputmode="numeric"
                           placeholder="••••"
                           class="w-full pl-3.5 sm:pl-4 pr-10 sm:pr-12 py-2.5 sm:py-3.5 rounded-xl sm:rounded-2xl bg-slate-950/80 border border-slate-700/80 text-white font-mono text-base sm:text-lg font-black tracking-widest placeholder-slate-600 focus:outline-hidden focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition shadow-inner">
                    <div class="absolute right-3.5 sm:right-4 top-2.5 sm:top-3.5 text-slate-500 pointer-events-none">
                        <i data-lucide="lock" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </div>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-400">
                    Your PIN is the <strong>last 4 digits</strong> of your registered buyer mobile phone number.
                </p>
            </div>

            <!-- Submit CTA -->
            <button type="submit"
                    class="w-full py-3 sm:py-4 px-4 sm:px-6 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-black uppercase tracking-wider text-white bg-gradient-to-r from-rose-600 via-red-600 to-rose-700 hover:from-rose-500 hover:to-red-500 shadow-xl shadow-rose-950/60 active:scale-[0.99] transition duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Verify Document Availability</span>
            </button>
        </form>

        <!-- Verification PIN Concierge Guide (How to get your PIN) -->
        <div class="p-3.5 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-900/90 border border-slate-800 text-xs text-slate-300 space-y-3 shadow-xl backdrop-blur-md">
            <div class="flex items-center gap-2 text-rose-400 font-bold">
                <div class="p-1.5 rounded-lg bg-rose-950/80 border border-rose-800/60 text-rose-400">
                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                </div>
                <span class="text-xs uppercase tracking-wider text-white font-black">How Do I Get My Verification PIN?</span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3 pt-1">
                <div class="p-3 sm:p-3.5 rounded-lg sm:rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-1">
                    <div class="flex items-center gap-1.5 text-slate-200 font-bold text-xs">
                        <i data-lucide="smartphone" class="w-3.5 h-3.5 text-rose-400"></i>
                        <span>1. Your Registered Phone</span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-slate-400 leading-relaxed">
                        By default, your PIN is the <strong>last 4 digits</strong> of the mobile number you provided during purchase (e.g., phone <span class="font-mono text-rose-300">0917-xxx-1234</span> &rarr; PIN: <strong class="font-mono text-emerald-400 font-black">1234</strong>).
                    </p>
                </div>

                <div class="p-3 sm:p-3.5 rounded-lg sm:rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-1">
                    <div class="flex items-center gap-1.5 text-slate-200 font-bold text-xs">
                        <i data-lucide="receipt" class="w-3.5 h-3.5 text-blue-400"></i>
                        <span>2. Official Claim Stub</span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-slate-400 leading-relaxed">
                        Check the printed <strong>MotoRelease Tracking Slip</strong> given to you upon motorcycle purchase. Your Tracking Code and 4-digit PIN are printed on the slip.
                    </p>
                </div>
            </div>

            <div class="pt-1 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-1.5 sm:gap-2 text-[10px] sm:text-[11px] text-slate-400 border-t border-slate-800/60">
                <span>Need assistance?</span>
                <span class="text-rose-400 font-semibold flex items-center gap-1">
                    <i data-lucide="phone-call" class="w-3 h-3"></i> Yamaha San Jose Desk: 0917-123-4567
                </span>
            </div>
        </div>

        <!-- Interactive Quick-Test Demo Drawer (GetLayers Style) -->
        <div class="mt-6 pt-5 sm:mt-8 sm:pt-6 border-t border-slate-800/80">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">One-Click Testing Scenarios</span>
                <span class="text-[9px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 font-mono">PILOT DATA</span>
            </div>

            <div class="grid grid-cols-1 gap-2 text-xs">
                <!-- Ready Demo -->
                <button type="button" onclick="fillTest('MR-SJA-2026-000003', '8921')"
                        class="p-2.5 rounded-xl bg-slate-950/60 hover:bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition flex items-center justify-between text-left group">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-xs shadow-emerald-400/50"></span>
                        <div>
                            <span class="font-mono text-xs text-slate-200 font-bold block">MR-SJA-2026-000003</span>
                            <span class="text-[10px] text-slate-500">NMAX 155 &bull; PIN: 8921</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-950/80 text-emerald-300 border border-emerald-800/50">
                        Ready for Pickup
                    </span>
                </button>

                <!-- Partial Demo -->
                <button type="button" onclick="fillTest('MR-SJA-2026-000002', '3412')"
                        class="p-2.5 rounded-xl bg-slate-950/60 hover:bg-slate-900 border border-slate-800 hover:border-amber-500/40 transition flex items-center justify-between text-left group">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-amber-400 shadow-xs shadow-amber-400/50"></span>
                        <div>
                            <span class="font-mono text-xs text-slate-200 font-bold block">MR-SJA-2026-000002</span>
                            <span class="text-[10px] text-slate-500">Aerox 155 &bull; PIN: 3412</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-950/80 text-amber-300 border border-amber-800/50">
                        Partially Received
                    </span>
                </button>

                <!-- Claimed Demo -->
                <button type="button" onclick="fillTest('MR-SJA-2026-000005', '2388')"
                        class="p-2.5 rounded-xl bg-slate-950/60 hover:bg-slate-900 border border-slate-800 hover:border-blue-500/40 transition flex items-center justify-between text-left group">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-400 shadow-xs shadow-blue-400/50"></span>
                        <div>
                            <span class="font-mono text-xs text-slate-200 font-bold block">MR-SJA-2026-000005</span>
                            <span class="text-[10px] text-slate-500">Sniper 155 &bull; PIN: 2388</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-950/80 text-blue-300 border border-blue-800/50">
                        Claimed &amp; Released
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Privacy Assurance Deck -->
    <div class="p-3.5 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800/80 text-[10px] sm:text-xs text-slate-400 flex items-start gap-3 sm:gap-3.5 shadow-lg backdrop-blur-md">
        <div class="p-1.5 sm:p-2 rounded-lg sm:rounded-xl bg-emerald-950/80 border border-emerald-800/60 text-emerald-400 shrink-0">
            <i data-lucide="shield-check" class="w-4 h-4 sm:w-5 sm:h-5"></i>
        </div>
        <div class="space-y-0.5 sm:space-y-1">
            <strong class="text-slate-200 font-bold block text-xs sm:text-sm">Philippine Data Privacy Act (RA 10173) Protected</strong>
            <p class="leading-relaxed text-slate-400 text-[10px] sm:text-[11px]">
                Sensitive vehicle identifiers (engine, chassis, and ID serials) are cryptographically masked. Contact numbers are protected under two-factor reference validation.
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
