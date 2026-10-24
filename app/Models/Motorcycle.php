<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Motorcycle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'tracking_reference',
        'tracking_pin_hash',
        'manufacturer',
        'model',
        'variant',
        'color',
        'year_model',
        'engine_number',
        'chassis_number',
        'dealer_transaction_ref',
        'registration_reference',
        'purchase_date',
        'status',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'year_model' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function orcrRecord(): HasOne
    {
        return $this->hasOne(OrcrRecord::class);
    }

    public function licensePlateRecord(): HasOne
    {
        return $this->hasOne(LicensePlateRecord::class);
    }

    public function releaseItems(): HasMany
    {
        return $this->hasMany(ReleaseItem::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function statusHistories(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'trackable')->latest();
    }

    public function getMaskedEngineNumberAttribute(): string
    {
        $val = $this->engine_number;
        if (strlen($val) > 6) {
            return substr($val, 0, 3) . '****' . substr($val, -3);
        }
        return '****' . substr($val, -3);
    }

    public function getMaskedChassisNumberAttribute(): string
    {
        $val = $this->chassis_number;
        if (strlen($val) > 6) {
            return substr($val, 0, 4) . '****' . substr($val, -4);
        }
        return '****' . substr($val, -4);
    }

    public function isOrcrReady(): bool
    {
        return $this->orcrRecord && $this->orcrRecord->status === 'ready_for_pickup';
    }

    public function isPlateReady(): bool
    {
        return $this->licensePlateRecord && $this->licensePlateRecord->status === 'ready_for_pickup';
    }

    public function isOrcrClaimed(): bool
    {
        return $this->orcrRecord && $this->orcrRecord->status === 'claimed';
    }

    public function isPlateClaimed(): bool
    {
        return $this->licensePlateRecord && $this->licensePlateRecord->status === 'claimed';
    }

    public function isFullyClaimed(): bool
    {
        return $this->isOrcrClaimed() && $this->isPlateClaimed();
    }
}
