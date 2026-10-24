<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class OrcrRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'motorcycle_id',
        'or_number',
        'cr_number',
        'is_or_received',
        'or_received_date',
        'is_cr_received',
        'cr_received_date',
        'received_by_user_id',
        'verified_by_user_id',
        'verified_at',
        'status',
        'storage_location_ref',
        'hold_reason',
        'claim_date',
        'released_by_user_id',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'is_or_received' => 'boolean',
            'is_cr_received' => 'boolean',
            'or_received_date' => 'date',
            'cr_received_date' => 'date',
            'verified_at' => 'datetime',
            'claim_date' => 'datetime',
        ];
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function releasedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by_user_id');
    }

    public function statusHistories(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'trackable')->latest();
    }

    /**
     * Physical verification rule:
     * Complete OR/CR package CANNOT be marked ready unless BOTH required
     * documents are physically confirmed and verified.
     */
    public function isPhysicallyComplete(): bool
    {
        return $this->is_or_received && $this->is_cr_received;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'not_yet_confirmed' => 'Not Yet Confirmed',
            'awaiting_documents' => 'Awaiting Documents',
            'partially_received' => 'Partially Received',
            'ready_for_pickup' => 'Ready for Pickup',
            'on_hold' => 'On Hold',
            'claimed' => 'Claimed',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'not_yet_confirmed' => 'inline-flex items-center whitespace-nowrap bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300',
            'awaiting_documents' => 'inline-flex items-center whitespace-nowrap bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300',
            'partially_received' => 'inline-flex items-center whitespace-nowrap bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300',
            'ready_for_pickup' => 'inline-flex items-center whitespace-nowrap bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300',
            'on_hold' => 'inline-flex items-center whitespace-nowrap bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-300',
            'claimed' => 'inline-flex items-center whitespace-nowrap bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300',
            default => 'inline-flex items-center whitespace-nowrap bg-gray-100 text-gray-700',
        };
    }
}
