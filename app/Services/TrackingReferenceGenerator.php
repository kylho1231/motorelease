<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Motorcycle;
use App\Models\ReleaseTransaction;
use Illuminate\Support\Str;

class TrackingReferenceGenerator
{
    /**
     * Generate unique tracking reference: MR-SJA-YYYY-000001
     */
    public static function generateMotorcycleTrackingRef(Branch $branch): string
    {
        $year = date('Y');
        $parts = explode('-', $branch->code);
        $prefix = preg_replace('/[^A-Za-z0-9]/', '', $parts[0]);
        $branchCode = strtoupper(substr($prefix, 0, 4)) ?: 'SJA';

        do {
            $latest = Motorcycle::where('branch_id', $branch->id)
                ->whereYear('created_at', $year)
                ->count();

            $seq = str_pad($latest + 1, 6, '0', STR_PAD_LEFT);
            $ref = "MR-{$branchCode}-{$year}-{$seq}";

            // Ensure uniqueness
            $exists = Motorcycle::where('tracking_reference', $ref)->exists();
            if ($exists) {
                // If collision due to concurrent creation, append random hex
                $ref = "MR-{$branchCode}-{$year}-" . str_pad($latest + rand(2, 99), 6, '0', STR_PAD_LEFT);
            }
        } while (Motorcycle::where('tracking_reference', $ref)->exists());

        return $ref;
    }

    /**
     * Generate unique customer code: CUST-YYYY-00001
     */
    public static function generateCustomerCode(): string
    {
        $year = date('Y');
        do {
            $count = Customer::whereYear('created_at', $year)->count();
            $code = 'CUST-' . $year . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
            $exists = Customer::where('customer_code', $code)->exists();
            if ($exists) {
                $code = 'CUST-' . $year . '-' . str_pad($count + rand(2, 50), 5, '0', STR_PAD_LEFT);
            }
        } while (Customer::where('customer_code', $code)->exists());

        return $code;
    }

    /**
     * Generate unique release transaction code: REL-YYYY-00001
     */
    public static function generateReleaseTransactionCode(): string
    {
        $year = date('Y');
        do {
            $count = ReleaseTransaction::whereYear('created_at', $year)->count();
            $code = 'REL-' . $year . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
            $exists = ReleaseTransaction::where('transaction_code', $code)->exists();
            if ($exists) {
                $code = 'REL-' . $year . '-' . str_pad($count + rand(2, 50), 5, '0', STR_PAD_LEFT);
            }
        } while (ReleaseTransaction::where('transaction_code', $code)->exists());

        return $code;
    }
}
