<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\LicensePlateRecord;
use App\Models\Motorcycle;
use App\Models\OrcrRecord;
use App\Models\ReleaseItem;
use App\Models\ReleaseTransaction;
use App\Models\StatusHistory;
use App\Services\TrackingReferenceGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReleaseManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $branchId = $request->input('branch_id');
        $user = Auth::user();

        if (!$user->isAdmin() && $user->branch_id) {
            $branchId = $user->branch_id;
        }

        $transactions = ReleaseTransaction::with([
            'branch',
            'processedByUser',
            'releaseItems.motorcycle.customer',
        ])
        ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
        ->when($search, function ($q) use ($search) {
            $q->where('transaction_code', 'like', "%{$search}%")
              ->orWhere('claimant_name', 'like', "%{$search}%")
              ->orWhere('claimant_contact', 'like', "%{$search}%")
              ->orWhereHas('releaseItems.motorcycle', function ($m) use ($search) {
                  $m->where('tracking_reference', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('engine_number', 'like', "%{$search}%");
              });
        })
        ->latest('released_at')
        ->paginate(15)
        ->withQueryString();

        $branches = Branch::where('is_active', true)->get();

        return view('releases.index', compact('transactions', 'branches', 'search', 'branchId'));
    }

    /**
     * Show claim checkout form for a specific motorcycle or search input
     */
    public function create(Request $request): View
    {
        $ref = $request->input('tracking_reference');
        $motorcycle = null;

        if ($ref) {
            $motorcycle = Motorcycle::with(['customer', 'branch', 'orcrRecord', 'licensePlateRecord'])
                ->where('tracking_reference', trim($ref))
                ->first();
        }

        return view('releases.create', compact('motorcycle', 'ref'));
    }

    /**
     * Store release transaction and update claimed statuses
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'motorcycle_id' => ['required', 'exists:motorcycles,id'],
            'claim_items' => ['required', 'array', 'min:1'],
            'claim_items.*' => ['in:orcr,license_plate'],
            'claimant_type' => ['required', 'in:owner,authorized_representative'],
            'claimant_name' => ['required', 'string', 'max:150'],
            'claimant_contact' => ['required', 'string', 'max:30'],
            'id_type_presented' => ['required', 'string', 'max:80'],
            'id_number_masked' => ['required', 'string', 'max:80'],
            'authorization_letter_checked' => ['nullable', 'boolean'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $motorcycle = Motorcycle::with(['customer', 'branch', 'orcrRecord', 'licensePlateRecord'])
            ->findOrFail($validated['motorcycle_id']);

        $selectedItems = $validated['claim_items'];

        // DOUBLE-CLAIM & READINESS VALIDATIONS:
        if (in_array('orcr', $selectedItems, true)) {
            $orcr = $motorcycle->orcrRecord;
            if (!$orcr) {
                return back()->withErrors(['error' => 'No OR/CR record exists for this motorcycle.'])->withInput();
            }
            if ($orcr->status === 'claimed') {
                return back()->withErrors(['error' => 'Double Claim Prevented: The OR/CR document has ALREADY been claimed.'])->withInput();
            }
            if ($orcr->status !== 'ready_for_pickup') {
                return back()->withErrors(['error' => "Cannot release OR/CR: Current status is '{$orcr->status_label}'. Only items verified as 'Ready for Pickup' can be released."])->withInput();
            }
        }

        if (in_array('license_plate', $selectedItems, true)) {
            $plate = $motorcycle->licensePlateRecord;
            if (!$plate) {
                return back()->withErrors(['error' => 'No license plate record exists for this motorcycle.'])->withInput();
            }
            if ($plate->status === 'claimed') {
                return back()->withErrors(['error' => 'Double Claim Prevented: The license plate has ALREADY been claimed.'])->withInput();
            }
            if ($plate->status !== 'ready_for_pickup') {
                return back()->withErrors(['error' => "Cannot release license plate: Current status is '{$plate->status_label}'. Only items verified as 'Ready for Pickup' can be released."])->withInput();
            }
        }

        if ($validated['claimant_type'] === 'authorized_representative' && empty($request->boolean('authorization_letter_checked'))) {
            return back()->withErrors([
                'authorization_letter_checked' => 'Representative Release Policy: A signed authorization letter and registered owner ID photocopy must be verified and checked.',
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            $transactionCode = TrackingReferenceGenerator::generateReleaseTransactionCode();

            $releaseTx = ReleaseTransaction::create([
                'transaction_code' => $transactionCode,
                'branch_id' => $motorcycle->branch_id,
                'processed_by_user_id' => Auth::id(),
                'claimant_type' => $validated['claimant_type'],
                'claimant_name' => $validated['claimant_name'],
                'claimant_contact' => $validated['claimant_contact'],
                'id_type_presented' => $validated['id_type_presented'],
                'id_number_masked' => $validated['id_number_masked'],
                'authorization_letter_checked' => $request->boolean('authorization_letter_checked'),
                'released_at' => now(),
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // Release OR/CR if selected
            if (in_array('orcr', $selectedItems, true)) {
                $orcr = $motorcycle->orcrRecord;
                $identifier = "OR: " . ($orcr->or_number ?: 'N/A') . " / CR: " . ($orcr->cr_number ?: 'N/A');

                ReleaseItem::create([
                    'release_transaction_id' => $releaseTx->id,
                    'motorcycle_id' => $motorcycle->id,
                    'item_type' => 'orcr',
                    'item_record_id' => $orcr->id,
                    'item_identifier' => $identifier,
                    'status_before' => $orcr->status,
                    'status_after' => 'claimed',
                ]);

                $orcr->update([
                    'status' => 'claimed',
                    'claim_date' => now(),
                    'released_by_user_id' => Auth::id(),
                ]);

                StatusHistory::create([
                    'trackable_type' => OrcrRecord::class,
                    'trackable_id' => $orcr->id,
                    'from_status' => 'ready_for_pickup',
                    'to_status' => 'claimed',
                    'changed_by_user_id' => Auth::id(),
                    'notes' => "Released to {$validated['claimant_name']} ({$validated['claimant_type']}). Trans Ref: {$transactionCode}",
                ]);
            }

            // Release Plate if selected
            if (in_array('license_plate', $selectedItems, true)) {
                $plate = $motorcycle->licensePlateRecord;
                $identifier = "Plate: " . ($plate->plate_number ?: 'Assigned') . " (MV: " . ($plate->mv_file_number ?: 'N/A') . ")";

                ReleaseItem::create([
                    'release_transaction_id' => $releaseTx->id,
                    'motorcycle_id' => $motorcycle->id,
                    'item_type' => 'license_plate',
                    'item_record_id' => $plate->id,
                    'item_identifier' => $identifier,
                    'status_before' => $plate->status,
                    'status_after' => 'claimed',
                ]);

                $plate->update([
                    'status' => 'claimed',
                    'claim_date' => now(),
                    'released_by_user_id' => Auth::id(),
                ]);

                StatusHistory::create([
                    'trackable_type' => LicensePlateRecord::class,
                    'trackable_id' => $plate->id,
                    'from_status' => 'ready_for_pickup',
                    'to_status' => 'claimed',
                    'changed_by_user_id' => Auth::id(),
                    'notes' => "Released to {$validated['claimant_name']} ({$validated['claimant_type']}). Trans Ref: {$transactionCode}",
                ]);
            }

            // Check if both items are now claimed -> mark motorcycle completed
            if ($motorcycle->isFullyClaimed()) {
                $motorcycle->update(['status' => 'completed']);
            }

            AuditLog::log('ITEMS_RELEASED', "Processed release transaction {$transactionCode} for {$motorcycle->tracking_reference}", $releaseTx);

            DB::commit();

            return redirect()->route('releases.show', $releaseTx)
                ->with('success', "Items successfully released! Transaction Code: {$transactionCode}");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Release transaction failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(ReleaseTransaction $release): View
    {
        $release->load([
            'branch',
            'processedByUser',
            'releaseItems.motorcycle.customer',
            'releaseItems.motorcycle.orcrRecord',
            'releaseItems.motorcycle.licensePlateRecord',
        ]);

        return view('releases.show', compact('release'));
    }

    /**
     * Printable release acknowledgment receipt
     */
    public function printReceipt(ReleaseTransaction $release): View
    {
        $release->load([
            'branch',
            'processedByUser',
            'releaseItems.motorcycle.customer',
        ]);

        return view('releases.print_receipt', compact('release'));
    }
}
