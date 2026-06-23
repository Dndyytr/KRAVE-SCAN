<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $cashierRole;

    private Branch $branchJakarta;

    private Branch $branchBandung;

    private User $jakartaAdmin;

    private User $jakartaCashier;

    private User $bandungCashier;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Roles
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->cashierRole = Role::create(['name' => 'cashier']);

        // Create Branches
        $this->branchJakarta = Branch::create([
            'name' => 'Krave Scan Jakarta',
            'code' => 'JKT-01',
            'address' => 'Jakarta Sudirman',
            'phone' => '021-111111',
        ]);

        $this->branchBandung = Branch::create([
            'name' => 'Krave Scan Bandung',
            'code' => 'BDG-01',
            'address' => 'Bandung Dago',
            'phone' => '022-222222',
        ]);

        // Create Users
        $this->jakartaAdmin = User::create([
            'name' => 'Jakarta Admin',
            'email' => 'admin.jkt@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $this->branchJakarta->id,
        ]);

        $this->jakartaCashier = User::create([
            'name' => 'Jakarta Cashier',
            'email' => 'cashier.jkt@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $this->branchJakarta->id,
        ]);

        $this->bandungCashier = User::create([
            'name' => 'Bandung Cashier',
            'email' => 'cashier.bdg@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $this->branchBandung->id,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin HQ',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => null, // Null branch_id for Super Admin
        ]);
    }

    /**
     * Guest user cannot access dashboard.
     */
    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect('/login');
    }

    /**
     * Cashier can access dashboard.
     */
    public function test_cashier_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->jakartaCashier)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    /**
     * Admin can access dashboard.
     */
    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->jakartaAdmin)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    /**
     * Test metrics calculation and data isolation between branches.
     */
    public function test_dashboard_metrics_are_correctly_isolated_by_branch(): void
    {
        // 1. Setup Jakarta Data
        // Order 1 (Jakarta): Today, Completed/Confirmed, paid
        $orderJkt1 = Order::withoutEvents(function () {
            return Order::create([
                'branch_id' => $this->branchJakarta->id,
                'table_number' => 1,
                'status' => 'confirmed',
                'total_amount' => 50000.00,
                'created_at' => now(),
            ]);
        });
        Payment::withoutEvents(function () use ($orderJkt1) {
            return Payment::create([
                'order_id' => $orderJkt1->id,
                'amount' => 50000.00,
                'method' => 'qris',
                'status' => 'success',
                'created_at' => now(),
            ]);
        });

        // Order 2 (Jakarta): Today, Pending, unpaid
        Order::withoutEvents(function () {
            return Order::create([
                'branch_id' => $this->branchJakarta->id,
                'table_number' => 2,
                'status' => 'pending',
                'total_amount' => 30000.00,
                'created_at' => now(),
            ]);
        });

        // Stock 1 (Jakarta): Low stock
        StockItem::withoutEvents(function () {
            return StockItem::create([
                'branch_id' => $this->branchJakarta->id,
                'name' => 'Kopi Susu Jakarta',
                'quantity' => 2,
                'minimum_quantity' => 10,
                'unit' => 'botol',
            ]);
        });

        // Stock 2 (Jakarta): Normal stock
        StockItem::withoutEvents(function () {
            return StockItem::create([
                'branch_id' => $this->branchJakarta->id,
                'name' => 'Gula Pasir Jakarta',
                'quantity' => 50,
                'minimum_quantity' => 10,
                'unit' => 'kg',
            ]);
        });

        // 2. Setup Bandung Data
        // Order 3 (Bandung): Today, Confirmed, paid
        $orderBdg1 = Order::withoutEvents(function () {
            return Order::create([
                'branch_id' => $this->branchBandung->id,
                'table_number' => 1,
                'status' => 'confirmed',
                'total_amount' => 75000.00,
                'created_at' => now(),
            ]);
        });
        Payment::withoutEvents(function () use ($orderBdg1) {
            return Payment::create([
                'order_id' => $orderBdg1->id,
                'amount' => 75000.00,
                'method' => 'cash',
                'status' => 'success',
                'created_at' => now(),
            ]);
        });

        // Stock 3 (Bandung): Low stock
        StockItem::withoutEvents(function () {
            return StockItem::create([
                'branch_id' => $this->branchBandung->id,
                'name' => 'Susu UHT Bandung',
                'quantity' => 1,
                'minimum_quantity' => 5,
                'unit' => 'liter',
            ]);
        });

        // --- Verify Jakarta Cashier View ---
        $responseJkt = $this->actingAs($this->jakartaCashier)->get(route('dashboard'));
        $responseJkt->assertStatus(200);

        // Jakarta should see:
        // - 2 orders today (order 1 & order 2)
        // - Rp 50,000 revenue (from order 1 payment)
        // - 1 pending order (order 2)
        // - 1 low stock warning (Kopi Susu)
        $responseJkt->assertViewHas('todayOrdersCount', 2);
        $responseJkt->assertViewHas('todayRevenue', 50000.00);
        $responseJkt->assertViewHas('pendingOrdersCount', 1);
        $responseJkt->assertViewHas('lowStockCount', 1);

        // --- Verify Bandung Cashier View ---
        $responseBdg = $this->actingAs($this->bandungCashier)->get(route('dashboard'));
        $responseBdg->assertStatus(200);

        // Bandung should see:
        // - 1 order today (order 3)
        // - Rp 75,000 revenue (from order 3 payment)
        // - 0 pending orders
        // - 1 low stock warning (Susu UHT)
        $responseBdg->assertViewHas('todayOrdersCount', 1);
        $responseBdg->assertViewHas('todayRevenue', 75000.00);
        $responseBdg->assertViewHas('pendingOrdersCount', 0);
        $responseBdg->assertViewHas('lowStockCount', 1);

        // --- Verify Super Admin View (Cumulative across branches) ---
        $responseSuper = $this->actingAs($this->superAdmin)->get(route('dashboard'));
        $responseSuper->assertStatus(200);

        // Super Admin should see all:
        // - 3 orders total today
        // - Rp 125,000 revenue total (50k + 75k)
        // - 1 pending order total (order 2)
        // - 2 low stock warnings total (Kopi Susu & Susu UHT)
        $responseSuper->assertViewHas('todayOrdersCount', 3);
        $responseSuper->assertViewHas('todayRevenue', 125000.00);
        $responseSuper->assertViewHas('pendingOrdersCount', 1);
        $responseSuper->assertViewHas('lowStockCount', 2);
    }
}
