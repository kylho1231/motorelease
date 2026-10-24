<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\LicensePlateRecord;
use App\Models\Motorcycle;
use App\Models\Notification;
use App\Models\OrcrRecord;
use App\Models\ReleaseTransaction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $branchId = $request->input('branch_id');
        $dateFrom = $request->input('date_from', now()->subMonths(3)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $user = Auth::user();
        if (!$user->isAdmin() && $user->branch_id) {
            $branchId = $user->branch_id;
        }

        // Branch-scoped motorcycles
        $motorcycleIds = Motorcycle::when($branchId, fn($q) => $q->where('branch_id', $branchId))->pluck('id');

        // Status counts
        $totalRegistered = $motorcycleIds->count();

        $orcrPending = OrcrRecord::whereIn('motorcycle_id', $motorcycleIds)
            ->whereIn('status', ['not_yet_confirmed', 'awaiting_documents', 'partially_received'])
            ->count();

        $orcrReady = OrcrRecord::whereIn('motorcycle_id', $motorcycleIds)
            ->where('status', 'ready_for_pickup')
            ->count();

        $orcrClaimed = OrcrRecord::whereIn('motorcycle_id', $motorcycleIds)
            ->where('status', 'claimed')
            ->count();

        $platesPending = LicensePlateRecord::whereIn('motorcycle_id', $motorcycleIds)
            ->whereIn('status', ['not_yet_confirmed', 'awaiting_physical_plate', 'received_for_verification'])
            ->count();

        $platesReady = LicensePlateRecord::whereIn('motorcycle_id', $motorcycleIds)
            ->where('status', 'ready_for_pickup')
            ->count();

        $platesClaimed = LicensePlateRecord::whereIn('motorcycle_id', $motorcycleIds)
            ->where('status', 'claimed')
            ->count();

        // Unclaimed aging list (ready for pickup > 14 days)
        $unclaimedAgingOrcr = OrcrRecord::with(['motorcycle.customer', 'motorcycle.branch'])
            ->whereIn('motorcycle_id', $motorcycleIds)
            ->where('status', 'ready_for_pickup')
            ->where('verified_at', '<=', now()->subDays(14))
            ->get();

        $unclaimedAgingPlates = LicensePlateRecord::with(['motorcycle.customer', 'motorcycle.branch'])
            ->whereIn('motorcycle_id', $motorcycleIds)
            ->where('status', 'ready_for_pickup')
            ->where('verified_at', '<=', now()->subDays(14))
            ->get();

        // Turnaround metrics calculations
        // 1. Avg days between document received and notification sent
        $claimedOrcr = OrcrRecord::whereIn('motorcycle_id', $motorcycleIds)
            ->whereNotNull('or_received_date')
            ->whereNotNull('verified_at')
            ->get();

        $totalDaysReceiptToVerified = 0;
        $countReceiptToVerified = 0;
        foreach ($claimedOrcr as $rec) {
            $diff = $rec->or_received_date->diffInDays($rec->verified_at);
            $totalDaysReceiptToVerified += $diff;
            $countReceiptToVerified++;
        }
        $avgDaysReceiptToVerified = $countReceiptToVerified > 0 ? round($totalDaysReceiptToVerified / $countReceiptToVerified, 1) : 0;

        // 2. Avg days between verified ready and customer collection
        $collectedOrcr = OrcrRecord::whereIn('motorcycle_id', $motorcycleIds)
            ->where('status', 'claimed')
            ->whereNotNull('verified_at')
            ->whereNotNull('claim_date')
            ->get();

        $totalDaysVerifiedToClaim = 0;
        $countVerifiedToClaim = 0;
        foreach ($collectedOrcr as $rec) {
            $diff = $rec->verified_at->diffInDays($rec->claim_date);
            $totalDaysVerifiedToClaim += $diff;
            $countVerifiedToClaim++;
        }
        $avgDaysVerifiedToClaim = $countVerifiedToClaim > 0 ? round($totalDaysVerifiedToClaim / $countVerifiedToClaim, 1) : 0;

        // Branch performance breakdown
        $branchSummaries = Branch::withCount([
            'motorcycles',
            'customers',
            'releaseTransactions',
        ])->get();

        // Notification stats
        $totalSms = Notification::whereIn('motorcycle_id', $motorcycleIds)->count();
        $deliveredSms = Notification::whereIn('motorcycle_id', $motorcycleIds)->where('status', 'delivered')->count();
        $failedSms = Notification::whereIn('motorcycle_id', $motorcycleIds)->where('status', 'failed')->count();

        $branches = Branch::where('is_active', true)->get();

        return view('reports.index', compact(
            'totalRegistered',
            'orcrPending',
            'orcrReady',
            'orcrClaimed',
            'platesPending',
            'platesReady',
            'platesClaimed',
            'unclaimedAgingOrcr',
            'unclaimedAgingPlates',
            'avgDaysReceiptToVerified',
            'avgDaysVerifiedToClaim',
            'branchSummaries',
            'totalSms',
            'deliveredSms',
            'failedSms',
            'branches',
            'branchId',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Export operational records to CSV with data privacy compliance
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $type = $request->input('type', 'releases');
        $branchId = $request->input('branch_id');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"motorelease_{$type}_export_" . date('Ymd_His') . ".csv\"",
        ];

        $callback = function () use ($type, $branchId) {
            $output = fopen('php://output', 'w');

            if ($type === 'releases') {
                fputcsv($output, [
                    'Transaction Code',
                    'Branch',
                    'Released At',
                    'Claimant Type',
                    'Claimant Name',
                    'Claimant Contact',
                    'ID Presented',
                    'Masked ID Number',
                    'Motorcycle Ref',
                    'Motorcycle Model',
                    'Released Items',
                    'Processed By',
                ]);

                $releases = ReleaseTransaction::with(['branch', 'processedByUser', 'releaseItems.motorcycle'])
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->latest('released_at')
                    ->get();

                foreach ($releases as $rel) {
                    $items = $rel->releaseItems->map(fn($item) => $item->item_type_label . " ({$item->item_identifier})")->implode('; ');
                    $firstBike = $rel->releaseItems->first()?->motorcycle;

                    fputcsv($output, [
                        $rel->transaction_code,
                        $rel->branch->name,
                        $rel->released_at->format('Y-m-d H:i:s'),
                        $rel->claimant_type,
                        $rel->claimant_name,
                        $rel->claimant_contact,
                        $rel->id_type_presented,
                        $rel->id_number_masked,
                        $firstBike?->tracking_reference ?? 'N/A',
                        $firstBike?->model ?? 'N/A',
                        $items,
                        $rel->processedByUser->name,
                    ]);
                }
            } else {
                // Default: Inventory & Availability Status
                fputcsv($output, [
                    'Tracking Ref',
                    'Branch',
                    'Customer Name (Masked)',
                    'Customer Contact',
                    'Model',
                    'Purchase Date',
                    'OR/CR Status',
                    'OR/CR Ready Date',
                    'Plate Status',
                    'Plate Ready Date',
                ]);

                $bikes = Motorcycle::with(['customer', 'branch', 'orcrRecord', 'licensePlateRecord'])
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->latest()
                    ->get();

                foreach ($bikes as $bike) {
                    fputcsv($output, [
                        $bike->tracking_reference,
                        $bike->branch->name,
                        $bike->customer ? $bike->customer->masked_name : 'N/A',
                        $bike->customer ? $bike->customer->masked_mobile : 'N/A',
                        $bike->model,
                        $bike->purchase_date->format('Y-m-d'),
                        $bike->orcrRecord?->status_label ?? 'N/A',
                        $bike->orcrRecord?->verified_at?->format('Y-m-d') ?? 'N/A',
                        $bike->licensePlateRecord?->status_label ?? 'N/A',
                        $bike->licensePlateRecord?->verified_at?->format('Y-m-d') ?? 'N/A',
                    ]);
                }
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
