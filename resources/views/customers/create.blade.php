@extends('layouts.app')

@section('title', 'Register Customer')
@section('page-title', 'Register New Dealership Customer')

@section('content')
<div class="max-w-3xl space-y-4 sm:space-y-6">

    <div class="bg-white rounded-xl sm:rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-xs">
        <div class="border-b border-slate-100 pb-3 sm:pb-4 mb-4 sm:mb-6">
            <h2 class="text-sm sm:text-base font-bold text-slate-900">Customer Registration Form</h2>
            <p class="text-[11px] sm:text-xs text-slate-500">
                Collect essential contact and identity information required for motorcycle document release notifications.
            </p>
        </div>

        <form action="{{ route('customers.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Dealership Branch -->
            <div>
                <label for="branch_id" class="block text-xs font-semibold text-slate-700 mb-1">
                    Dealership Branch <span class="text-red-500">*</span>
                </label>
                <select name="branch_id" id="branch_id" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ (old('branch_id', auth()->user()->branch_id) == $b->id) ? 'selected' : '' }}>
                            {{ $b->name }} ({{ $b->municipality }}, {{ $b->province }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Customer Name Parts -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label for="first_name" class="block text-xs font-semibold text-slate-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                           placeholder="e.g. Maria"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="middle_name" class="block text-xs font-semibold text-slate-700 mb-1">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                           placeholder="e.g. Santos"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="last_name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                           placeholder="e.g. Dela Cruz"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="suffix" class="block text-xs font-semibold text-slate-700 mb-1">Suffix (Optional)</label>
                    <input type="text" id="suffix" name="suffix" value="{{ old('suffix') }}"
                           placeholder="e.g. Jr., III"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="mobile_number" class="block text-xs font-semibold text-slate-700 mb-1">
                        Mobile Phone <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" required
                           placeholder="09171234567"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-red-500">
                    <p class="text-[10px] text-slate-400 mt-1">Used for automated SMS availability notifications.</p>
                </div>
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email (Optional)</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           placeholder="customer@example.com"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            <!-- Address Fields -->
            <div class="space-y-4 pt-2 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Residential Address</h3>

                <div>
                    <label for="address_line" class="block text-xs font-semibold text-slate-700 mb-1">
                        House No. / Street / Purok / Sitio <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="address_line" name="address_line" value="{{ old('address_line') }}" required
                           placeholder="e.g. Purok Kawayan, Trade Town"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="barangay" class="block text-xs font-semibold text-slate-700 mb-1">
                            Barangay <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="barangay" name="barangay" value="{{ old('barangay') }}" required
                               placeholder="e.g. Funda-Dalipe"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="municipality" class="block text-xs font-semibold text-slate-700 mb-1">
                            Municipality <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="municipality" name="municipality" value="{{ old('municipality', 'San Jose de Buenavista') }}" required
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="province" class="block text-xs font-semibold text-slate-700 mb-1">
                            Province <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="province" name="province" value="{{ old('province', 'Antique') }}" required
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label for="postal_code" class="block text-xs font-semibold text-slate-700 mb-1">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', '5700') }}" required
                           class="w-32 px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            <!-- Notification & Privacy -->
            <div class="space-y-4 pt-2 border-t border-slate-100">
                <div>
                    <label for="notification_preference" class="block text-xs font-semibold text-slate-700 mb-1">
                        Notification Preference
                    </label>
                    <select name="notification_preference" id="notification_preference" class="w-full sm:w-64 px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                        <option value="sms" {{ old('notification_preference', 'sms') == 'sms' ? 'selected' : '' }}>SMS Only (Recommended)</option>
                        <option value="both" {{ old('notification_preference') == 'both' ? 'selected' : '' }}>SMS & Email</option>
                        <option value="none" {{ old('notification_preference') == 'none' ? 'selected' : '' }}>No Automated Notifications</option>
                    </select>
                </div>

                <div>
                    <label for="remarks" class="block text-xs font-semibold text-slate-700 mb-1">Staff Remarks (Optional)</label>
                    <textarea id="remarks" name="remarks" rows="2"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm placeholder-slate-400"
                              placeholder="Special customer requests or notes...">{{ old('remarks') }}</textarea>
                </div>

                <!-- Data Privacy Consent Checkbox -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="privacy_consent" value="1" required checked
                               class="mt-1 w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300">
                        <span class="text-xs text-slate-600 leading-relaxed">
                            <strong>Data Privacy Acknowledgment:</strong> The customer has been informed and consents to the collection of their contact information in accordance with the Philippine Data Privacy Act of 2012 (RA 10173), solely for official motorcycle document release and availability notifications.
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('customers.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition min-h-[44px] flex items-center justify-center">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm transition min-h-[44px] cursor-pointer flex items-center justify-center">
                    Save Customer &amp; Continue
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
