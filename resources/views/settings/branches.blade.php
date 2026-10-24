@extends('layouts.app')

@section('title', 'Dealership Branches')
@section('page-title', 'Dealership Branches Management')

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">

    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Participating Dealership Branches</h2>
            <p class="text-xs text-slate-500">Configure dealership locations, operating hours, and release collection instructions</p>
        </div>

        <button @click="addModal = true" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Branch
        </button>
    </div>

    <!-- Branches Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($branches as $b)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $b->code }}
                        </span>
                        <h3 class="text-base font-bold text-slate-900 mt-2">{{ $b->name }}</h3>
                        <p class="text-xs text-slate-500">{{ $b->dealership_brand }}</p>
                    </div>

                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $b->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                        {{ $b->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="space-y-1 text-xs text-slate-600 border-t border-slate-100 pt-3">
                    <p class="flex items-center gap-2"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i> {{ $b->full_address }}</p>
                    <p class="flex items-center gap-2"><i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i> {{ $b->contact_number }}</p>
                    <p class="flex items-center gap-2"><i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i> {{ $b->operating_hours }}</p>
                </div>

                <div class="grid grid-cols-3 gap-2 pt-3 border-t border-slate-100 text-center text-xs">
                    <div class="p-2 rounded-lg bg-slate-50">
                        <span class="block font-bold text-slate-800 text-sm">{{ $b->motorcycles_count }}</span>
                        <span class="text-[10px] text-slate-400">Motorcycles</span>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-50">
                        <span class="block font-bold text-slate-800 text-sm">{{ $b->customers_count }}</span>
                        <span class="text-[10px] text-slate-400">Customers</span>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-50">
                        <span class="block font-bold text-slate-800 text-sm">{{ $b->users_count }}</span>
                        <span class="text-[10px] text-slate-400">Staff Assigned</span>
                    </div>
                </div>

                @if($b->pickup_instructions)
                    <div class="p-3 rounded-xl bg-slate-50 text-slate-600 text-[11px] leading-relaxed border border-slate-200">
                        <strong>Pickup Instructions:</strong> {{ $b->pickup_instructions }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Add Branch Modal -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Add New Dealership Branch</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-700"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>

            <form action="{{ route('settings.branches.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Branch Code (e.g. SJA-01)</label>
                        <input type="text" name="code" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Branch Name</label>
                        <input type="text" name="name" required placeholder="e.g. San Jose Antique 3S" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dealership Brand / Franchise</label>
                    <input type="text" name="dealership_brand" value="Yamaha 3S Authorized Dealership" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Street Address</label>
                    <input type="text" name="address_line" required placeholder="e.g. Bancal Street corner Trade Town Road" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Municipality</label>
                        <input type="text" name="municipality" value="San Jose de Buenavista" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Province</label>
                        <input type="text" name="province" value="Antique" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Postal Code</label>
                        <input type="text" name="postal_code" value="5700" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Hotline Contact Number</label>
                        <input type="text" name="contact_number" required placeholder="0917-123-4567" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Operating Hours</label>
                        <input type="text" name="operating_hours" value="Mon - Sat: 8:00 AM - 5:00 PM" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pickup Instructions for Customers</label>
                    <textarea name="pickup_instructions" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs"
                              placeholder="Instructions displayed on tracking portal..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-sm">Save Branch</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
