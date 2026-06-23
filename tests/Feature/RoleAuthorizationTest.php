<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $cashierRole;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat data dasar untuk testing
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->cashierRole = Role::create(['name' => 'cashier']);
        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'code' => 'TEST-01',
            'address' => 'Test Address',
            'phone' => '1234567890',
        ]);
    }

    /**
     * Tamu tanpa sesi aktif dilempar kembali ke halaman login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/admin/menus')->assertRedirect(route('login'));
        $this->get('/cashier/orders')->assertRedirect(route('login'));
    }

    /**
     * Admin dapat mengakses halaman dasbor.
     */
    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Cashier dapat mengakses halaman dasbor.
     */
    public function test_cashier_can_access_dashboard(): void
    {
        $cashier = User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($cashier)->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Cashier dilarang mengakses rute admin (HTTP 403).
     */
    public function test_cashier_cannot_access_admin_routes(): void
    {
        $cashier = User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($cashier)->get('/admin/menus');

        $response->assertStatus(403);
    }

    /**
     * Admin dilarang mengakses rute kasir (HTTP 403) sesuai permintaan user.
     */
    public function test_admin_cannot_access_cashier_routes(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($admin)->get('/cashier/orders');

        $response->assertStatus(403);
    }
}
