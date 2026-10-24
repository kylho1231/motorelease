@extends('layouts.app')

@section('title', 'Edit Customer - ' . $customer->full_name)
@section('page-title', 'Edit Customer Information')

@section('content')
<div class="max-w-3xl space-y-4 sm:space-y-6">

    <div class="bg-white rounded-xl sm:rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-xs">
        <div class="border-b border-slate-100 pb-3 sm:pb-4 mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-slate-900">Update Customer Records</h2>
                <p class="text-[11px] sm:text-xs text-slate-500">Edit contact details and residential address</p>
            </div>
            <span class="font-mono text-[11px] sm:text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 w-fit">
                {{ $customer->customer_code }}
            </span>
        </div>

        <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Branch -->
            <div>
                <label for="branch_id" class="block text-xs font-semibold text-slate-700 mb-1">Assigned Dealership Branch</label>
                <select name="branch_id" id="branch_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id', $customer->branch_id) == $b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Name Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label for="first_name" class="block text-xs font-semibold text-slate-700 mb-1">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $customer->first_name) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="middle_name" class="block text-xs font-semibold text-slate-700 mb-1">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $customer->middle_name) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="last_name" class="block text-xs font-semibold text-slate-700 mb-1">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $customer->last_name) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="suffix" class="block text-xs font-semibold text-slate-700 mb-1">Suffix</label>
                    <input type="text" id="suffix" name="suffix" value="{{ old('suffix', $customer->suffix) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="mobile_number" class="block text-xs font-semibold text-slate-700 mb-1">Mobile Phone (PH)</label>
                    <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $customer->mobile_number) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                </div>
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <!-- Address Fields -->
            <div class="space-y-4 pt-2 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Address Details</h3>

                <div>
                    <label for="address_line" class="block text-xs font-semibold text-slate-700 mb-1">Street Address</label>
                    <input type="text" id="address_line" name="address_line" value="{{ old('address_line', $customer->address_line) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="barangay" class="block text-xs font-semibold text-slate-700 mb-1">Barangay</label>
                        <input type="text" id="barangay" name="barangay" value="{{ old('barangay', $customer->barangay) }}" required
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label for="municipality" class="block text-xs font-semibold text-slate-700 mb-1">Municipality</label>
                        <input type="text" id="municipality" name="municipality" value="{{ old('municipality', $customer->municipality) }}" required
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label for="province" class="block text-xs font-semibold text-slate-700 mb-1">Province</label>
                        <input type="text" id="province" name="province" value="{{ old('province', $customer->province) }}" required
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                </div>

                <div>
                    <label for="postal_code" class="block text-xs font-semibold text-slate-700 mb-1">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}" required
                           class="w-32 px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                </div>
            </div>

            <div class="space-y-4 pt-2 border-t border-slate-100">
                <div>
                    <label for="notification_preference" class="block text-xs font-semibold text-slate-700 mb-1">Notification Preference</label>
                    <select name="notification_preference" id="notification_preference" class="w-full sm:w-64 px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                        <option value="sms" {{ old('notification_preference', $customer->notification_preference) == 'sms' ? 'selected' : '' }}>SMS Only</option>
                        <option value="both" {{ old('notification_preference', $customer->notification_preference) == 'both' ? 'selected' : '' }}>SMS & Email</option>
                        <option value="none" {{ old('notification_preference', $customer->notification_preference) == 'none' ? 'selected' : '' }}>No Notifications</option>
                    </select>
                </div>

                <div>
                    <label for="remarks" class="block text-xs font-semibold text-slate-700 mb-1">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">{{ old('remarks', $customer->remarks) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('customers.show', $customer) }}" class="text-xs text-slate-500 hover:text-slate-800 font-semibold">
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
