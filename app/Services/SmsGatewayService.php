<?php

namespace App\Services;

use App\Jobs\SendSmsNotificationJob;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Motorcycle;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsGatewayService
{
    /**
     * Normalize Philippine mobile phone number to standard 09XXXXXXXXX or 639XXXXXXXXX
     */
    public static function normalizePhilippineNumber(string $phone, bool $international = false): ?string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // e.g. 639171234567 -> 09171234567
        if (str_starts_with($clean, '63') && strlen($clean) === 12) {
            return $international ? $clean : ('0' . substr($clean, 2));
        }

        // e.g. 9171234567 -> 09171234567
        if (str_starts_with($clean, '9') && strlen($clean) === 10) {
            return $international ? ('63' . $clean) : ('0' . $clean);
        }

        // e.g. 09171234567
        if (str_starts_with($clean, '09') && strlen($clean) === 11) {
            return $international ? ('63' . substr($clean, 1)) : $clean;
        }

        return null;
    }

    /**
     * Validate if a string is a valid Philippine mobile number
     */
    public static function isValidPhilippineNumber(string $phone): bool
    {
        return self::normalizePhilippineNumber($phone) !== null;
    }

    /**
     * Dispatch notification with idempotency protection
     */
    public static function queueNotification(
        Motorcycle $motorcycle,
        string $eventType,
        ?int $triggeredByUserId = null,
        bool $force = false
    ): ?Notification {
        $customer = $motorcycle->customer;
        if (!$customer || $customer->notification_preference === 'none') {
            Log::info("Notification skipped for motorcycle #{$motorcycle->id}: Customer opted out or missing.");
            return null;
        }

        $normalizedPhone = self::normalizePhilippineNumber($customer->mobile_number);
        if (!$normalizedPhone) {
            Log::warning("Notification failed for customer #{$customer->id}: Invalid Philippine phone number: {$customer->mobile_number}");
            return null;
        }

        // Generate idempotency key based on motorcycle, event, and current status verification date/hour
        $eventToken = match ($eventType) {
            'orcr_ready' => $motorcycle->orcrRecord?->verified_at?->format('Y-m-d_H') ?? date('Y-m-d_H'),
            'plate_ready' => $motorcycle->licensePlateRecord?->verified_at?->format('Y-m-d_H') ?? date('Y-m-d_H'),
            'combined_ready' => date('Y-m-d_H'),
            'pickup_reminder' => date('Y-m-d'),
            default => date('Y-m-d_H-i-s'),
        };

        $idempotencyKey = hash('sha256', "NOTIF_{$motorcycle->id}_{$eventType}_{$eventToken}");

        if (!$force && Notification::where('idempotency_key', $idempotencyKey)->exists()) {
            Log::info("Duplicate notification suppressed by idempotency key: {$idempotencyKey}");
            return Notification::where('idempotency_key', $idempotencyKey)->first();
        }

        $branch = $motorcycle->branch;
        $trackingUrl = route('customer.track', ['ref' => $motorcycle->tracking_reference]);

        // Build default message body or fetch from notification_templates
        $template = NotificationTemplate::where('code', strtoupper($eventType))->where('is_active', true)->first();
        
        $placeholders = [
            'customer_name' => $customer->first_name,
            'model' => $motorcycle->model,
            'branch_name' => $branch->name,
            'branch_address' => $branch->address_line,
            'branch_phone' => $branch->contact_number,
            'tracking_ref' => $motorcycle->tracking_reference,
            'tracking_url' => $trackingUrl,
            'plate_number' => $motorcycle->licensePlateRecord?->plate_number ?? 'Assigned Plate',
            'or_number' => $motorcycle->orcrRecord?->or_number ?? 'N/A',
            'cr_number' => $motorcycle->orcrRecord?->cr_number ?? 'N/A',
        ];

        if ($template) {
            $messageBody = $template->render($placeholders);
        } else {
            $messageBody = self::getDefaultMessage($eventType, $placeholders);
        }

        $notification = Notification::create([
            'motorcycle_id' => $motorcycle->id,
            'customer_id' => $customer->id,
            'triggered_by_user_id' => $triggeredByUserId,
            'event_type' => $eventType,
            'recipient_number' => $normalizedPhone,
            'message_body' => $messageBody,
            'status' => 'queued',
            'provider' => config('services.sms.driver', env('SMS_DRIVER', 'mock')),
            'idempotency_key' => $force ? ($idempotencyKey . '_' . time()) : $idempotencyKey,
            'retry_count' => 0,
        ]);

        // Dispatch background queue job (will not block staff UI)
        SendSmsNotificationJob::dispatch($notification->id);

        AuditLog::log('SMS_QUEUED', "Queued SMS for {$customer->full_name} ({$normalizedPhone}) event {$eventType}", $notification);

        return $notification;
    }

    /**
     * Send notification synchronously or within a queue worker
     */
    public static function send(Notification $notification): bool
    {
        $driver = config('services.sms.driver', env('SMS_DRIVER', 'mock'));

        try {
            if ($driver === 'semaphore' && !empty(env('SEMAPHORE_API_KEY'))) {
                return self::sendViaSemaphore($notification);
            }

            // Fallback / default: Mock SMS Gateway
            return self::sendViaMockGateway($notification);
        } catch (\Throwable $e) {
            $notification->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'retry_count' => $notification->retry_count + 1,
            ]);

            Log::error("SMS Sending failed for notification #{$notification->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via Semaphore SMS API
     */
    protected static function sendViaSemaphore(Notification $notification): bool
    {
        $apiKey = env('SEMAPHORE_API_KEY');
        $senderName = env('SEMAPHORE_SENDER_NAME', 'MOTORELEASE');
        $recipientIntl = self::normalizePhilippineNumber($notification->recipient_number, true);

        $response = Http::timeout(10)->post('https://api.semaphore.co/api/v4/messages', [
            'apikey' => $apiKey,
            'number' => $recipientIntl,
            'message' => $notification->message_body,
            'sendername' => $senderName,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $messageId = is_array($data) && isset($data[0]['message_id']) ? (string) $data[0]['message_id'] : 'SEMAPHORE-OK';

            $notification->update([
                'status' => 'delivered',
                'provider' => 'semaphore',
                'provider_message_id' => $messageId,
                'sent_at' => now(),
                'delivered_at' => now(),
                'error_message' => null,
            ]);

            Log::info("SMS delivered via Semaphore to {$notification->recipient_number}. Message ID: {$messageId}");
            return true;
        }

        $notification->update([
            'status' => 'failed',
            'error_message' => 'Semaphore API Error: ' . $response->body(),
            'retry_count' => $notification->retry_count + 1,
        ]);

        return false;
    }

    /**
     * Send via Mock SMS Gateway (for local development and safe testing)
     */
    protected static function sendViaMockGateway(Notification $notification): bool
    {
        $mockId = 'MOCK-' . strtoupper(bin2hex(random_bytes(6)));

        // Log to dedicated sms channel / storage
        Log::channel('single')->info("=== [MOCK SMS GATEWAY SIMULATION] ===");
        Log::channel('single')->info("To: {$notification->recipient_number}");
        Log::channel('single')->info("Message:\n{$notification->message_body}");
        Log::channel('single')->info("Mock ID: {$mockId}");
        Log::channel('single')->info("======================================");

        $notification->update([
            'status' => 'delivered',
            'provider' => 'mock_gateway',
            'provider_message_id' => $mockId,
            'sent_at' => now(),
            'delivered_at' => now(),
            'error_message' => null,
        ]);

        return true;
    }

    /**
     * Default fallbacks if database templates are empty
     */
    protected static function getDefaultMessage(string $eventType, array $p): string
    {
        return match ($eventType) {
            'orcr_ready' => "Good day {$p['customer_name']}! Your motorcycle OR/CR for {$p['model']} is verified and READY FOR PICKUP at {$p['branch_name']}. Please bring 2 valid IDs. Tracking Ref: {$p['tracking_ref']}. Check status: {$p['tracking_url']}",
            'plate_ready' => "Good day {$p['customer_name']}! Your motorcycle license plate ({$p['plate_number']}) is now AVAILABLE for collection at {$p['branch_name']}. Please present valid ID. Ref: {$p['tracking_ref']}. Inquiries: {$p['branch_phone']}",
            'combined_ready' => "Good day {$p['customer_name']}! Both your OR/CR and physical license plate for {$p['model']} are READY FOR COLLECTION at {$p['branch_name']}. Ref: {$p['tracking_ref']}. Visit: {$p['tracking_url']}",
            'pickup_reminder' => "Reminder: Your motorcycle documents/plate for {$p['model']} remain ready for claim at {$p['branch_name']}. Please collect at your earliest convenience. Ref: {$p['tracking_ref']}",
            'status_hold' => "Notice: Document release for {$p['model']} is temporarily on hold for verification at {$p['branch_name']}. Our staff will assist you. Inquiries: {$p['branch_phone']}. Ref: {$p['tracking_ref']}",
            default => "Good day from {$p['branch_name']}. Status update for your motorcycle {$p['model']}: Ref {$p['tracking_ref']}. Track online: {$p['tracking_url']}",
        };
    }
}
