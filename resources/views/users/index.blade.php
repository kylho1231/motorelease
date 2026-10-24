@extends('layouts.app')

@section('title', 'Staff Management')
@section('page-title', 'Employee Accounts & Permissions')

@section('content')
<div class="space-y-6">

    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Dealership Staff Directory</h2>
            <p class="text-xs text-slate-500">Manage user accounts, assigned branches, and role-based access levels</p>
        </div>

        <a href="{{ route('users.create') }}" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Add Employee
        </a>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">Employee Name</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Work Email</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Assigned Branch</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">System Role</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Account Status</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Last Login</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3.5 font-bold text-slate-800 whitespace-nowrap">
                                {{ $u->name }}
                            </td>
                            <td class="px-4 py-3.5 text-slate-600 whitespace-nowrap">
                                {{ $u->email }}
                            </td>
                            <td class="px-4 py-3.5 text-slate-600 whitespace-nowrap">
                                {{ $u->branch?->name ?? 'All Dealership Locations' }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider whitespace-nowrap
                                    {{ $u->isAdmin() ? 'bg-red-50 text-red-700 border border-red-200' : ($u->isManager() ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $u->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                    {{ $u->is_active ? 'Active' : 'Deactivated' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-500 whitespace-nowrap">
                                {{ $u->last_login_at ? $u->last_login_at->format('M d, Y h:i A') : 'Never' }}
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('users.edit', $u) }}" class="inline-flex items-center text-xs font-bold text-slate-700 hover:text-red-600 whitespace-nowrap">Edit</a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('users.toggle_active', $u) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center text-xs font-semibold whitespace-nowrap {{ $u->is_active ? 'text-rose-600 hover:underline' : 'text-emerald-600 hover:underline' }}">
                                            {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
