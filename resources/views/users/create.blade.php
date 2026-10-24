@extends('layouts.app')

@section('title', 'Add Employee Account')
@section('page-title', 'Create Dealership Staff Account')

@section('content')
<div class="max-w-2xl space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-4">Employee Account Details</h2>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Employee Full Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Juanita Reyes"
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Work Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="juanita@motorelease.ph"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="phone_number" class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                           placeholder="09171234567"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="block text-xs font-semibold text-slate-700 mb-1">System Role <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Dealership Staff</option>
                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Dealership Manager</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>System Administrator</option>
                    </select>
                </div>
                <div>
                    <label for="branch_id" class="block text-xs font-semibold text-slate-700 mb-1">Assigned Branch</label>
                    <select name="branch_id" id="branch_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                        <option value="">-- All Branches (Global) --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" required minlength="8"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm transition">
                    Create Staff Account
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
