<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $manager;
    private User $staff;

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

        $this->admin = User::create([
            'branch_id' => $branch->id,
            'name' => 'Admin User',
            'email' => 'admin@motorelease.ph',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->manager = User::create([
            'branch_id' => $branch->id,
            'name' => 'Manager User',
            'email' => 'manager@motorelease.ph',
            'password' => Hash::make('Password123!'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->staff = User::create([
            'branch_id' => $branch->id,
            'name' => 'Staff User',
            'email' => 'staff@motorelease.ph',
            'password' => Hash::make('Password123!'),
            'role' => 'staff',
            'is_active' => true,
        ]);
    }

    public function test_unauthorized_staff_cannot_access_admin_settings(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get('/settings');
        $response->assertStatus(403);

        $responseUsers = $this->get('/users');
        $responseUsers->assertStatus(403);
    }

    public function test_staff_cannot_access_reports(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get('/reports');
        $response->assertStatus(403);
    }

    public function test_manager_can_access_reports_but_not_user_settings(): void
    {
        $this->actingAs($this->manager);

        $reportsResponse = $this->get('/reports');
        $reportsResponse->assertStatus(200);

        $usersResponse = $this->get('/users');
        $usersResponse->assertStatus(403);
    }

    public function test_admin_has_full_access(): void
    {
        $this->actingAs($this->admin);

        $this->get('/settings')->assertStatus(200);
        $this->get('/users')->assertStatus(200);
        $this->get('/reports')->assertStatus(200);
    }
}
