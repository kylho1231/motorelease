@extends('layouts.app')

@section('title', 'SMS Delivery Logs')
@section('page-title', 'Automated SMS Notifications & Delivery Logs')

@section('content')
<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('notifications.index') }}" class="w-full sm:w-auto flex-1 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[220px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search recipient phone, message body, provider ID, tracking ref..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-red-500">
            </div>

            <select name="status" class="px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-700">
                <option value="">All Delivery Statuses</option>
                <option value="queued" {{ $status == 'queued' ? 'selected' : '' }}>Queued</option>
                <option value="delivered" {{ $status == 'delivered' ? 'selected' : '' }}>Delivered / Sent</option>
                <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>

            <select name="event_type" class="px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-700">
                <option value="">All Notification Events</option>
                <option value="orcr_ready" {{ $eventType == 'orcr_ready' ? 'selected' : '' }}>OR/CR Ready</option>
                <option value="plate_ready" {{ $eventType == 'plate_ready' ? 'selected' : '' }}>License Plate Ready</option>
                <option value="combined_ready" {{ $eventType == 'combined_ready' ? 'selected' : '' }}>Combined Ready</option>
                <option value="pickup_reminder" {{ $eventType == 'pickup_reminder' ? 'selected' : '' }}>Pickup Reminder</option>
                <option value="status_hold" {{ $eventType == 'status_hold' ? 'selected' : '' }}>Document Hold</option>
            </select>

            @if(auth()->user()->isAdmin())
                <select name="branch_id" class="px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-700">
                    <option value="">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            @endif

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition">
                Filter
            </button>
            @if($search || $status || $eventType || $branchId)
                <a href="{{ route('notifications.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>
    </div>

    <!-- Notifications Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5">Trigger Timestamp</th>
                        <th class="px-4 py-3.5">Recipient & Owner</th>
                        <th class="px-4 py-3.5">Event Type</th>
                        <th class="px-4 py-3.5">Message Content</th>
                        <th class="px-4 py-3.5">Gateway Provider</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Status</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($notifications as $notif)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3.5 text-slate-600 whitespace-nowrap">
                                {{ $notif->created_at->format('M d, Y h:i A') }}
                                <span class="block text-[10px] text-slate-400">By: {{ $notif->triggeredByUser?->name ?? 'Automatic' }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-mono font-bold text-slate-900">{{ $notif->recipient_number }}</span>
                                <span class="block text-[11px] text-slate-500">{{ $notif->customer?->full_name }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-semibold text-slate-700">{{ $notif->event_label }}</span>
                                <span class="block text-[10px] font-mono text-slate-400">{{ $notif->motorcycle?->tracking_reference }}</span>
                            </td>
                            <td class="px-4 py-3.5 max-w-xs text-slate-600 truncate" title="{{ $notif->message_body }}">
                                {{ $notif->message_body }}
                            </td>
                            <td class="px-4 py-3.5 text-slate-500 font-mono text-[11px] whitespace-nowrap">
                                {{ $notif->provider }}
                                @if($notif->provider_message_id)
                                    <span class="block text-[10px] text-slate-400">ID: {{ $notif->provider_message_id }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $notif->status_badge_class }}">
                                    {{ ucfirst($notif->status) }}
                                </span>
                                @if($notif->retry_count > 0)
                                    <span class="block text-[10px] text-slate-400">Tries: {{ $notif->retry_count }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('notifications.show', $notif) }}" class="inline-flex items-center text-xs font-bold text-slate-700 hover:text-red-600 whitespace-nowrap">
                                    View
                                </a>
                                @if($notif->status === 'failed' || auth()->user()->isAdmin())
                                    <form action="{{ route('notifications.resend', $notif) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center text-xs font-bold text-red-600 hover:underline whitespace-nowrap">
                                            Resend
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                No SMS notifications found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
