<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Motorcycle;
use App\Models\Notification;
use App\Models\OrcrRecord;
use App\Models\User;
use App\Services\SmsGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrcrVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private Motorcycle $motorcycle;
    private OrcrRecord $orcrRecord;

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
            'name' => 'Staff Maria',
            'email' => 'staff@motorelease.ph',
            'password' => Hash::make('Password123!'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'branch_id' => $branch->id,
            'customer_code' => 'CUST-2026-00001',
            'first_name' => 'Danilo',
            'last_name' => 'Marcos',
            'mobile_number' => '09175556789',
            'address_line' => 'Zone 2',
            'barangay' => 'San Pedro',
            'municipality' => 'San Jose de Buenavista',
            'province' => 'Antique',
            'postal_code' => '5700',
            'notification_preference' => 'sms',
            'privacy_consent' => true,
        ]);

        $this->motorcycle = Motorcycle::create([
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'tracking_reference' => 'MR-SJA-2026-000088',
            'manufacturer' => 'Yamaha',
            'model' => 'Sniper 155',
            'color' => 'Midnight Black',
            'year_model' => 2026,
            'engine_number' => 'G3R-998811',
            'chassis_number' => 'MH3-998811',
            'purchase_date' => '2026-03-01',
            'status' => 'active',
        ]);

        $this->orcrRecord = OrcrRecord::create([
            'motorcycle_id' => $this->motorcycle->id,
            'status' => 'awaiting_documents',
            'is_or_received' => false,
            'is_cr_received' => false,
        ]);
    }

    public function test_orcr_remains_pending_when_only_one_document_is_available(): void
    {
        $this->actingAs($this->staff);

        // Record receipt of OR only
        $this->put(route('orcr.update_receipt', $this->orcrRecord), [
            'is_or_received' => 1,
            'or_number' => 'OR-99120',
            'or_received_date' => '2026-03-10',
            'is_cr_received' => 0,
        ]);

        $this->orcrRecord->refresh();
        $this->assertEquals('partially_received', $this->orcrRecord->status);
        $this->assertFalse($this->orcrRecord->isPhysicallyComplete());
    }

    public function test_physical_verification_gate_rejects_readiness_if_cr_is_missing(): void
    {
        $this->actingAs($this->staff);

        $this->orcrRecord->update([
            'is_or_received' => true,
            'is_cr_received' => false,
        ]);

        $response = $this->post(route('orcr.verify_ready', $this->orcrRecord));

        $response->assertSessionHasErrors('error');
        $this->orcrRecord->refresh();
        $this->assertNotEquals('ready_for_pickup', $this->orcrRecord->status);
    }

    public function test_staff_confirms_readiness_when_both_documents_are_physically_verified(): void
    {
        $this->actingAs($this->staff);

        $this->orcrRecord->update([
            'is_or_received' => true,
            'or_number' => 'OR-99120',
            'or_received_date' => now(),
            'is_cr_received' => true,
            'cr_number' => 'CR-44120',
            'cr_received_date' => now(),
        ]);

        $response = $this->post(route('orcr.verify_ready', $this->orcrRecord));

        $response->assertSessionHas('success');
        $this->orcrRecord->refresh();
        $this->assertEquals('ready_for_pickup', $this->orcrRecord->status);
        $this->assertNotNull($this->orcrRecord->verified_at);

        // A single SMS notification should be queued/delivered
        $notif = Notification::where('motorcycle_id', $this->motorcycle->id)
            ->where('event_type', 'orcr_ready')
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('delivered', $notif->status);
    }

    public function test_repeated_saves_do_not_create_duplicate_sms_notifications(): void
    {
        $this->actingAs($this->staff);

        $this->orcrRecord->update([
            'is_or_received' => true,
            'is_cr_received' => true,
        ]);

        // First trigger
        SmsGatewayService::queueNotification($this->motorcycle, 'orcr_ready', $this->staff->id);

        $countBefore = Notification::where('motorcycle_id', $this->motorcycle->id)
            ->where('event_type', 'orcr_ready')
            ->count();

        $this->assertEquals(1, $countBefore);

        // Repeated trigger attempt without force flag
        SmsGatewayService::queueNotification($this->motorcycle, 'orcr_ready', $this->staff->id);

        $countAfter = Notification::where('motorcycle_id', $this->motorcycle->id)
            ->where('event_type', 'orcr_ready')
            ->count();

        // Idempotency must prevent duplicate record creation
        $this->assertEquals(1, $countAfter);
    }
}
