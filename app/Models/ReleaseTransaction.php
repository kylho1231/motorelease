<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReleaseTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'branch_id',
        'processed_by_user_id',
        'claimant_type',
        'claimant_name',
        'claimant_contact',
        'id_type_presented',
        'id_number_masked',
        'authorization_letter_checked',
        'released_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'released_at' => 'datetime',
            'authorization_letter_checked' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function processedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    public function releaseItems(): HasMany
    {
        return $this->hasMany(ReleaseItem::class);
    }
}
