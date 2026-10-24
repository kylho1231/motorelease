<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'customer_code',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'mobile_number',
        'email',
        'address_line',
        'barangay',
        'municipality',
        'province',
        'postal_code',
        'notification_preference',
        'is_verified',
        'privacy_consent',
        'remarks',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'privacy_consent' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function motorcycles(): HasMany
    {
        return $this->hasMany(Motorcycle::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name ? substr($this->middle_name, 0, 1) . '.' : null,
            $this->last_name,
            $this->suffix,
        ]);

        return implode(' ', $parts);
    }

    public function getMaskedMobileAttribute(): string
    {
        $num = $this->mobile_number;
        if (strlen($num) >= 10) {
            return substr($num, 0, 4) . '****' . substr($num, -3);
        }
        return '****' . substr($num, -3);
    }

    public function getMaskedNameAttribute(): string
    {
        $first = $this->first_name;
        $last = $this->last_name;
        $maskedFirst = mb_substr($first, 0, 1) . str_repeat('*', max(1, mb_strlen($first) - 1));
        $maskedLast = mb_substr($last, 0, 1) . str_repeat('*', max(1, mb_strlen($last) - 1));

        return "{$maskedFirst} {$maskedLast}";
    }
}
