@extends('layouts.app')

@section('title', 'Edit Staff - ' . $user->name)
@section('page-title', 'Edit Employee Account')

@section('content')
<div class="max-w-2xl space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-4">Edit Employee Information</h2>

        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Work Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="phone_number" class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="block text-xs font-semibold text-slate-700 mb-1">System Role</label>
                    <select name="role" id="role" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                        <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Dealership Staff</option>
                        <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Dealership Manager</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>System Administrator</option>
                    </select>
                </div>
                <div>
                    <label for="branch_id" class="block text-xs font-semibold text-slate-700 mb-1">Assigned Branch</label>
                    <select name="branch_id" id="branch_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                        <option value="">-- All Branches (Global) --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id', $user->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300">
                    <span class="text-xs font-bold text-slate-800">Account is Active</span>
                </label>
                <p class="text-[11px] text-slate-400 mt-1">Deactivated users are blocked from logging in or using the API.</p>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-700 mb-2">Reset Password (leave empty to keep current password)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <input type="password" name="password" placeholder="New password" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                    <div>
                        <input type="password" name="password_confirmation" placeholder="Confirm new password" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
