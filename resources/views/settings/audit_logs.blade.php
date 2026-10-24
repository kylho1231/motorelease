@extends('layouts.app')

@section('title', 'Audit Trail')
@section('page-title', 'System Audit Trail & Security Logs')

@section('content')
<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('settings.audit_logs') }}" class="w-full sm:w-auto flex-1 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[220px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search action, employee name, IP address, description..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-red-500">
            </div>

            <select name="action" class="px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-700">
                <option value="">All Actions</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ $action == $act ? 'selected' : '' }}>{{ $act }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition">
                Filter Logs
            </button>
            @if($search || $action)
                <a href="{{ route('settings.audit_logs') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5">Timestamp</th>
                        <th class="px-4 py-3.5">Action Event</th>
                        <th class="px-4 py-3.5">Responsible User</th>
                        <th class="px-4 py-3.5">Description</th>
                        <th class="px-4 py-3.5">Target Entity</th>
                        <th class="px-4 py-3.5 text-right">IP & Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-500">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-4 py-3 font-bold text-red-600">
                                {{ $log->action }}
                            </td>
                            <td class="px-4 py-3 font-sans font-semibold text-slate-800">
                                {{ $log->user?->name ?? 'System / Anonymous' }}
                            </td>
                            <td class="px-4 py-3 font-sans text-slate-700 max-w-sm truncate" title="{{ $log->description }}">
                                {{ $log->description }}
                            </td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ $log->auditable_type ? class_basename($log->auditable_type) . ' #' . $log->auditable_id : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right text-slate-400 text-[10px]">
                                {{ $log->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400 font-sans text-xs">
                                No audit logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
