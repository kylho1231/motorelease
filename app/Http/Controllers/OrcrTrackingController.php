<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Motorcycle;
use App\Models\OrcrRecord;
use App\Models\StatusHistory;
use App\Services\SmsGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrcrTrackingController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $branchId = $request->input('branch_id');
        $search = $request->input('search');
        $user = Auth::user();

        if (!$user->isAdmin() && $user->branch_id) {
            $branchId = $user->branch_id;
        }

        $records = OrcrRecord::with([
            'motorcycle.customer',
            'motorcycle.branch',
            'verifiedByUser',
            'releasedByUser',
        ])
        ->when($status, fn($q) => $q->where('status', $status))
        ->when($branchId, fn($q) => $q->whereHas('motorcycle', fn($m) => $m->where('branch_id', $branchId)))
        ->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('or_number', 'like', "%{$search}%")
                    ->orWhere('cr_number', 'like', "%{$search}%")
                    ->orWhere('storage_location_ref', 'like', "%{$search}%")
                    ->orWhereHas('motorcycle', function ($m) use ($search) {
                        $m->where('tracking_reference', 'like', "%{$search}%")
                          ->orWhere('model', 'like', "%{$search}%")
                          ->orWhere('engine_number', 'like', "%{$search}%")
                          ->orWhere('chassis_number', 'like', "%{$search}%")
                          ->orWhereHas('customer', function ($c) use ($search) {
                              $c->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('mobile_number', 'like', "%{$search}%");
                          });
                    });
            });
        })
        ->latest('updated_at')
        ->paginate(15)
        ->withQueryString();

        $branches = Branch::where('is_active', true)->get();

        return view('orcr.index', compact('records', 'branches', 'status', 'branchId', 'search'));
    }

    public function show(OrcrRecord $orcrRecord): View
    {
        $orcrRecord->load([
            'motorcycle.customer',
            'motorcycle.branch',
            'motorcycle.licensePlateRecord',
            'receivedByUser',
            'verifiedByUser',
            'releasedByUser',
            'statusHistories.changedByUser',
        ]);

        return view('orcr.show', compact('orcrRecord'));
    }

    /**
     * Record physical receipt of OR and/or CR documents
     */
    public function updateReceipt(Request $request, OrcrRecord $orcrRecord): RedirectResponse
    {
        if ($orcrRecord->status === 'claimed') {
            return back()->withErrors(['error' => 'Cannot modify receipt status of an already claimed OR/CR record.']);
        }

        $validated = $request->validate([
            'or_number' => ['nullable', 'string', 'max:50'],
            'is_or_received' => ['nullable', 'boolean'],
            'or_received_date' => ['nullable', 'date'],
            'cr_number' => ['nullable', 'string', 'max:50'],
            'is_cr_received' => ['nullable', 'boolean'],
            'cr_received_date' => ['nullable', 'date'],
            'storage_location_ref' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $isOr = $request->boolean('is_or_received');
        $isCr = $request->boolean('is_cr_received');

        // Determine derived status
        $oldStatus = $orcrRecord->status;
        $newStatus = $oldStatus;

        if ($isOr && $isCr) {
            // Both are physically received. If not already verified/ready, mark as received
            if ($oldStatus !== 'ready_for_pickup') {
                $newStatus = 'partially_received'; // Will require explicit verification click to become ready
            }
        } elseif ($isOr || $isCr) {
            $newStatus = 'partially_received';
        } else {
            $newStatus = 'awaiting_documents';
        }

        $orcrRecord->update([
            'or_number' => $validated['or_number'] ?? $orcrRecord->or_number,
            'is_or_received' => $isOr,
            'or_received_date' => $isOr ? ($validated['or_received_date'] ?? now()) : null,
            'cr_number' => $validated['cr_number'] ?? $orcrRecord->cr_number,
            'is_cr_received' => $isCr,
            'cr_received_date' => $isCr ? ($validated['cr_received_date'] ?? now()) : null,
            'received_by_user_id' => Auth::id(),
            'storage_location_ref' => $validated['storage_location_ref'] ?? $orcrRecord->storage_location_ref,
            'remarks' => $validated['remarks'] ?? $orcrRecord->remarks,
            'status' => $newStatus,
        ]);

        if ($oldStatus !== $newStatus) {
            StatusHistory::create([
                'trackable_type' => OrcrRecord::class,
                'trackable_id' => $orcrRecord->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by_user_id' => Auth::id(),
                'notes' => 'Updated physical document receipt status. OR: ' . ($isOr ? 'Yes' : 'No') . ', CR: ' . ($isCr ? 'Yes' : 'No'),
            ]);
        }

        AuditLog::log('ORCR_RECEIPT_UPDATED', "Updated OR/CR document receipt for motorcycle #{$orcrRecord->motorcycle_id}", $orcrRecord);

        return back()->with('success', 'Physical document receipt details updated successfully.');
    }

    /**
     * Physical verification & quality gate:
     * Enforces the rule that both OR and CR must be physically present before marking ready.
     */
    public function verifyReady(Request $request, OrcrRecord $orcrRecord): RedirectResponse
    {
        if ($orcrRecord->status === 'claimed') {
            return back()->withErrors(['error' => 'This item is already claimed.']);
        }

        // STRICT DOMAIN RULE:
        if (!$orcrRecord->is_or_received || !$orcrRecord->is_cr_received) {
            return back()->withErrors([
                'error' => 'Verification Rejected: Complete OR/CR package CANNOT be marked ready unless BOTH Official Receipt (OR) and Certificate of Registration (CR) are physically received and checked.',
            ]);
        }

        $request->validate([
            'storage_location_ref' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $orcrRecord->status;

            $orcrRecord->update([
                'status' => 'ready_for_pickup',
                'verified_by_user_id' => Auth::id(),
                'verified_at' => now(),
                'hold_reason' => null,
                'storage_location_ref' => $request->input('storage_location_ref') ?: $orcrRecord->storage_location_ref,
                'remarks' => $request->input('remarks') ?: $orcrRecord->remarks,
            ]);

            StatusHistory::create([
                'trackable_type' => OrcrRecord::class,
                'trackable_id' => $orcrRecord->id,
                'from_status' => $oldStatus,
                'to_status' => 'ready_for_pickup',
                'changed_by_user_id' => Auth::id(),
                'notes' => 'Both physical documents verified and approved for pickup. Triggered automated customer notification.',
            ]);

            // AUTOMATED SMS TRIGGER:
            // Dispatches automated SMS notification safely in background queue
            $notif = SmsGatewayService::queueNotification(
                $orcrRecord->motorcycle,
                'orcr_ready',
                Auth::id()
            );

            AuditLog::log('ORCR_VERIFIED_READY', "Verified OR/CR as READY FOR PICKUP for motorcycle {$orcrRecord->motorcycle->tracking_reference}", $orcrRecord);

            DB::commit();

            $msg = 'OR/CR package physically verified and marked READY FOR PICKUP!';
            if ($notif) {
                $msg .= ' Automated customer SMS notification queued.';
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to verify OR/CR: ' . $e->getMessage()]);
        }
    }

    /**
     * Put OR/CR on hold
     */
    public function putOnHold(Request $request, OrcrRecord $orcrRecord): RedirectResponse
    {
        $validated = $request->validate([
            'hold_reason' => ['required', 'string', 'max:500'],
        ]);

        $oldStatus = $orcrRecord->status;
        $orcrRecord->update([
            'status' => 'on_hold',
            'hold_reason' => $validated['hold_reason'],
        ]);

        StatusHistory::create([
            'trackable_type' => OrcrRecord::class,
            'trackable_id' => $orcrRecord->id,
            'from_status' => $oldStatus,
            'to_status' => 'on_hold',
            'changed_by_user_id' => Auth::id(),
            'notes' => 'Put on hold: ' . $validated['hold_reason'],
        ]);

        AuditLog::log('ORCR_PUT_ON_HOLD', "OR/CR put on hold for motorcycle {$orcrRecord->motorcycle->tracking_reference}: {$validated['hold_reason']}", $orcrRecord);

        return back()->with('warning', 'OR/CR status changed to ON HOLD.');
    }
}
