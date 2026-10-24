<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\LicensePlateRecord;
use App\Models\Motorcycle;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\OrcrRecord;
use App\Models\ReleaseItem;
use App\Models\ReleaseTransaction;
use App\Models\StatusHistory;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic production-ready dealership data.
     */
    public function run(): void
    {
        // 1. Create Dealership Branch
        $branch = Branch::create([
            'code' => 'SJA-01',
            'name' => 'San Jose de Buenavista Main Dealership',
            'dealership_brand' => 'Yamaha 3S Authorized Dealer',
            'address_line' => 'Bancal Street corner Trade Town Road, Brgy. Funda-Dalipe',
            'municipality' => 'San Jose de Buenavista',
            'province' => 'Antique',
            'postal_code' => '5700',
            'contact_number' => '0917-889-1234 / (036) 540-8921',
            'email' => 'sanjose.antique@motorelease.ph',
            'operating_hours' => 'Monday to Saturday: 8:00 AM – 5:00 PM',
            'pickup_instructions' => 'Please bring two (2) valid government-issued IDs and your official delivery receipt or sales invoice. If an authorized representative is claiming on your behalf, provide a signed authorization letter and photocopy of the registered owner\'s ID.',
            'is_active' => true,
        ]);

        // Secondary Branch (for multi-branch demonstration)
        $branchSibalom = Branch::create([
            'code' => 'SBL-02',
            'name' => 'Sibalom Satellite Branch',
            'dealership_brand' => 'Yamaha 3S Authorized Dealer',
            'address_line' => 'National Highway, Brgy. District II',
            'municipality' => 'Sibalom',
            'province' => 'Antique',
            'postal_code' => '5701',
            'contact_number' => '0928-554-3210',
            'email' => 'sibalom@motorelease.ph',
            'operating_hours' => 'Monday to Saturday: 8:30 AM – 4:30 PM',
            'pickup_instructions' => 'Present official tracking slip, valid ID, and purchase sales invoice.',
            'is_active' => true,
        ]);

        // 2. Create Users (Admin, Manager, Staff)
        $admin = User::create([
            'branch_id' => $branch->id,
            'name' => 'John Kenneth Admin',
            'email' => 'admin@motorelease.ph',
            'phone_number' => '09178889901',
            'role' => 'admin',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $manager = User::create([
            'branch_id' => $branch->id,
            'name' => 'Maria Elena Fernandez',
            'email' => 'manager@motorelease.ph',
            'phone_number' => '09178889902',
            'role' => 'manager',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $staff = User::create([
            'branch_id' => $branch->id,
            'name' => 'Ramonito Dela Cruz',
            'email' => 'staff@motorelease.ph',
            'phone_number' => '09178889903',
            'role' => 'staff',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // 3. Notification Templates
        $templates = [
            [
                'code' => 'ORCR_READY',
                'name' => 'OR/CR Ready Notification',
                'description' => 'Dispatched when both OR and CR are physically confirmed and verified by staff.',
                'body_template' => 'Good day {customer_name}! Your motorcycle OR/CR for {model} is physically verified and READY FOR PICKUP at {branch_name}. Please bring 2 valid IDs. Tracking Reference: {tracking_ref}. Track status: {tracking_url}',
                'is_active' => true,
            ],
            [
                'code' => 'PLATE_READY',
                'name' => 'License Plate Ready Notification',
                'description' => 'Dispatched when physical metal license plate is verified at the branch.',
                'body_template' => 'Good day {customer_name}! Your motorcycle license plate ({plate_number}) is now AVAILABLE for collection at {branch_name}. Please present valid ID. Reference: {tracking_ref}. Inquiries: {branch_phone}',
                'is_active' => true,
            ],
            [
                'code' => 'COMBINED_READY',
                'name' => 'Combined OR/CR & Plate Ready Notification',
                'description' => 'Sent when both OR/CR and license plate are simultaneously ready for collection.',
                'body_template' => 'Good day {customer_name}! Both your OR/CR and license plate for {model} are ready for pickup at {branch_name}. Please present 2 valid IDs. Reference: {tracking_ref}. Track: {tracking_url}',
                'is_active' => true,
            ],
            [
                'code' => 'PICKUP_REMINDER',
                'name' => 'Unclaimed Item Pickup Reminder',
                'description' => 'Gentle reminder sent to customers who have verified items ready > 14 days.',
                'body_template' => 'Reminder from {branch_name}: Your motorcycle documents/plate for {model} are waiting for collection. Office hours: {operating_hours}. Reference: {tracking_ref}',
                'is_active' => true,
            ],
            [
                'code' => 'STATUS_HOLD',
                'name' => 'Document Hold Notification',
                'description' => 'Sent when a document or plate undergoes discrepancy review.',
                'body_template' => 'Notice from {branch_name}: Document release for {model} is temporarily on hold for verification. Please call our hotline at {branch_phone} or visit our branch. Ref: {tracking_ref}',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $tmpl) {
            NotificationTemplate::create($tmpl);
        }

        // 4. System Settings
        SystemSetting::set('dealership_name', 'Yamaha Authorized Dealership - San Jose Antique', 'general', 'string', 'Official Dealership Name');
        SystemSetting::set('app_system_name', 'MotoRelease System', 'general', 'string', 'Independent System Branding');
        SystemSetting::set('sms_provider_active', 'mock', 'sms', 'string', 'Active SMS gateway driver (mock or semaphore)');
        SystemSetting::set('require_2_ids_for_release', 'true', 'releasing', 'boolean', 'Require 2 valid IDs for document handover');
        SystemSetting::set('auto_queue_sms_on_verified', 'true', 'sms', 'boolean', 'Automatically queue SMS when readiness is verified');

        // 5. Seed Realistic Customers in Antique
        $customersData = [
            [
                'customer_code' => 'CUST-2026-00001',
                'first_name' => 'Eduardo',
                'middle_name' => 'Tan',
                'last_name' => 'Villanueva',
                'suffix' => 'Jr.',
                'mobile_number' => '09175551201',
                'email' => 'eduardo.villanueva@example.com',
                'address_line' => 'Zone 4, Sitio Looc',
                'barangay' => 'Funda-Dalipe',
                'municipality' => 'San Jose de Buenavista',
                'province' => 'Antique',
                'postal_code' => '5700',
                'notification_preference' => 'sms',
            ],
            [
                'customer_code' => 'CUST-2026-00002',
                'first_name' => 'Clarissa',
                'middle_name' => 'Magbanua',
                'last_name' => 'Alcantara',
                'suffix' => null,
                'mobile_number' => '09287773412',
                'email' => 'clarissa.alcantara@example.com',
                'address_line' => 'Poblacion Proper, near Municipal Plaza',
                'barangay' => 'District I',
                'municipality' => 'San Jose de Buenavista',
                'province' => 'Antique',
                'postal_code' => '5700',
                'notification_preference' => 'sms',
            ],
            [
                'customer_code' => 'CUST-2026-00003',
                'first_name' => 'Reynaldo',
                'middle_name' => 'Salazar',
                'last_name' => 'Montinola',
                'suffix' => null,
                'mobile_number' => '09194448921',
                'email' => 'rey.montinola@example.com',
                'address_line' => 'Barangay Road, Purok Kawayan',
                'barangay' => 'Madrangca',
                'municipality' => 'San Jose de Buenavista',
                'province' => 'Antique',
                'postal_code' => '5700',
                'notification_preference' => 'sms',
            ],
            [
                'customer_code' => 'CUST-2026-00004',
                'first_name' => 'Jocelyn',
                'middle_name' => 'Geronimo',
                'last_name' => 'Abiera',
                'suffix' => null,
                'mobile_number' => '09183337719',
                'email' => 'jocelyn.abiera@example.com',
                'address_line' => 'Coastal View Highway',
                'barangay' => 'Maybato Sur',
                'municipality' => 'San Jose de Buenavista',
                'province' => 'Antique',
                'postal_code' => '5700',
                'notification_preference' => 'sms',
            ],
            [
                'customer_code' => 'CUST-2026-00005',
                'first_name' => 'Anthony',
                'middle_name' => 'Bautista',
                'last_name' => 'Santos',
                'suffix' => 'III',
                'mobile_number' => '09176662388',
                'email' => 'anthony.santos@example.com',
                'address_line' => 'Trade Town Commercial Complex',
                'barangay' => 'San Pedro',
                'municipality' => 'San Jose de Buenavista',
                'province' => 'Antique',
                'postal_code' => '5700',
                'notification_preference' => 'sms',
            ],
            [
                'customer_code' => 'CUST-2026-00006',
                'first_name' => 'Maricel',
                'middle_name' => 'Palacios',
                'last_name' => 'Damasco',
                'suffix' => null,
                'mobile_number' => '09208884510',
                'email' => 'maricel.damasco@example.com',
                'address_line' => 'Sitio Ilawod, Barangay Mojon',
                'barangay' => 'Mojon',
                'municipality' => 'San Jose de Buenavista',
                'province' => 'Antique',
                'postal_code' => '5700',
                'notification_preference' => 'sms',
            ],
        ];

        $customers = [];
        foreach ($customersData as $c) {
            $customers[] = Customer::create(array_merge($c, [
                'branch_id' => $branch->id,
                'is_verified' => true,
                'privacy_consent' => true,
                'created_by_user_id' => $staff->id,
            ]));
        }

        // 6. Seed Realistic Motorcycle Scenarios
        // Scenario A: Brand new purchase - OR/CR and Plate both pending
        $bike1 = Motorcycle::create([
            'customer_id' => $customers[0]->id,
            'branch_id' => $branch->id,
            'tracking_reference' => 'MR-SJA-2026-000001',
            'tracking_pin_hash' => Hash::make(substr($customers[0]->mobile_number, -4)), // last 4 digits
            'manufacturer' => 'Yamaha',
            'model' => 'NMAX 155 ABS',
            'variant' => 'Connected/ABS Version',
            'color' => 'Matte Dark Green',
            'year_model' => 2026,
            'engine_number' => 'G3M1E-0194821',
            'chassis_number' => 'MH3SG6410RJ019482',
            'dealer_transaction_ref' => 'INV-SJA-2026-0104',
            'registration_reference' => 'LTO-REG-APP-9812',
            'purchase_date' => now()->subDays(10)->format('Y-m-d'),
            'status' => 'active',
            'created_by_user_id' => $staff->id,
        ]);

        OrcrRecord::create([
            'motorcycle_id' => $bike1->id,
            'is_or_received' => false,
            'is_cr_received' => false,
            'status' => 'awaiting_documents',
            'remarks' => 'Submitted to LTO liaison officer on ' . now()->subDays(7)->format('M d, Y'),
        ]);

        LicensePlateRecord::create([
            'motorcycle_id' => $bike1->id,
            'is_plate_received' => false,
            'status' => 'awaiting_physical_plate',
            'remarks' => 'Awaiting plate production from LTO Central Plate Facility',
        ]);

        // Scenario B: Partially Received OR/CR (OR received, CR pending) - Cannot be marked ready!
        $bike2 = Motorcycle::create([
            'customer_id' => $customers[1]->id,
            'branch_id' => $branch->id,
            'tracking_reference' => 'MR-SJA-2026-000002',
            'tracking_pin_hash' => Hash::make(substr($customers[1]->mobile_number, -4)),
            'manufacturer' => 'Yamaha',
            'model' => 'Mio Aerox 155',
            'variant' => 'Standard Version',
            'color' => 'Icon Blue',
            'year_model' => 2026,
            'engine_number' => 'B651E-0482190',
            'chassis_number' => 'MH3SG5310RJ048219',
            'dealer_transaction_ref' => 'INV-SJA-2026-0129',
            'purchase_date' => now()->subDays(18)->format('Y-m-d'),
            'status' => 'active',
            'created_by_user_id' => $staff->id,
        ]);

        OrcrRecord::create([
            'motorcycle_id' => $bike2->id,
            'or_number' => 'OR-2026-99214',
            'is_or_received' => true,
            'or_received_date' => now()->subDays(3)->format('Y-m-d'),
            'is_cr_received' => false,
            'received_by_user_id' => $staff->id,
            'status' => 'partially_received',
            'storage_location_ref' => 'Folder Batch-04B',
            'remarks' => 'Official Receipt received from LTO Antique District Office. Physical CR copy still in transit.',
        ]);

        LicensePlateRecord::create([
            'motorcycle_id' => $bike2->id,
            'is_plate_received' => false,
            'status' => 'awaiting_physical_plate',
        ]);

        // Scenario C: Both OR and CR physically confirmed & verified -> READY FOR PICKUP, SMS dispatched!
        $bike3 = Motorcycle::create([
            'customer_id' => $customers[2]->id,
            'branch_id' => $branch->id,
            'tracking_reference' => 'MR-SJA-2026-000003',
            'tracking_pin_hash' => Hash::make(substr($customers[2]->mobile_number, -4)),
            'manufacturer' => 'Yamaha',
            'model' => 'Sniper 155R',
            'variant' => 'Special Edition with Smart Key',
            'color' => 'Midnight Cyan',
            'year_model' => 2026,
            'engine_number' => 'G3R1E-0331902',
            'chassis_number' => 'MH3SG7120RJ033190',
            'dealer_transaction_ref' => 'INV-SJA-2026-0088',
            'purchase_date' => now()->subDays(25)->format('Y-m-d'),
            'status' => 'active',
            'created_by_user_id' => $staff->id,
        ]);

        $orcr3 = OrcrRecord::create([
            'motorcycle_id' => $bike3->id,
            'or_number' => 'OR-2026-88120',
            'cr_number' => 'CR-2026-44910',
            'is_or_received' => true,
            'or_received_date' => now()->subDays(2)->format('Y-m-d'),
            'is_cr_received' => true,
            'cr_received_date' => now()->subDays(2)->format('Y-m-d'),
            'received_by_user_id' => $staff->id,
            'verified_by_user_id' => $manager->id,
            'verified_at' => now()->subDays(2)->setHour(14)->setMinute(30),
            'status' => 'ready_for_pickup',
            'storage_location_ref' => 'Cabinet 1 - Shelf B (Envelope #34)',
            'remarks' => 'Documents verified complete with clear official dry seal.',
        ]);

        StatusHistory::create([
            'trackable_type' => OrcrRecord::class,
            'trackable_id' => $orcr3->id,
            'from_status' => 'partially_received',
            'to_status' => 'ready_for_pickup',
            'changed_by_user_id' => $manager->id,
            'notes' => 'Both OR and CR physical copies confirmed and inspected.',
        ]);

        Notification::create([
            'motorcycle_id' => $bike3->id,
            'customer_id' => $customers[2]->id,
            'triggered_by_user_id' => $manager->id,
            'event_type' => 'orcr_ready',
            'recipient_number' => $customers[2]->mobile_number,
            'message_body' => "Good day {$customers[2]->first_name}! Your motorcycle OR/CR for Sniper 155R is verified and READY FOR PICKUP at San Jose de Buenavista Main Dealership. Please bring 2 valid IDs. Tracking Reference: MR-SJA-2026-000003.",
            'status' => 'delivered',
            'provider' => 'mock_gateway',
            'provider_message_id' => 'MOCK-NOTIF-003841',
            'retry_count' => 0,
            'idempotency_key' => hash('sha256', "NOTIF_{$bike3->id}_orcr_ready_initial_seed"),
            'sent_at' => now()->subDays(2)->setHour(14)->setMinute(35),
            'delivered_at' => now()->subDays(2)->setHour(14)->setMinute(36),
        ]);

        LicensePlateRecord::create([
            'motorcycle_id' => $bike3->id,
            'is_plate_received' => false,
            'status' => 'awaiting_physical_plate',
            'remarks' => 'Awaiting plate shipment batch.',
        ]);

        // Scenario D: Both OR/CR and License Plate Ready for collection!
        $bike4 = Motorcycle::create([
            'customer_id' => $customers[3]->id,
            'branch_id' => $branch->id,
            'tracking_reference' => 'MR-SJA-2026-000004',
            'tracking_pin_hash' => Hash::make(substr($customers[3]->mobile_number, -4)),
            'manufacturer' => 'Yamaha',
            'model' => 'Fazzio 125 Hybrid',
            'variant' => 'Standard Hybrid',
            'color' => 'Cyan Metallic',
            'year_model' => 2026,
            'engine_number' => 'E321E-0994120',
            'chassis_number' => 'MH3SE8920RJ099412',
            'dealer_transaction_ref' => 'INV-SJA-2026-0045',
            'purchase_date' => now()->subDays(45)->format('Y-m-d'),
            'status' => 'active',
            'created_by_user_id' => $staff->id,
        ]);

        OrcrRecord::create([
            'motorcycle_id' => $bike4->id,
            'or_number' => 'OR-2026-77312',
            'cr_number' => 'CR-2026-33104',
            'is_or_received' => true,
            'or_received_date' => now()->subDays(12)->format('Y-m-d'),
            'is_cr_received' => true,
            'cr_received_date' => now()->subDays(12)->format('Y-m-d'),
            'received_by_user_id' => $staff->id,
            'verified_by_user_id' => $staff->id,
            'verified_at' => now()->subDays(12),
            'status' => 'ready_for_pickup',
            'storage_location_ref' => 'Cabinet 1 - Shelf A (Envelope #12)',
        ]);

        LicensePlateRecord::create([
            'motorcycle_id' => $bike4->id,
            'plate_number' => '542-EAF',
            'mv_file_number' => '0601-0000094123',
            'is_plate_received' => true,
            'received_date' => now()->subDays(5)->format('Y-m-d'),
            'received_by_user_id' => $staff->id,
            'verified_by_user_id' => $manager->id,
            'verified_at' => now()->subDays(5),
            'status' => 'ready_for_pickup',
            'storage_bin_ref' => 'Bin #04 - Slot 18',
            'remarks' => 'Embossed aluminum plate verified matching chassis and MV File numbers.',
        ]);

        // Scenario E: OR/CR Claimed, Plate still pending!
        $bike5 = Motorcycle::create([
            'customer_id' => $customers[4]->id,
            'branch_id' => $branch->id,
            'tracking_reference' => 'MR-SJA-2026-000005',
            'tracking_pin_hash' => Hash::make(substr($customers[4]->mobile_number, -4)),
            'manufacturer' => 'Yamaha',
            'model' => 'YZF-R15 V4',
            'variant' => 'Standard Edition',
            'color' => 'Racing Blue',
            'year_model' => 2026,
            'engine_number' => 'G3N2E-0128741',
            'chassis_number' => 'MH3SG5810RJ012874',
            'dealer_transaction_ref' => 'INV-SJA-2026-0021',
            'purchase_date' => now()->subDays(60)->format('Y-m-d'),
            'status' => 'active',
            'created_by_user_id' => $staff->id,
        ]);

        $orcr5 = OrcrRecord::create([
            'motorcycle_id' => $bike5->id,
            'or_number' => 'OR-2026-66291',
            'cr_number' => 'CR-2026-22810',
            'is_or_received' => true,
            'or_received_date' => now()->subDays(30)->format('Y-m-d'),
            'is_cr_received' => true,
            'cr_received_date' => now()->subDays(30)->format('Y-m-d'),
            'received_by_user_id' => $staff->id,
            'verified_by_user_id' => $staff->id,
            'verified_at' => now()->subDays(30),
            'status' => 'claimed',
            'claim_date' => now()->subDays(15),
            'released_by_user_id' => $staff->id,
            'remarks' => 'Released to owner Anthony Santos III. Claim stub surrendered.',
        ]);

        // Record the formal release transaction for bike 5 OR/CR
        $relTx5 = ReleaseTransaction::create([
            'transaction_code' => 'REL-2026-00001',
            'branch_id' => $branch->id,
            'processed_by_user_id' => $staff->id,
            'claimant_type' => 'owner',
            'claimant_name' => 'Anthony Santos III',
            'claimant_contact' => '09176662388',
            'id_type_presented' => 'Philippine Passport',
            'id_number_masked' => 'P****782B',
            'authorization_letter_checked' => false,
            'released_at' => now()->subDays(15),
            'remarks' => 'Claimed original OR & CR. Advised customer regarding license plate schedule.',
        ]);

        ReleaseItem::create([
            'release_transaction_id' => $relTx5->id,
            'motorcycle_id' => $bike5->id,
            'item_type' => 'orcr',
            'item_record_id' => $orcr5->id,
            'item_identifier' => 'OR-2026-66291 / CR-2026-22810',
            'status_before' => 'ready_for_pickup',
            'status_after' => 'claimed',
        ]);

        LicensePlateRecord::create([
            'motorcycle_id' => $bike5->id,
            'is_plate_received' => false,
            'status' => 'awaiting_physical_plate',
            'remarks' => 'Plate batch expected by next month.',
        ]);

        // Scenario F: Document On-Hold (Lien / Discrepancy notice)
        $bike6 = Motorcycle::create([
            'customer_id' => $customers[5]->id,
            'branch_id' => $branch->id,
            'tracking_reference' => 'MR-SJA-2026-000006',
            'tracking_pin_hash' => Hash::make(substr($customers[5]->mobile_number, -4)),
            'manufacturer' => 'Yamaha',
            'model' => 'Mio Gear 125',
            'variant' => 'Standard Version',
            'color' => 'Matte Ash Gray',
            'year_model' => 2026,
            'engine_number' => 'E334E-0551982',
            'chassis_number' => 'MH3SE7210RJ055198',
            'dealer_transaction_ref' => 'INV-SJA-2026-0155',
            'purchase_date' => now()->subDays(22)->format('Y-m-d'),
            'status' => 'active',
            'created_by_user_id' => $staff->id,
        ]);

        OrcrRecord::create([
            'motorcycle_id' => $bike6->id,
            'is_or_received' => true,
            'is_cr_received' => true,
            'status' => 'on_hold',
            'hold_reason' => 'Typographical correction in middle name endorsement sent to LTO district office.',
            'remarks' => 'LTO requested amendment affidavit for name spelling.',
        ]);

        LicensePlateRecord::create([
            'motorcycle_id' => $bike6->id,
            'is_plate_received' => false,
            'status' => 'awaiting_physical_plate',
        ]);

        AuditLog::log('SYSTEM_SEED', 'Initial system seeding completed with realistic Antique dealership records.', $admin);
    }
}
