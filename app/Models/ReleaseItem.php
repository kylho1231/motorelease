<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReleaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'release_transaction_id',
        'motorcycle_id',
        'item_type',
        'item_record_id',
        'item_identifier',
        'status_before',
        'status_after',
    ];

    public function releaseTransaction(): BelongsTo
    {
        return $this->belongsTo(ReleaseTransaction::class);
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function getItemTypeLabelAttribute(): string
    {
        return match ($this->item_type) {
            'orcr' => 'Official Receipt & Certificate of Registration (OR/CR)',
            'license_plate' => 'Physical Motorcycle License Plate',
            default => strtoupper($this->item_type),
        };
    }
}
