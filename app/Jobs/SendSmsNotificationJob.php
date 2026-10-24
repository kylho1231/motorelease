<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\SmsGatewayService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $notificationId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notification = Notification::find($this->notificationId);

        if (!$notification) {
            Log::warning("SendSmsNotificationJob: Notification #{$this->notificationId} not found.");
            return;
        }

        if ($notification->status === 'delivered') {
            Log::info("SendSmsNotificationJob: Notification #{$this->notificationId} already delivered.");
            return;
        }

        SmsGatewayService::send($notification);
    }
}
