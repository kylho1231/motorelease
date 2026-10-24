@extends('layouts.app')

@section('title', 'Customer Profile - ' . $customer->full_name)
@section('page-title', 'Customer Profile Overview')

@section('content')
<div class="space-y-6">

    <!-- Top Profile Header -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-slate-900 to-slate-800 text-white flex items-center justify-center font-black text-xl shadow-md">
                {{ strtoupper(substr($customer->first_name, 0, 1) . substr($customer->last_name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-xl font-bold text-slate-900">{{ $customer->full_name }}</h2>
                    <span class="px-2 py-0.5 rounded-full text-xs font-mono font-bold bg-slate-100 border border-slate-200 text-slate-700">
                        {{ $customer->customer_code }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-3">
                    <span class="flex items-center gap-1"><i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i> {{ $customer->mobile_number }}</span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i> {{ $customer->address_line }}, {{ $customer->barangay }}, {{ $customer->municipality }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('customers.edit', $customer) }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition flex items-center gap-1.5">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit Profile
            </a>
            <a href="{{ route('motorcycles.create', ['customer_id' => $customer->id]) }}" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Motorcycle
            </a>
        </div>
    </div>

    <!-- Associated Motorcycles Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Registered Motorcycles</h3>
                <p class="text-xs text-slate-500">Document and physical plate availability status for each unit owned</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                {{ $customer->motorcycles->count() }} Vehicle(s)
            </span>
        </div>

        @if($customer->motorcycles->isEmpty())
            <div class="p-8 text-center text-slate-400 text-sm">
                <i data-lucide="bike" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                No motorcycles registered yet for this customer.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($customer->motorcycles as $bike)
                    <div class="p-5 hover:bg-slate-50/60 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2.5">
                                <span class="font-bold text-base text-slate-900">{{ $bike->model }}</span>
                                <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200">
                                    {{ $bike->tracking_reference }}
                                </span>
                                <span class="text-xs text-slate-500">Color: {{ $bike->color }} ({{ $bike->year_model }})</span>
                            </div>
                            <div class="text-xs text-slate-500 flex flex-wrap items-center gap-4">
                                <span>Engine: <strong class="font-mono text-slate-700">{{ $bike->engine_number }}</strong></span>
                                <span>Chassis: <strong class="font-mono text-slate-700">{{ $bike->chassis_number }}</strong></span>
                                <span>Purchase Date: {{ $bike->purchase_date->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <!-- Status Badges & Quick Action -->
                        <div class="flex items-center gap-3">
                            <div class="text-right text-xs space-y-1">
                                <div class="flex items-center gap-1.5 justify-end">
                                    <span class="text-slate-400 text-[11px]">OR/CR:</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $bike->orcrRecord?->status_badge_class }}">
                                        {{ $bike->orcrRecord?->status_label ?? 'Pending' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 justify-end">
                                    <span class="text-slate-400 text-[11px]">Plate:</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $bike->licensePlateRecord?->status_badge_class }}">
                                        {{ $bike->licensePlateRecord?->status_label ?? 'Pending' }}
                                    </span>
                                </div>
                            </div>

                            <a href="{{ route('motorcycles.show', $bike) }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                                Manage Unit
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Notification History for this Customer -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Automated Notification Activity</h3>
            <span class="text-xs text-slate-500">{{ $customer->notifications->count() }} sent log(s)</span>
        </div>

        @if($customer->notifications->isEmpty())
            <div class="p-8 text-center text-slate-400 text-sm">
                No notification attempts recorded yet for this customer.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($customer->notifications as $notif)
                    <div class="p-4 hover:bg-slate-50 transition flex items-start justify-between gap-4">
                        <div class="space-y-1 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800">{{ $notif->event_label }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $notif->status_badge_class }}">
                                    {{ ucfirst($notif->status) }}
                                </span>
                                <span class="text-slate-400 text-[11px]">{{ $notif->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                            <p class="text-slate-600 italic bg-slate-50 p-2 rounded-lg border border-slate-100">
                                "{{ $notif->message_body }}"
                            </p>
                            <p class="text-[11px] text-slate-400">
                                Recipient: {{ $notif->recipient_number }} &bull; Provider ID: {{ $notif->provider_message_id ?? 'Queued' }}
                            </p>
                        </div>

                        @if($notif->status === 'failed')
                            <form action="{{ route('notifications.resend', $notif) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition">
                                    Retry SMS
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
