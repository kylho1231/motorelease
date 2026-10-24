<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Customer;
use App\Services\TrackingReferenceGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $branchId = $request->input('branch_id');
        $user = Auth::user();

        if (!$user->isAdmin() && $user->branch_id) {
            $branchId = $user->branch_id;
        }

        $customers = Customer::with(['branch', 'motorcycles'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('customer_code', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('mobile_number', 'like', "%{$search}%")
                        ->orWhere('barangay', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $branches = Branch::where('is_active', true)->get();

        return view('customers.index', compact('customers', 'branches', 'search', 'branchId'));
    }

    public function create(): View
    {
        $branches = Branch::where('is_active', true)->get();
        return view('customers.create', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'mobile_number' => ['required', 'string', 'regex:/^(\+?63|0)9[0-9]{9}$/'],
            'email' => ['nullable', 'email', 'max:100'],
            'address_line' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:100'],
            'municipality' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'notification_preference' => ['required', 'in:sms,email,both,none'],
            'privacy_consent' => ['accepted'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        // Duplicate Warning Check (same mobile number or matching first & last name)
        $potentialDuplicates = Customer::where('mobile_number', $validated['mobile_number'])
            ->orWhere(function ($q) use ($validated) {
                $q->where('first_name', $validated['first_name'])
                  ->where('last_name', $validated['last_name']);
            })
            ->exists();

        $customerCode = TrackingReferenceGenerator::generateCustomerCode();

        $customer = Customer::create(array_merge($validated, [
            'customer_code' => $customerCode,
            'is_verified' => true,
            'privacy_consent' => true,
            'created_by_user_id' => Auth::id(),
        ]));

        AuditLog::log('CUSTOMER_CREATED', "Registered customer {$customer->full_name} ({$customer->customer_code})", $customer);

        $msg = 'Customer registered successfully.';
        if ($potentialDuplicates) {
            $msg .= ' (Note: Another customer with similar details exists in the system.)';
        }

        return redirect()->route('customers.show', $customer)->with('success', $msg);
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'branch',
            'motorcycles.orcrRecord',
            'motorcycles.licensePlateRecord',
            'motorcycles.releaseItems.releaseTransaction',
            'notifications' => fn($q) => $q->latest()->take(10),
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        $branches = Branch::where('is_active', true)->get();
        return view('customers.edit', compact('customer', 'branches'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'mobile_number' => ['required', 'string', 'regex:/^(\+?63|0)9[0-9]{9}$/'],
            'email' => ['nullable', 'email', 'max:100'],
            'address_line' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:100'],
            'municipality' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'notification_preference' => ['required', 'in:sms,email,both,none'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $old = $customer->toArray();
        $customer->update($validated);

        // Update tracking PIN hash for any motorcycles if mobile changed
        if ($old['mobile_number'] !== $validated['mobile_number']) {
            $newPin = substr(preg_replace('/[^0-9]/', '', $validated['mobile_number']), -4);
            foreach ($customer->motorcycles as $bike) {
                $bike->update(['tracking_pin_hash' => \Illuminate\Support\Facades\Hash::make($newPin)]);
            }
        }

        AuditLog::log('CUSTOMER_UPDATED', "Updated customer details for {$customer->full_name}", $customer, $old, $customer->toArray());

        return redirect()->route('customers.show', $customer)->with('success', 'Customer records updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        // Only admin can delete customer
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can delete customer records.');
        }

        if ($customer->motorcycles()->count() > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete customer because there are motorcycles attached to this profile. Delete or reassign the motorcycles first.',
            ]);
        }

        $name = $customer->full_name;
        $customer->delete();

        AuditLog::log('CUSTOMER_DELETED', "Deleted customer {$name}");

        return redirect()->route('customers.index')->with('success', "Customer {$name} removed successfully.");
    }
}
