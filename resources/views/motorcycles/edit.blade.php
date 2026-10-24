@extends('layouts.app')

@section('title', 'Edit Motorcycle - ' . $motorcycle->tracking_reference)
@section('page-title', 'Edit Motorcycle Records')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Update Vehicle Details</h2>
                <p class="text-xs text-slate-500">Edit registration parameters and identifiers</p>
            </div>
            <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-700">
                {{ $motorcycle->tracking_reference }}
            </span>
        </div>

        <form action="{{ route('motorcycles.update', $motorcycle) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Branch -->
            <div>
                <label for="branch_id" class="block text-xs font-semibold text-slate-700 mb-1">Selling Dealership Branch</label>
                <select name="branch_id" id="branch_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id', $motorcycle->branch_id) == $b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Vehicle Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="manufacturer" class="block text-xs font-semibold text-slate-700 mb-1">Brand / Make</label>
                    <input type="text" id="manufacturer" name="manufacturer" value="{{ old('manufacturer', $motorcycle->manufacturer) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="model" class="block text-xs font-semibold text-slate-700 mb-1">Model Name</label>
                    <input type="text" id="model" name="model" value="{{ old('model', $motorcycle->model) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="variant" class="block text-xs font-semibold text-slate-700 mb-1">Variant</label>
                    <input type="text" id="variant" name="variant" value="{{ old('variant', $motorcycle->variant) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="color" class="block text-xs font-semibold text-slate-700 mb-1">Color</label>
                    <input type="text" id="color" name="color" value="{{ old('color', $motorcycle->color) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="year_model" class="block text-xs font-semibold text-slate-700 mb-1">Year Model</label>
                    <input type="number" id="year_model" name="year_model" value="{{ old('year_model', $motorcycle->year_model) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                </div>
                <div>
                    <label for="purchase_date" class="block text-xs font-semibold text-slate-700 mb-1">Purchase Date</label>
                    <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $motorcycle->purchase_date->format('Y-m-d')) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div>
                    <label for="engine_number" class="block text-xs font-semibold text-slate-700 mb-1">Engine Number</label>
                    <input type="text" id="engine_number" name="engine_number" value="{{ old('engine_number', $motorcycle->engine_number) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono uppercase">
                </div>
                <div>
                    <label for="chassis_number" class="block text-xs font-semibold text-slate-700 mb-1">Chassis Number</label>
                    <input type="text" id="chassis_number" name="chassis_number" value="{{ old('chassis_number', $motorcycle->chassis_number) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono uppercase">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="dealer_transaction_ref" class="block text-xs font-semibold text-slate-700 mb-1">Dealer Sales Invoice</label>
                    <input type="text" id="dealer_transaction_ref" name="dealer_transaction_ref" value="{{ old('dealer_transaction_ref', $motorcycle->dealer_transaction_ref) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                </div>
                <div>
                    <label for="registration_reference" class="block text-xs font-semibold text-slate-700 mb-1">Registration Reference</label>
                    <input type="text" id="registration_reference" name="registration_reference" value="{{ old('registration_reference', $motorcycle->registration_reference) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                </div>
                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Record Status</label>
                    <select name="status" id="status" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                        <option value="active" {{ old('status', $motorcycle->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status', $motorcycle->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $motorcycle->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('motorcycles.show', $motorcycle) }}" class="text-xs text-slate-500 hover:text-slate-800 font-semibold">
                    &larr; Cancel and go back
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
