<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Motorcycle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAndMotorcycleTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'code' => 'SJA-01',
            'name' => 'San Jose Main Dealership',
            'address_line' => 'Trade Town',
            'municipality' => 'San Jose de Buenavista',
            'province' => 'Antique',
            'postal_code' => '5700',
            'contact_number' => '09171234567',
        ]);

        $this->staff = User::create([
            'branch_id' => $this->branch->id,
            'name' => 'Staff Maria',
            'email' => 'staff@motorelease.ph',
            'password' => Hash::make('Password123!'),
            'role' => 'staff',
            'is_active' => true,
        ]);
    }

    public function test_staff_registers_a_customer_and_motorcycle(): void
    {
        $this->actingAs($this->staff);

        // 1. Register Customer
        $customerResponse = $this->post('/customers', [
            'branch_id' => $this->branch->id,
            'first_name' => 'Roberto',
            'middle_name' => 'Alcantara',
            'last_name' => 'Cruz',
            'mobile_number' => '09178881234',
            'email' => 'roberto@example.com',
            'address_line' => 'Purok Narra',
            'barangay' => 'Funda-Dalipe',
            'municipality' => 'San Jose de Buenavista',
            'province' => 'Antique',
            'postal_code' => '5700',
            'notification_preference' => 'sms',
            'privacy_consent' => '1',
        ]);

        $customer = Customer::where('mobile_number', '09178881234')->first();
        $this->assertNotNull($customer);
        $customerResponse->assertRedirect(route('customers.show', $customer));

        // 2. Register Motorcycle
        $bikeResponse = $this->post('/motorcycles', [
            'customer_id' => $customer->id,
            'branch_id' => $this->branch->id,
            'manufacturer' => 'Yamaha',
            'model' => 'NMAX 155 ABS',
            'color' => 'Icon Blue',
            'year_model' => 2026,
            'engine_number' => 'G3M-9812401',
            'chassis_number' => 'MH3-981240192',
            'purchase_date' => '2026-03-01',
        ]);

        $motorcycle = Motorcycle::where('engine_number', 'G3M-9812401')->first();
        $this->assertNotNull($motorcycle);
        $bikeResponse->assertRedirect(route('motorcycles.show', $motorcycle));

        // Check tracking reference format
        $this->assertStringStartsWith('MR-SJA-', $motorcycle->tracking_reference);

        // Check initial OR/CR and Plate records exist in awaiting state
        $this->assertNotNull($motorcycle->orcrRecord);
        $this->assertEquals('awaiting_documents', $motorcycle->orcrRecord->status);

        $this->assertNotNull($motorcycle->licensePlateRecord);
        $this->assertEquals('awaiting_physical_plate', $motorcycle->licensePlateRecord->status);
    }

    public function test_customer_can_own_more_than_one_motorcycle(): void
    {
        $this->actingAs($this->staff);

        $customer = Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUST-2026-00010',
            'first_name' => 'Elisa',
            'last_name' => 'Perez',
            'mobile_number' => '09289991234',
            'address_line' => 'Main St.',
            'barangay' => 'Mojon',
            'municipality' => 'San Jose de Buenavista',
            'province' => 'Antique',
            'postal_code' => '5700',
            'notification_preference' => 'sms',
            'privacy_consent' => true,
        ]);

        $this->post('/motorcycles', [
            'customer_id' => $customer->id,
            'branch_id' => $this->branch->id,
            'manufacturer' => 'Yamaha',
            'model' => 'Mio Aerox 155',
            'color' => 'Cyan',
            'year_model' => 2026,
            'engine_number' => 'B65-100001',
            'chassis_number' => 'MH3-100001',
            'purchase_date' => '2026-03-01',
        ]);

        $this->post('/motorcycles', [
            'customer_id' => $customer->id,
            'branch_id' => $this->branch->id,
            'manufacturer' => 'Yamaha',
            'model' => 'Fazzio 125',
            'color' => 'White',
            'year_model' => 2026,
            'engine_number' => 'E32-200002',
            'chassis_number' => 'MH3-200002',
            'purchase_date' => '2026-03-05',
        ]);

        $this->assertEquals(2, $customer->motorcycles()->count());
    }
}
