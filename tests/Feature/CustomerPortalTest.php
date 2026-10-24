<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\LicensePlateRecord;
use App\Models\Motorcycle;
use App\Models\OrcrRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    private Motorcycle $bikeA;
    private Motorcycle $bikeB;

    protected function setUp(): void
    {
        parent::setUp();

        $branch = Branch::create([
            'code' => 'SJA-01',
            'name' => 'San Jose Main Dealership',
            'address_line' => 'Trade Town',
            'municipality' => 'San Jose de Buenavista',
            'province' => 'Antique',
            'postal_code' => '5700',
            'contact_number' => '09171234567',
        ]);

        $customerA = Customer::create([
            'branch_id' => $branch->id,
            'customer_code' => 'CUST-A',
            'first_name' => 'Arman',
            'last_name' => 'Dela Cruz',
            'mobile_number' => '09171112233', // last 4 = 2233
            'address_line' => 'Zone 1',
            'barangay' => 'San Pedro',
            'municipality' => 'San Jose de Buenavista',
            'province' => 'Antique',
            'postal_code' => '5700',
            'privacy_consent' => true,
        ]);

        $this->bikeA = Motorcycle::create([
            'customer_id' => $customerA->id,
            'branch_id' => $branch->id,
            'tracking_reference' => 'MR-SJA-2026-AAAAAA',
            'tracking_pin_hash' => Hash::make('2233'),
            'manufacturer' => 'Yamaha',
            'model' => 'NMAX 155 ABS',
            'color' => 'Blue',
            'year_model' => 2026,
            'engine_number' => 'G3M-SECRET-A',
            'chassis_number' => 'MH3-SECRET-A',
            'purchase_date' => '2026-03-01',
        ]);

        OrcrRecord::create([
            'motorcycle_id' => $this->bikeA->id,
            'status' => 'ready_for_pickup',
            'is_or_received' => true,
            'is_cr_received' => true,
            'verified_at' => now(),
        ]);

        LicensePlateRecord::create([
            'motorcycle_id' => $this->bikeA->id,
            'status' => 'awaiting_physical_plate',
            'is_plate_received' => false,
        ]);

        $customerB = Customer::create([
            'branch_id' => $branch->id,
            'customer_code' => 'CUST-B',
            'first_name' => 'Bernardo',
            'last_name' => 'Santos',
            'mobile_number' => '09184445566', // last 4 = 5566
            'address_line' => 'Zone 2',
            'barangay' => 'Mojon',
            'municipality' => 'San Jose de Buenavista',
            'province' => 'Antique',
            'postal_code' => '5700',
            'privacy_consent' => true,
        ]);

        $this->bikeB = Motorcycle::create([
            'customer_id' => $customerB->id,
            'branch_id' => $branch->id,
            'tracking_reference' => 'MR-SJA-2026-BBBBBB',
            'tracking_pin_hash' => Hash::make('5566'),
            'manufacturer' => 'Yamaha',
            'model' => 'Mio Aerox 155',
            'color' => 'Cyan',
            'year_model' => 2026,
            'engine_number' => 'B65-SECRET-B',
            'chassis_number' => 'MH3-SECRET-B',
            'purchase_date' => '2026-03-01',
        ]);
    }

    public function test_customer_authenticates_with_valid_tracking_ref_and_pin(): void
    {
        $response = $this->post(route('customer.verify'), [
            'tracking_reference' => 'MR-SJA-2026-AAAAAA',
            'pin' => '2233',
        ]);

        $response->assertRedirect(route('customer.status'));

        // Customer sees only their authorized motorcycle
        $statusResponse = $this->get(route('customer.status'));
        $statusResponse->assertStatus(200);
        $statusResponse->assertSee('NMAX 155 ABS');
        $statusResponse->assertSee('Arman');

        // Customer DOES NOT see Customer B's records
        $statusResponse->assertDontSee('Bernardo');
        $statusResponse->assertDontSee('Mio Aerox 155');
    }

    public function test_invalid_pin_is_rejected(): void
    {
        $response = $this->post(route('customer.verify'), [
            'tracking_reference' => 'MR-SJA-2026-AAAAAA',
            'pin' => '9999', // wrong pin
        ]);

        $response->assertSessionHasErrors('pin');
    }

    public function test_invalid_tracking_reference_is_rejected(): void
    {
        $response = $this->post(route('customer.verify'), [
            'tracking_reference' => 'MR-UNKNOWN-REF',
            'pin' => '1234',
        ]);

        $response->assertSessionHasErrors('tracking_reference');
    }

    public function test_unauthenticated_user_cannot_access_status_directly(): void
    {
        $response = $this->get(route('customer.status'));
        $response->assertRedirect(route('customer.track'));
    }
}
