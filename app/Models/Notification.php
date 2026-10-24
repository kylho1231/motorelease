<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'motorcycle_id',
        'customer_id',
        'triggered_by_user_id',
        'event_type',
        'recipient_number',
        'message_body',
        'status',
        'provider',
        'provider_message_id',
        'retry_count',
        'error_message',
        'idempotency_key',
        'sent_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'retry_count' => 'integer',
        ];
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function triggeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_user_id');
    }

    public function getEventLabelAttribute(): string
    {
        return match ($this->event_type) {
            'orcr_ready' => 'OR/CR Ready Notification',
            'plate_ready' => 'License Plate Ready Notification',
            'combined_ready' => 'OR/CR & Plate Ready Notification',
            'pickup_reminder' => 'Claim Reminder Notice',
            'status_hold' => 'Document Hold Notice',
            'manual_notice' => 'Custom Customer Notice',
            default => ucfirst(str_replace('_', ' ', $this->event_type)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'queued' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300',
            'sent' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300',
            'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300',
            'failed' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-300',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
