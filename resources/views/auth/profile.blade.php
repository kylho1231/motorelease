@extends('layouts.app')

@section('title', 'My Account Profile')
@section('page-title', 'My Account Profile')

@section('content')
<div class="max-w-3xl space-y-6">

    <!-- Profile Overview Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
            <i data-lucide="user-check" class="w-5 h-5 text-red-600"></i>
            Account Details
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-slate-500 font-medium">Employee Name</p>
                <p class="font-bold text-slate-800">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Work Email</p>
                <p class="font-semibold text-slate-800">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">System Role</p>
                <span class="inline-block mt-0.5 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                    {{ $user->isAdmin() ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                    {{ $user->role }}
                </span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Assigned Branch</p>
                <p class="font-semibold text-slate-800">{{ $user->branch?->name ?? 'All Dealership Locations' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Last Login</p>
                <p class="text-slate-700">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'First session' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Login IP Address</p>
                <p class="text-slate-700">{{ $user->last_login_ip ?? '127.0.0.1' }}</p>
            </div>
        </div>
    </div>

    <!-- Change Password Form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
            <i data-lucide="key-round" class="w-5 h-5 text-red-600"></i>
            Change Account Password
        </h2>

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1">Current Password</label>
                <input type="password" id="current_password" name="current_password" required
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">New Password (min 8 chars)</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
