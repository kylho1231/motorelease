@extends('layouts.app')

@section('title', 'Customers Directory')
@section('page-title', 'Customer Records Management')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Search -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('customers.index') }}" class="w-full sm:w-auto flex-1 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[240px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search by name, customer code, mobile number, barangay..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-red-500">
            </div>

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
            @if($search || $branchId)
                <a href="{{ route('customers.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>

        <a href="{{ route('customers.create') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm transition">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Register Customer
        </a>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">Customer Code</th>
                        <th class="px-4 py-3.5">Customer Name</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Mobile Phone</th>
                        <th class="px-4 py-3.5">Municipality / Barangay</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Motorcycles</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Branch</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $c)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3.5 font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $c->customer_code }}
                            </td>
                            <td class="px-4 py-3.5 font-bold text-slate-800">
                                <a href="{{ route('customers.show', $c) }}" class="hover:text-red-600 transition">
                                    {{ $c->full_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-700 whitespace-nowrap">
                                {{ $c->mobile_number }}
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">
                                {{ $c->barangay }}, {{ $c->municipality }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 font-bold text-[10px] whitespace-nowrap">
                                    {{ $c->motorcycles->count() }} unit(s)
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-500 whitespace-nowrap">
                                {{ $c->branch->name }}
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('customers.show', $c) }}" class="inline-flex items-center text-xs font-bold text-slate-700 hover:text-red-600 whitespace-nowrap">View</a>
                                <a href="{{ route('customers.edit', $c) }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 whitespace-nowrap">Edit</a>
                                <a href="{{ route('motorcycles.create', ['customer_id' => $c->id]) }}" class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-700 whitespace-nowrap">+&nbsp;Bike</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                No customer records found matching your query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
