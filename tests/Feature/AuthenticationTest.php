<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $inactiveUser;

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

        $this->inactiveUser = User::create([
            'branch_id' => $branch->id,
            'name' => 'Inactive Staff',
            'email' => 'inactive@motorelease.ph',
            'password' => Hash::make('Password123!'),
            'role' => 'staff',
            'is_active' => false,
        ]);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('MotoRelease');
    }

    public function test_admin_successfully_logs_in(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@motorelease.ph',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_deactivated_staff_cannot_log_in(): void
    {
        $response = $this->post('/login', [
            'email' => 'inactive@motorelease.ph',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
