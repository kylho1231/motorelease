@extends('layouts.app')

@section('title', 'Notification Details #' . $notification->id)
@section('page-title', 'SMS Notification Audit Record')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">SMS Notification Dispatch</span>
                <h2 class="text-lg font-bold text-slate-900">{{ $notification->event_label }}</h2>
                <p class="text-xs text-slate-500">Triggered on {{ $notification->created_at->format('F d, Y h:i:s A') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $notification->status_badge_class }}">
                {{ ucfirst($notification->status) }}
            </span>
        </div>

        <!-- Notification Parameters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs p-4 rounded-xl bg-slate-50 border border-slate-200 mb-6">
            <div>
                <span class="text-slate-400 block text-[11px]">Recipient Mobile Number</span>
                <strong class="font-mono text-sm text-slate-900">{{ $notification->recipient_number }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Target Customer</span>
                <strong class="text-slate-800">{{ $notification->customer?->full_name }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Associated Motorcycle</span>
                <strong class="text-slate-800">{{ $notification->motorcycle?->model }}</strong>
                <span class="block font-mono text-[10px] text-slate-500">Ref: {{ $notification->motorcycle?->tracking_reference }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Branch Origin</span>
                <strong class="text-slate-800">{{ $notification->motorcycle?->branch?->name }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Gateway Driver</span>
                <span class="font-mono text-slate-800">{{ $notification->provider }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Provider Transaction ID</span>
                <span class="font-mono text-slate-800">{{ $notification->provider_message_id ?: 'Queued / Processing' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Idempotency Protection Key</span>
                <span class="font-mono text-[10px] text-slate-500 break-all">{{ $notification->idempotency_key }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Retry Count</span>
                <span class="font-bold text-slate-800">{{ $notification->retry_count }} attempt(s)</span>
            </div>
        </div>

        <!-- Rendered Message Body -->
        <div class="space-y-2 mb-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Dispatched Message Content</h3>
            <div class="p-4 rounded-xl bg-slate-900 text-slate-100 font-mono text-xs leading-relaxed border border-slate-800">
                {{ $notification->message_body }}
            </div>
            <p class="text-[11px] text-slate-400">Characters: {{ strlen($notification->message_body) }} (approx. {{ ceil(strlen($notification->message_body) / 160) }} SMS segment(s))</p>
        </div>

        @if($notification->error_message)
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs mb-6 space-y-1">
                <strong class="font-bold">Provider Error Response:</strong>
                <p class="font-mono text-[11px]">{{ $notification->error_message }}</p>
            </div>
        @endif

        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                &larr; Back to Notification Logs
            </a>

            <form action="{{ route('notifications.resend', $notification) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Trigger Immediate Resend
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
