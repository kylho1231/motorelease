@extends('layouts.app')

@section('title', 'System Settings & Templates')
@section('page-title', 'System Settings & SMS Templates')

@section('content')
<div class="space-y-6">

    <!-- Global Settings Form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-1">Dealership System Parameters</h2>
        <p class="text-xs text-slate-500 mb-5">Configure system branding, SMS provider settings, and release policies</p>

        <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dealership Name</label>
                    <input type="text" name="dealership_name" value="{{ $settings['dealership_name']->value ?? 'Yamaha Authorized Dealership - San Jose Antique' }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Active SMS Driver</label>
                    <select name="sms_provider_active" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs">
                        <option value="mock" {{ ($settings['sms_provider_active']->value ?? 'mock') == 'mock' ? 'selected' : '' }}>Mock Gateway (Safe Simulation / Local Dev)</option>
                        <option value="semaphore" {{ ($settings['sms_provider_active']->value ?? '') == 'semaphore' ? 'selected' : '' }}>Semaphore Philippine SMS API</option>
                    </select>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                <strong class="font-bold text-slate-800">SMS Gateway Configuration Status</strong>
                <p class="text-slate-600">
                    Currently operating in <strong>{{ env('SMS_DRIVER', 'mock') }}</strong> mode. When in mock mode, real SMS credits are not consumed and all generated messages are logged to the database and terminal logs.
                </p>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-100">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition">
                    Save System Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Notification Templates List & Editor -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm">Automated SMS Notification Templates</h3>
            <p class="text-xs text-slate-500">
                Customize automated text messages sent to customers upon verified readiness and release reminders. Supported placeholders: <code>{customer_name}</code>, <code>{model}</code>, <code>{branch_name}</code>, <code>{tracking_ref}</code>, <code>{tracking_url}</code>, <code>{plate_number}</code>
            </p>
        </div>

        <div class="divide-y divide-slate-100 p-6 space-y-6">
            @foreach($templates as $tmpl)
                <form action="{{ route('settings.template.update', $tmpl) }}" method="POST" class="space-y-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="font-mono text-xs font-bold text-red-600 px-2 py-0.5 rounded bg-white border border-slate-200">
                                {{ $tmpl->code }}
                            </span>
                            <span class="font-bold text-sm text-slate-900 ml-2">{{ $tmpl->name }}</span>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer text-xs">
                            <input type="checkbox" name="is_active" value="1" {{ $tmpl->is_active ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300">
                            <span class="font-semibold text-slate-700">Enabled</span>
                        </label>
                    </div>

                    <p class="text-xs text-slate-500">{{ $tmpl->description }}</p>

                    <div>
                        <input type="hidden" name="name" value="{{ $tmpl->name }}">
                        <textarea name="body_template" rows="2" required
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono text-xs text-slate-800 focus:ring-2 focus:ring-red-500">{{ $tmpl->body_template }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                            Update Template
                        </button>
                    </div>
                </form>
            @endforeach
        </div>
    </div>

</div>
@endsection
