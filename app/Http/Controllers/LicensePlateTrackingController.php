<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\LicensePlateRecord;
use App\Models\StatusHistory;
use App\Services\SmsGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LicensePlateTrackingController extends Controller
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

        $records = LicensePlateRecord::with([
            'motorcycle.customer',
            'motorcycle.branch',
            'verifiedByUser',
            'releasedByUser',
        ])
        ->when($status, fn($q) => $q->where('status', $status))
        ->when($branchId, fn($q) => $q->whereHas('motorcycle', fn($m) => $m->where('branch_id', $branchId)))
        ->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('plate_number', 'like', "%{$search}%")
                    ->orWhere('mv_file_number', 'like', "%{$search}%")
                    ->orWhere('storage_bin_ref', 'like', "%{$search}%")
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

        return view('plates.index', compact('records', 'branches', 'status', 'branchId', 'search'));
    }

    public function show(LicensePlateRecord $plateRecord): View
    {
        $plateRecord->load([
            'motorcycle.customer',
            'motorcycle.branch',
            'motorcycle.orcrRecord',
            'receivedByUser',
            'verifiedByUser',
            'releasedByUser',
            'statusHistories.changedByUser',
        ]);

        return view('plates.show', compact('plateRecord'));
    }

    /**
     * Record physical plate receipt at dealership
     */
    public function updateReceipt(Request $request, LicensePlateRecord $plateRecord): RedirectResponse
    {
        if ($plateRecord->status === 'claimed') {
            return back()->withErrors(['error' => 'Cannot modify receipt status of an already claimed license plate.']);
        }

        $validated = $request->validate([
            'plate_number' => ['nullable', 'string', 'max:30'],
            'mv_file_number' => ['nullable', 'string', 'max:50'],
            'is_plate_received' => ['nullable', 'boolean'],
            'received_date' => ['nullable', 'date'],
            'storage_bin_ref' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $isReceived = $request->boolean('is_plate_received');
        $oldStatus = $plateRecord->status;
        $newStatus = $oldStatus;

        if ($isReceived) {
            if ($oldStatus !== 'ready_for_pickup') {
                $newStatus = 'received_for_verification';
            }
        } else {
            $newStatus = 'awaiting_physical_plate';
        }

        $plateRecord->update([
            'plate_number' => $validated['plate_number'] ?? $plateRecord->plate_number,
            'mv_file_number' => $validated['mv_file_number'] ?? $plateRecord->mv_file_number,
            'is_plate_received' => $isReceived,
            'received_date' => $isReceived ? ($validated['received_date'] ?? now()) : null,
            'received_by_user_id' => Auth::id(),
            'storage_bin_ref' => $validated['storage_bin_ref'] ?? $plateRecord->storage_bin_ref,
            'remarks' => $validated['remarks'] ?? $plateRecord->remarks,
            'status' => $newStatus,
        ]);

        if ($oldStatus !== $newStatus) {
            StatusHistory::create([
                'trackable_type' => LicensePlateRecord::class,
                'trackable_id' => $plateRecord->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by_user_id' => Auth::id(),
                'notes' => 'Physical license plate receipt status updated to: ' . $newStatus,
            ]);
        }

        AuditLog::log('PLATE_RECEIPT_UPDATED', "Updated physical plate receipt for motorcycle #{$plateRecord->motorcycle_id}", $plateRecord);

        return back()->with('success', 'Physical license plate details updated successfully.');
    }

    /**
     * Verify plate and mark ready for pickup
     */
    public function verifyReady(Request $request, LicensePlateRecord $plateRecord): RedirectResponse
    {
        if ($plateRecord->status === 'claimed') {
            return back()->withErrors(['error' => 'This license plate is already claimed.']);
        }

        if (!$plateRecord->is_plate_received) {
            return back()->withErrors([
                'error' => 'Verification Rejected: Physical license plate must be marked as received and inspected before marking Ready for Pickup.',
            ]);
        }

        $request->validate([
            'plate_number' => ['nullable', 'string', 'max:30'],
            'storage_bin_ref' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $plateRecord->status;

            $plateRecord->update([
                'plate_number' => $request->input('plate_number') ?: $plateRecord->plate_number,
                'storage_bin_ref' => $request->input('storage_bin_ref') ?: $plateRecord->storage_bin_ref,
                'remarks' => $request->input('remarks') ?: $plateRecord->remarks,
                'status' => 'ready_for_pickup',
                'verified_by_user_id' => Auth::id(),
                'verified_at' => now(),
                'hold_reason' => null,
            ]);

            StatusHistory::create([
                'trackable_type' => LicensePlateRecord::class,
                'trackable_id' => $plateRecord->id,
                'from_status' => $oldStatus,
                'to_status' => 'ready_for_pickup',
                'changed_by_user_id' => Auth::id(),
                'notes' => 'Physical license plate verified and approved for pickup.',
            ]);

            // AUTOMATED SMS TRIGGER:
            $notif = SmsGatewayService::queueNotification(
                $plateRecord->motorcycle,
                'plate_ready',
                Auth::id()
            );

            AuditLog::log('PLATE_VERIFIED_READY', "Verified license plate as READY FOR PICKUP for motorcycle {$plateRecord->motorcycle->tracking_reference}", $plateRecord);

            DB::commit();

            $msg = 'License plate verified and marked READY FOR PICKUP!';
            if ($notif) {
                $msg .= ' Automated customer SMS notification queued.';
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to verify license plate: ' . $e->getMessage()]);
        }
    }

    /**
     * Put license plate on hold
     */
    public function putOnHold(Request $request, LicensePlateRecord $plateRecord): RedirectResponse
    {
        $validated = $request->validate([
            'hold_reason' => ['required', 'string', 'max:500'],
        ]);

        $oldStatus = $plateRecord->status;
        $plateRecord->update([
            'status' => 'on_hold',
            'hold_reason' => $validated['hold_reason'],
        ]);

        StatusHistory::create([
            'trackable_type' => LicensePlateRecord::class,
            'trackable_id' => $plateRecord->id,
            'from_status' => $oldStatus,
            'to_status' => 'on_hold',
            'changed_by_user_id' => Auth::id(),
            'notes' => 'Put on hold: ' . $validated['hold_reason'],
        ]);

        AuditLog::log('PLATE_PUT_ON_HOLD', "License plate put on hold for motorcycle {$plateRecord->motorcycle->tracking_reference}: {$validated['hold_reason']}", $plateRecord);

        return back()->with('warning', 'License plate status changed to ON HOLD.');
    }
}
