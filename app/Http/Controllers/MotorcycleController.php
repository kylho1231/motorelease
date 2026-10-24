<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\LicensePlateRecord;
use App\Models\Motorcycle;
use App\Models\OrcrRecord;
use App\Services\QrCodeService;
use App\Services\TrackingReferenceGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MotorcycleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $branchId = $request->input('branch_id');
        $orcrStatus = $request->input('orcr_status');
        $plateStatus = $request->input('plate_status');
        $user = Auth::user();

        if (!$user->isAdmin() && $user->branch_id) {
            $branchId = $user->branch_id;
        }

        $motorcycles = Motorcycle::with(['customer', 'branch', 'orcrRecord', 'licensePlateRecord'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($orcrStatus, fn($q) => $q->whereHas('orcrRecord', fn($sub) => $sub->where('status', $orcrStatus)))
            ->when($plateStatus, fn($q) => $q->whereHas('licensePlateRecord', fn($sub) => $sub->where('status', $plateStatus)))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('tracking_reference', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('engine_number', 'like', "%{$search}%")
                        ->orWhere('chassis_number', 'like', "%{$search}%")
                        ->orWhere('dealer_transaction_ref', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($c) use ($search) {
                            $c->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('mobile_number', 'like', "%{$search}%");
                        })
                        ->orWhereHas('licensePlateRecord', function ($lp) use ($search) {
                            $lp->where('plate_number', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $branches = Branch::where('is_active', true)->get();

        return view('motorcycles.index', compact(
            'motorcycles',
            'branches',
            'search',
            'branchId',
            'orcrStatus',
            'plateStatus'
        ));
    }

    public function create(Request $request): View
    {
        $customerId = $request->input('customer_id');
        $selectedCustomer = $customerId ? Customer::find($customerId) : null;
        $customers = Customer::orderBy('last_name')->get();
        $branches = Branch::where('is_active', true)->get();

        return view('motorcycles.create', compact('customers', 'branches', 'selectedCustomer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'manufacturer' => ['required', 'string', 'max:60'],
            'model' => ['required', 'string', 'max:100'],
            'variant' => ['nullable', 'string', 'max:80'],
            'color' => ['required', 'string', 'max:60'],
            'year_model' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'engine_number' => ['required', 'string', 'max:80', 'unique:motorcycles,engine_number'],
            'chassis_number' => ['required', 'string', 'max:80', 'unique:motorcycles,chassis_number'],
            'dealer_transaction_ref' => ['nullable', 'string', 'max:80'],
            'registration_reference' => ['nullable', 'string', 'max:80'],
            'purchase_date' => ['required', 'date'],
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $branch = Branch::findOrFail($validated['branch_id']);

        $trackingReference = TrackingReferenceGenerator::generateMotorcycleTrackingRef($branch);
        $last4Phone = substr(preg_replace('/[^0-9]/', '', $customer->mobile_number), -4);
        $pinHash = Hash::make($last4Phone);

        DB::beginTransaction();
        try {
            $motorcycle = Motorcycle::create(array_merge($validated, [
                'tracking_reference' => $trackingReference,
                'tracking_pin_hash' => $pinHash,
                'status' => 'active',
                'created_by_user_id' => Auth::id(),
            ]));

            // Initialize independent OR/CR and Plate records
            OrcrRecord::create([
                'motorcycle_id' => $motorcycle->id,
                'status' => 'awaiting_documents',
                'remarks' => 'Registration initiated upon vehicle sale on ' . $motorcycle->purchase_date->format('M d, Y'),
            ]);

            LicensePlateRecord::create([
                'motorcycle_id' => $motorcycle->id,
                'status' => 'awaiting_physical_plate',
                'remarks' => 'Physical license plate awaiting manufacture/delivery from LTO facility',
            ]);

            AuditLog::log('MOTORCYCLE_REGISTERED', "Registered motorcycle {$motorcycle->model} (Ref: {$motorcycle->tracking_reference}) for customer {$customer->full_name}", $motorcycle);

            DB::commit();

            return redirect()->route('motorcycles.show', $motorcycle)
                ->with('success', "Motorcycle registered successfully! Tracking reference: {$trackingReference}");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to register motorcycle: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Motorcycle $motorcycle): View
    {
        $motorcycle->load([
            'customer',
            'branch',
            'orcrRecord.receivedByUser',
            'orcrRecord.verifiedByUser',
            'orcrRecord.releasedByUser',
            'orcrRecord.statusHistories.changedByUser',
            'licensePlateRecord.receivedByUser',
            'licensePlateRecord.verifiedByUser',
            'licensePlateRecord.releasedByUser',
            'licensePlateRecord.statusHistories.changedByUser',
            'releaseItems.releaseTransaction.processedByUser',
            'notifications.triggeredByUser',
        ]);

        $qrDataUri = QrCodeService::generateTrackingQrDataUri($motorcycle->tracking_reference);

        return view('motorcycles.show', compact('motorcycle', 'qrDataUri'));
    }

    public function edit(Motorcycle $motorcycle): View
    {
        $branches = Branch::where('is_active', true)->get();
        return view('motorcycles.edit', compact('motorcycle', 'branches'));
    }

    public function update(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'manufacturer' => ['required', 'string', 'max:60'],
            'model' => ['required', 'string', 'max:100'],
            'variant' => ['nullable', 'string', 'max:80'],
            'color' => ['required', 'string', 'max:60'],
            'year_model' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'engine_number' => ['required', 'string', 'max:80', Rule::unique('motorcycles')->ignore($motorcycle->id)],
            'chassis_number' => ['required', 'string', 'max:80', Rule::unique('motorcycles')->ignore($motorcycle->id)],
            'dealer_transaction_ref' => ['nullable', 'string', 'max:80'],
            'registration_reference' => ['nullable', 'string', 'max:80'],
            'purchase_date' => ['required', 'date'],
            'status' => ['required', 'in:active,completed,cancelled'],
        ]);

        $old = $motorcycle->toArray();
        $motorcycle->update($validated);

        AuditLog::log('MOTORCYCLE_UPDATED', "Updated motorcycle {$motorcycle->tracking_reference}", $motorcycle, $old, $motorcycle->toArray());

        return redirect()->route('motorcycles.show', $motorcycle)->with('success', 'Motorcycle records updated successfully.');
    }

    public function destroy(Motorcycle $motorcycle): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can delete motorcycle records.');
        }

        if ($motorcycle->releaseItems()->count() > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete this motorcycle because it has historical release transactions.',
            ]);
        }

        $ref = $motorcycle->tracking_reference;
        $motorcycle->delete();

        AuditLog::log('MOTORCYCLE_DELETED', "Deleted motorcycle {$ref}");

        return redirect()->route('motorcycles.index')->with('success', "Motorcycle record {$ref} deleted.");
    }

    /**
     * Printable tracking slip / claim stub for the customer with QR code
     */
    public function printSlip(Motorcycle $motorcycle): View
    {
        $motorcycle->load(['customer', 'branch', 'orcrRecord', 'licensePlateRecord']);
        $qrDataUri = QrCodeService::generateTrackingQrDataUri($motorcycle->tracking_reference);

        return view('motorcycles.print_slip', compact('motorcycle', 'qrDataUri'));
    }
}
