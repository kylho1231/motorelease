<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsNotificationJob;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Notification;
use App\Services\SmsGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $eventType = $request->input('event_type');
        $search = $request->input('search');
        $branchId = $request->input('branch_id');
        $user = Auth::user();

        if (!$user->isAdmin() && $user->branch_id) {
            $branchId = $user->branch_id;
        }

        $notifications = Notification::with(['motorcycle.customer', 'motorcycle.branch', 'triggeredByUser'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($eventType, fn($q) => $q->where('event_type', $eventType))
            ->when($branchId, fn($q) => $q->whereHas('motorcycle', fn($m) => $m->where('branch_id', $branchId)))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('recipient_number', 'like', "%{$search}%")
                        ->orWhere('message_body', 'like', "%{$search}%")
                        ->orWhere('provider_message_id', 'like', "%{$search}%")
                        ->orWhereHas('motorcycle', function ($m) use ($search) {
                            $m->where('tracking_reference', 'like', "%{$search}%")
                              ->orWhere('model', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $branches = Branch::where('is_active', true)->get();

        return view('notifications.index', compact(
            'notifications',
            'branches',
            'status',
            'eventType',
            'search',
            'branchId'
        ));
    }

    public function show(Notification $notification): View
    {
        $notification->load(['motorcycle.customer', 'motorcycle.branch', 'triggeredByUser']);
        return view('notifications.show', compact('notification'));
    }

    /**
     * Resend an existing SMS notification manually
     */
    public function resend(Notification $notification): RedirectResponse
    {
        $notification->update([
            'status' => 'queued',
            'retry_count' => $notification->retry_count + 1,
            'error_message' => null,
            'sent_at' => null,
            'delivered_at' => null,
        ]);

        SendSmsNotificationJob::dispatch($notification->id);

        AuditLog::log('SMS_RESENT', "Staff manually triggered SMS resend for notification #{$notification->id} to {$notification->recipient_number}", $notification);

        return back()->with('success', "SMS dispatch has been re-queued for {$notification->recipient_number}.");
    }
}
