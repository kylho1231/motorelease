<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Document Claim Stub - {{ $motorcycle->tracking_reference }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-card { border: 1px solid #000 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 flex flex-col items-center text-slate-900">

    <!-- Print Action Controls (Screen only) -->
    <div class="no-print w-full max-w-md mb-6 flex items-center justify-between">
        <a href="{{ route('motorcycles.show', $motorcycle) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Back to Vehicle Records
        </a>
        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
            Print Customer Slip
        </button>
    </div>

    <!-- Official Customer Tracking Slip / Claim Stub -->
    <div class="print-card w-full max-w-md bg-white border border-slate-300 rounded-3xl p-6 sm:p-8 shadow-xl text-center space-y-6">

        <!-- Dealership Branding -->
        <div>
            <div class="mx-auto w-12 h-12 rounded-2xl bg-red-600 text-white font-extrabold text-xl flex items-center justify-center shadow-md mb-2">
                MR
            </div>
            <h1 class="text-lg font-black tracking-tight text-slate-900">MotoRelease System</h1>
            <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">{{ $motorcycle->branch->dealership_brand }}</p>
            <p class="text-[11px] text-slate-500">{{ $motorcycle->branch->name }}</p>
            <p class="text-[10px] text-slate-400">{{ $motorcycle->branch->full_address }} &bull; {{ $motorcycle->branch->contact_number }}</p>
        </div>

        <div class="border-t border-b border-dashed border-slate-300 py-3">
            <span class="block text-[11px] font-bold uppercase tracking-widest text-slate-400">Official Tracking Reference</span>
            <span class="block font-mono text-xl sm:text-2xl font-black text-red-600 tracking-tight mt-0.5">
                {{ $motorcycle->tracking_reference }}
            </span>
        </div>

        <!-- QR Code -->
        <div class="flex flex-col items-center">
            <div class="w-48 h-48 p-2 bg-white border-2 border-slate-900 rounded-2xl shadow-xs">
                <img src="{{ $qrDataUri }}" alt="Tracking QR" class="w-full h-full object-contain">
            </div>
            <p class="text-[11px] font-bold text-slate-700 mt-2">Scan with your smartphone camera</p>
            <p class="text-[10px] text-slate-400">or visit <span class="font-mono text-slate-600">{{ route('customer.track') }}</span></p>
        </div>

        <!-- Customer & Vehicle Data (Data Privacy Masked) -->
        <div class="bg-slate-50 rounded-2xl p-4 text-left text-xs space-y-1.5 border border-slate-200">
            <div class="flex justify-between">
                <span class="text-slate-500">Customer:</span>
                <strong class="text-slate-800">{{ $motorcycle->customer?->full_name }}</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Motorcycle:</span>
                <strong class="text-slate-800">{{ $motorcycle->model }} ({{ $motorcycle->color }})</strong>
            </div>
            <div class="flex justify-between font-mono">
                <span class="text-slate-500">Engine No:</span>
                <span class="text-slate-700">{{ $motorcycle->masked_engine_number }}</span>
            </div>
            <div class="flex justify-between font-mono">
                <span class="text-slate-500">Chassis No:</span>
                <span class="text-slate-700">{{ $motorcycle->masked_chassis_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Purchase Date:</span>
                <span class="text-slate-700">{{ $motorcycle->purchase_date->format('M d, Y') }}</span>
            </div>
            <div class="flex justify-between border-t border-slate-200 pt-1.5 font-bold">
                <span class="text-slate-700">Online Tracking PIN:</span>
                <span class="text-red-600 font-mono">Last 4 digits of registered phone</span>
            </div>
        </div>

        <!-- Requirements for Collection -->
        <div class="text-left text-[11px] text-slate-600 space-y-1.5 bg-red-50/50 p-4 rounded-2xl border border-red-100">
            <p class="font-bold text-red-800 uppercase tracking-wider text-[10px]">What to Bring Upon Release:</p>
            <ul class="list-disc list-inside space-y-0.5 text-slate-700">
                <li>This official tracking slip or your tracking reference number.</li>
                <li>Two (2) original government-issued valid IDs of registered owner.</li>
                <li>If representative: Signed authorization letter & owner's ID photocopy.</li>
            </ul>
        </div>

        <!-- Legal Notice -->
        <div class="text-[9px] text-slate-400 leading-tight">
            <p>You will receive an automated SMS once your OR/CR and/or license plate are verified ready for claim.</p>
            <p class="mt-1">In compliance with the Philippine Data Privacy Act of 2012 (RA 10173). Generated on {{ date('M d, Y h:i A') }}.</p>
        </div>
    </div>

</body>
</html>
