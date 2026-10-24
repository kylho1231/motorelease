<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\LicensePlateRecord;
use App\Models\Motorcycle;
use App\Models\Notification;
use App\Models\OrcrRecord;
use App\Models\ReleaseTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $selectedBranchId = $request->input('branch_id');

        // Staff & Manager default to their assigned branch if set
        if (!$user->isAdmin() && $user->branch_id) {
            $selectedBranchId = $user->branch_id;
        }

        // Branch-scoped base queries
        $motorcycleQuery = Motorcycle::query();
        $customerQuery = Customer::query();
        $releaseTxQuery = ReleaseTransaction::with(['processedByUser', 'releaseItems.motorcycle.customer']);

        if ($selectedBranchId) {
            $motorcycleQuery->where('branch_id', $selectedBranchId);
            $customerQuery->where('branch_id', $selectedBranchId);
            $releaseTxQuery->where('branch_id', $selectedBranchId);
        }

        $branchMotorcycleIds = $motorcycleQuery->pluck('id');

        // Dynamic Calculations from Database
        $totalCustomers = $customerQuery->count();
        $totalMotorcycles = $motorcycleQuery->count();

        $orcrPending = OrcrRecord::whereIn('motorcycle_id', $branchMotorcycleIds)
            ->whereIn('status', ['not_yet_confirmed', 'awaiting_documents', 'partially_received'])
            ->count();

        $orcrReady = OrcrRecord::whereIn('motorcycle_id', $branchMotorcycleIds)
            ->where('status', 'ready_for_pickup')
            ->count();

        $orcrClaimed = OrcrRecord::whereIn('motorcycle_id', $branchMotorcycleIds)
            ->where('status', 'claimed')
            ->count();

        $orcrOnHold = OrcrRecord::whereIn('motorcycle_id', $branchMotorcycleIds)
            ->where('status', 'on_hold')
            ->count();

        $platesPending = LicensePlateRecord::whereIn('motorcycle_id', $branchMotorcycleIds)
            ->whereIn('status', ['not_yet_confirmed', 'awaiting_physical_plate', 'received_for_verification'])
            ->count();

        $platesReady = LicensePlateRecord::whereIn('motorcycle_id', $branchMotorcycleIds)
            ->where('status', 'ready_for_pickup')
            ->count();

        $platesClaimed = LicensePlateRecord::whereIn('motorcycle_id', $branchMotorcycleIds)
            ->where('status', 'claimed')
            ->count();

        $platesOnHold = LicensePlateRecord::whereIn('motorcycle_id', $branchMotorcycleIds)
            ->where('status', 'on_hold')
            ->count();

        // Unclaimed items ready for pickup for more than 14 days
        $unclaimedOrcrOverdue = OrcrRecord::whereIn('motorcycle_id', $branchMotorcycleIds)
            ->where('status', 'ready_for_pickup')
            ->where('verified_at', '<=', now()->subDays(14))
            ->count();

        $unclaimedPlatesOverdue = LicensePlateRecord::whereIn('motorcycle_id', $branchMotorcycleIds)
            ->where('status', 'ready_for_pickup')
            ->where('verified_at', '<=', now()->subDays(14))
            ->count();

        $totalUnclaimedOverdue = $unclaimedOrcrOverdue + $unclaimedPlatesOverdue;

        // Notification failures
        $failedNotifications = Notification::whereIn('motorcycle_id', $branchMotorcycleIds)
            ->where('status', 'failed')
            ->count();

        // Recent releases (last 5)
        $recentReleases = $releaseTxQuery->latest('released_at')->take(5)->get();

        // Recent registrations (last 5)
        $recentMotorcycles = Motorcycle::with(['customer', 'branch', 'orcrRecord', 'licensePlateRecord'])
            ->when($selectedBranchId, fn($q) => $q->where('branch_id', $selectedBranchId))
            ->latest()
            ->take(5)
            ->get();

        // Ready items requiring immediate release or follow-up
        $itemsReadyList = Motorcycle::with(['customer', 'orcrRecord', 'licensePlateRecord', 'branch'])
            ->when($selectedBranchId, fn($q) => $q->where('branch_id', $selectedBranchId))
            ->where(function ($q) {
                $q->whereHas('orcrRecord', fn($sub) => $sub->where('status', 'ready_for_pickup'))
                  ->orWhereHas('licensePlateRecord', fn($sub) => $sub->where('status', 'ready_for_pickup'));
            })
            ->latest('updated_at')
            ->take(6)
            ->get();

        // Recent system audit logs (for admin/manager)
        $recentAuditLogs = AuditLog::with('user')->latest()->take(6)->get();

        $branches = Branch::where('is_active', true)->get();

        return view('dashboard.index', compact(
            'totalCustomers',
            'totalMotorcycles',
            'orcrPending',
            'orcrReady',
            'orcrClaimed',
            'orcrOnHold',
            'platesPending',
            'platesReady',
            'platesClaimed',
            'platesOnHold',
            'totalUnclaimedOverdue',
            'failedNotifications',
            'recentReleases',
            'recentMotorcycles',
            'itemsReadyList',
            'recentAuditLogs',
            'branches',
            'selectedBranchId'
        ));
    }
}
