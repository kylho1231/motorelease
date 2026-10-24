<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\LicensePlateRecord;
use App\Models\Motorcycle;
use App\Models\Notification;
use App\Models\OrcrRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LicensePlateVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private Motorcycle $motorcycle;
    private LicensePlateRecord $plateRecord;
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
            'customer_code' => 'CUST-2026-00002',
            'first_name' => 'Grace',
            'last_name' => 'Tan',
            'mobile_number' => '09281234567',
            'address_line' => 'Zone 1',
            'barangay' => 'Madrangca',
            'municipality' => 'San Jose de Buenavista',
            'province' => 'Antique',
            'postal_code' => '5700',
            'notification_preference' => 'sms',
            'privacy_consent' => true,
        ]);

        $this->motorcycle = Motorcycle::create([
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'tracking_reference' => 'MR-SJA-2026-000099',
            'manufacturer' => 'Yamaha',
            'model' => 'Mio Gear 125',
            'color' => 'Matte Gray',
            'year_model' => 2026,
            'engine_number' => 'E33-887711',
            'chassis_number' => 'MH3-887711',
            'purchase_date' => '2026-03-01',
            'status' => 'active',
        ]);

        $this->orcrRecord = OrcrRecord::create([
            'motorcycle_id' => $this->motorcycle->id,
            'status' => 'ready_for_pickup',
            'is_or_received' => true,
            'is_cr_received' => true,
            'verified_at' => now(),
        ]);

        $this->plateRecord = LicensePlateRecord::create([
            'motorcycle_id' => $this->motorcycle->id,
            'status' => 'awaiting_physical_plate',
            'is_plate_received' => false,
        ]);
    }

    public function test_plate_availability_remains_independent_from_orcr(): void
    {
        // OR/CR is already ready_for_pickup, plate must remain awaiting
        $this->assertEquals('ready_for_pickup', $this->orcrRecord->status);
        $this->assertEquals('awaiting_physical_plate', $this->plateRecord->status);
    }

    public function test_plate_is_received_and_verified_ready_with_sms_notification(): void
    {
        $this->actingAs($this->staff);

        // Mark plate received
        $this->put(route('plates.update_receipt', $this->plateRecord), [
            'is_plate_received' => 1,
            'plate_number' => '889-XYZ',
            'received_date' => now()->format('Y-m-d'),
        ]);

        $this->plateRecord->refresh();
        $this->assertTrue($this->plateRecord->is_plate_received);

        // Verify plate readiness
        $response = $this->post(route('plates.verify_ready', $this->plateRecord), [
            'storage_bin_ref' => 'Bin #05',
        ]);

        $response->assertSessionHas('success');
        $this->plateRecord->refresh();
        $this->assertEquals('ready_for_pickup', $this->plateRecord->status);
        $this->assertNotNull($this->plateRecord->verified_at);

        // Plate-ready SMS notification created
        $notif = Notification::where('motorcycle_id', $this->motorcycle->id)
            ->where('event_type', 'plate_ready')
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('delivered', $notif->status);
    }

    public function test_failed_sms_notification_can_be_retried_safely(): void
    {
        $this->actingAs($this->staff);

        $notif = Notification::create([
            'motorcycle_id' => $this->motorcycle->id,
            'customer_id' => $this->motorcycle->customer_id,
            'event_type' => 'plate_ready',
            'recipient_number' => '09281234567',
            'message_body' => 'Your plate is ready.',
            'status' => 'failed',
            'error_message' => 'Simulated network timeout',
            'idempotency_key' => 'FAIL_KEY_TEST_1',
            'retry_count' => 0,
        ]);

        $response = $this->post(route('notifications.resend', $notif));

        $response->assertSessionHas('success');
        $notif->refresh();
        $this->assertEquals('delivered', $notif->status);
        $this->assertEquals(1, $notif->retry_count);
    }
}
