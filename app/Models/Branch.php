<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'dealership_brand',
        'address_line',
        'municipality',
        'province',
        'postal_code',
        'contact_number',
        'email',
        'operating_hours',
        'pickup_instructions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function motorcycles(): HasMany
    {
        return $this->hasMany(Motorcycle::class);
    }

    public function releaseTransactions(): HasMany
    {
        return $this->hasMany(ReleaseTransaction::class);
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->address_line}, {$this->municipality}, {$this->province} {$this->postal_code}";
    }
}
