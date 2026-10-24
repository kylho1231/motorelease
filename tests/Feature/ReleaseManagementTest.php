<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\LicensePlateRecord;
use App\Models\Motorcycle;
use App\Models\OrcrRecord;
use App\Models\ReleaseTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReleaseManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private Motorcycle $motorcycle;
    private OrcrRecord $orcrRecord;
    private LicensePlateRecord $plateRecord;

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

        $this->staff = User::create([
            'branch_id' => $branch->id,
            'name' => 'Staff Ramonito',
            'email' => 'ramonito@motorelease.ph',
            'password' => Hash::make('Password123!'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'branch_id' => $branch->id,
            'customer_code' => 'CUST-2026-00003',
            'first_name' => 'Felipe',
            'last_name' => 'Bautista',
            'mobile_number' => '09179998877',
            'address_line' => 'Sitio Seaside',
            'barangay' => 'Maybato Sur',
            'municipality' => 'San Jose de Buenavista',
            'province' => 'Antique',
            'postal_code' => '5700',
            'notification_preference' => 'sms',
            'privacy_consent' => true,
        ]);

        $this->motorcycle = Motorcycle::create([
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'tracking_reference' => 'MR-SJA-2026-000055',
            'tracking_pin_hash' => Hash::make('8877'),
            'manufacturer' => 'Yamaha',
            'model' => 'YZF-R15 V4',
            'color' => 'Racing Blue',
            'year_model' => 2026,
            'engine_number' => 'G3N-554433',
            'chassis_number' => 'MH3-554433',
            'purchase_date' => '2026-03-01',
            'status' => 'active',
        ]);

        $this->orcrRecord = OrcrRecord::create([
            'motorcycle_id' => $this->motorcycle->id,
            'or_number' => 'OR-7711',
            'cr_number' => 'CR-7711',
            'is_or_received' => true,
            'is_cr_received' => true,
            'status' => 'ready_for_pickup',
            'verified_at' => now(),
        ]);

        $this->plateRecord = LicensePlateRecord::create([
            'motorcycle_id' => $this->motorcycle->id,
            'status' => 'awaiting_physical_plate',
            'is_plate_received' => false,
        ]);
    }

    public function test_staff_releases_orcr_independently(): void
    {
        $this->actingAs($this->staff);

        $response = $this->post(route('releases.store'), [
            'motorcycle_id' => $this->motorcycle->id,
            'claim_items' => ['orcr'],
            'claimant_type' => 'owner',
            'claimant_name' => 'Felipe Bautista',
            'claimant_contact' => '09179998877',
            'id_type_presented' => "Driver's License",
            'id_number_masked' => 'DL-****-8812',
            'remarks' => 'Handed over in good order',
        ]);

        $response->assertSessionHas('success');

        $this->orcrRecord->refresh();
        $this->assertEquals('claimed', $this->orcrRecord->status);
        $this->assertNotNull($this->orcrRecord->claim_date);

        // License Plate MUST remain in awaiting state
        $this->plateRecord->refresh();
        $this->assertEquals('awaiting_physical_plate', $this->plateRecord->status);

        // Release Transaction should exist
        $tx = ReleaseTransaction::first();
        $this->assertNotNull($tx);
        $this->assertEquals('Felipe Bautista', $tx->claimant_name);
        $this->assertEquals(1, $tx->releaseItems()->count());
    }

    public function test_duplicate_claim_attempts_are_strictly_rejected(): void
    {
        $this->actingAs($this->staff);

        // Mark OR/CR already claimed
        $this->orcrRecord->update(['status' => 'claimed']);

        // Attempting to claim again
        $response = $this->post(route('releases.store'), [
            'motorcycle_id' => $this->motorcycle->id,
            'claim_items' => ['orcr'],
            'claimant_type' => 'owner',
            'claimant_name' => 'Felipe Bautista',
            'claimant_contact' => '09179998877',
            'id_type_presented' => "Driver's License",
            'id_number_masked' => 'DL-****-8812',
        ]);

        $response->assertSessionHasErrors('error');
    }

    public function test_representative_release_requires_authorization_letter_verification(): void
    {
        $this->actingAs($this->staff);

        // Submitting representative without authorization letter checked
        $response = $this->post(route('releases.store'), [
            'motorcycle_id' => $this->motorcycle->id,
            'claim_items' => ['orcr'],
            'claimant_type' => 'authorized_representative',
            'claimant_name' => 'Pedro Representative',
            'claimant_contact' => '09221112233',
            'id_type_presented' => 'UMID',
            'id_number_masked' => 'UMID-****-9921',
            'authorization_letter_checked' => 0,
        ]);

        $response->assertSessionHasErrors('authorization_letter_checked');
    }
}
