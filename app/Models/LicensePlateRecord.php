<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LicensePlateRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'motorcycle_id',
        'plate_number',
        'mv_file_number',
        'is_plate_received',
        'received_date',
        'received_by_user_id',
        'verified_by_user_id',
        'verified_at',
        'status',
        'storage_bin_ref',
        'hold_reason',
        'claim_date',
        'released_by_user_id',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'is_plate_received' => 'boolean',
            'received_date' => 'date',
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

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'not_yet_confirmed' => 'Not Yet Confirmed',
            'awaiting_physical_plate' => 'Awaiting Physical Plate',
            'received_for_verification' => 'Received for Verification',
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
            'awaiting_physical_plate' => 'inline-flex items-center whitespace-nowrap bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300',
            'received_for_verification' => 'inline-flex items-center whitespace-nowrap bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300',
            'ready_for_pickup' => 'inline-flex items-center whitespace-nowrap bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300',
            'on_hold' => 'inline-flex items-center whitespace-nowrap bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-300',
            'claimed' => 'inline-flex items-center whitespace-nowrap bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300',
            default => 'inline-flex items-center whitespace-nowrap bg-gray-100 text-gray-700',
        };
    }
}
