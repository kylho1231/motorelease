<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Release Acknowledgment Receipt - {{ $release->transaction_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-container { border: 1px solid #000 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 flex flex-col items-center text-slate-900">

    <div class="no-print w-full max-w-2xl mb-6 flex items-center justify-between">
        <a href="{{ route('releases.show', $release) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Back to Release Details
        </a>
        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
            Print Official Receipt
        </button>
    </div>

    <!-- Official Receipt Sheet -->
    <div class="print-container w-full max-w-2xl bg-white border border-slate-300 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">

        <!-- Dealership Header -->
        <div class="text-center pb-4 border-b border-slate-300">
            <h1 class="text-xl font-black uppercase tracking-tight text-slate-900">Official Document Release Receipt</h1>
            <p class="text-xs font-bold text-slate-700 mt-1 uppercase tracking-wider">{{ $release->branch->dealership_brand }}</p>
            <p class="text-xs text-slate-600">{{ $release->branch->name }}</p>
            <p class="text-[11px] text-slate-400">{{ $release->branch->full_address }} &bull; Hotline: {{ $release->branch->contact_number }}</p>
        </div>

        <!-- Meta Bar -->
        <div class="flex flex-col sm:flex-row justify-between text-xs gap-2 py-2 bg-slate-50 px-4 rounded-xl border border-slate-200">
            <div>
                <span class="text-slate-500">Transaction No:</span>
                <strong class="font-mono text-slate-900">{{ $release->transaction_code }}</strong>
            </div>
            <div>
                <span class="text-slate-500">Date & Time Released:</span>
                <strong class="text-slate-800">{{ $release->released_at->format('F d, Y h:i A') }}</strong>
            </div>
        </div>

        <!-- Parties Box -->
        <div class="grid grid-cols-2 gap-4 text-xs">
            <div class="border border-slate-200 rounded-xl p-4 space-y-1">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Claimant Information</span>
                <p class="font-bold text-slate-900 text-sm">{{ $release->claimant_name }}</p>
                <p class="text-slate-600">Relationship: <strong class="capitalize">{{ str_replace('_', ' ', $release->claimant_type) }}</strong></p>
                <p class="text-slate-600">Contact: {{ $release->claimant_contact }}</p>
                <p class="text-slate-600">ID Presented: {{ $release->id_type_presented }} ({{ $release->id_number_masked }})</p>
            </div>

            <div class="border border-slate-200 rounded-xl p-4 space-y-1">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Registered Owner Details</span>
                @php $firstBike = $release->releaseItems->first()?->motorcycle; @endphp
                <p class="font-bold text-slate-900 text-sm">{{ $firstBike?->customer?->full_name }}</p>
                <p class="text-slate-600">Vehicle: {{ $firstBike?->model }} ({{ $firstBike?->color }})</p>
                <p class="font-mono text-[11px] text-slate-600">Eng: {{ $firstBike?->masked_engine_number }}</p>
                <p class="font-mono text-[11px] text-slate-600">Tracking Ref: {{ $firstBike?->tracking_reference }}</p>
            </div>
        </div>

        <!-- Released Items List -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Released Document & Plate Inventory</h3>
            <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-100 border-b border-slate-200 text-slate-600 font-bold uppercase">
                    <tr>
                        <th class="px-4 py-2.5">Item Description</th>
                        <th class="px-4 py-2.5">Vehicle Reference</th>
                        <th class="px-4 py-2.5">Recorded Numbers</th>
                        <th class="px-4 py-2.5 text-right">Condition</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($release->releaseItems as $item)
                        <tr>
                            <td class="px-4 py-3 font-bold text-slate-900">
                                {{ $item->item_type_label }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $item->motorcycle->model }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-800">
                                {{ $item->item_identifier }}
                            </td>
                            <td class="px-4 py-3 text-right text-emerald-700 font-bold">
                                Received in Good Order
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Customer Acknowledgment Statement -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 leading-relaxed space-y-1">
            <p class="font-bold text-slate-800">Claimant Acknowledgment:</p>
            <p>
                I hereby acknowledge the physical receipt of the original Land Transportation Office (LTO) registration documents and/or license plate specified above, in good, authentic, and complete physical condition.
            </p>
        </div>

        <!-- Dual Signature Lines -->
        <div class="grid grid-cols-2 gap-8 pt-8 text-center text-xs">
            <div>
                <div class="border-b border-slate-400 pb-1 mb-1.5 h-12 flex items-end justify-center">
                    <span class="font-bold text-slate-800">{{ $release->claimant_name }}</span>
                </div>
                <p class="font-semibold text-slate-700">Signature of Claimant / Receiver</p>
                <p class="text-[10px] text-slate-400">Date: {{ $release->released_at->format('M d, Y') }}</p>
            </div>

            <div>
                <div class="border-b border-slate-400 pb-1 mb-1.5 h-12 flex items-end justify-center">
                    <span class="font-bold text-slate-800">{{ $release->processedByUser->name }}</span>
                </div>
                <p class="font-semibold text-slate-700">Authorized Dealership Releasing Officer</p>
                <p class="text-[10px] text-slate-400">{{ $release->branch->name }}</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-200 pt-4 text-[9px] text-slate-400 text-center">
            MotoRelease Electronic Release Registry &bull; Compliant with Philippine Data Privacy Act of 2012 (RA 10173).
        </div>
    </div>

</body>
</html>
