<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Motorcycle;
use App\Models\OrcrRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportingAndAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
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

        $this->admin = User::create([
            'branch_id' => $this->branch->id,
            'name' => 'Admin Boss',
            'email' => 'admin@motorelease.ph',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_reports_page_calculates_and_displays_dynamic_metrics(): void
    {
        $this->actingAs($this->admin);

        $customer = Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUST-REP-1',
            'first_name' => 'Mario',
            'last_name' => 'Lopez',
            'mobile_number' => '09170001122',
            'address_line' => 'St.',
            'barangay' => 'San Pedro',
            'municipality' => 'San Jose de Buenavista',
            'province' => 'Antique',
            'postal_code' => '5700',
            'privacy_consent' => true,
        ]);

        $bike = Motorcycle::create([
            'customer_id' => $customer->id,
            'branch_id' => $this->branch->id,
            'tracking_reference' => 'MR-SJA-2026-REP01',
            'manufacturer' => 'Yamaha',
            'model' => 'NMAX 155',
            'color' => 'Black',
            'year_model' => 2026,
            'engine_number' => 'G3M-REP-01',
            'chassis_number' => 'MH3-REP-01',
            'purchase_date' => '2026-03-01',
        ]);

        OrcrRecord::create([
            'motorcycle_id' => $bike->id,
            'status' => 'ready_for_pickup',
            'is_or_received' => true,
            'is_cr_received' => true,
            'verified_at' => now()->subDays(20), // overdue unclaimed
        ]);

        $response = $this->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Dealership Operational Reports');
        $response->assertSee('Unclaimed Items Aging Report');
        $response->assertSee('MR-SJA-2026-REP01');
    }

    public function test_csv_export_streams_proper_csv_file(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('reports.export_csv', ['type' => 'inventory']));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_audit_logs_record_events_accurately(): void
    {
        $this->actingAs($this->admin);

        AuditLog::log('TEST_SYSTEM_EVENT', 'Auditing verification scenario.');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'TEST_SYSTEM_EVENT',
            'description' => 'Auditing verification scenario.',
            'user_id' => $this->admin->id,
        ]);
    }
}
