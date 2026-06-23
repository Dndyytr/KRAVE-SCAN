<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionManagementTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $cashierRole;

    private Branch $branch1;

    private Branch $branch2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->cashierRole = Role::create(['name' => 'cashier']);

        $this->branch1 = Branch::create([
            'name' => 'Krave JKT',
            'code' => 'JKT-01',
            'address' => 'Jakarta',
            'phone' => '12345',
        ]);

        $this->branch2 = Branch::create([
            'name' => 'Krave BDG',
            'code' => 'BDG-01',
            'address' => 'Bandung',
            'phone' => '54321',
        ]);
    }

    private function getSuperAdmin()
    {
        return User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => null,
        ]);
    }

    private function getBranchAdmin(Branch $branch)
    {
        return User::create([
            'name' => 'Branch Admin '.$branch->code,
            'email' => 'admin_'.$branch->code.'@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $branch->id,
        ]);
    }

    private function getCashier(Branch $branch)
    {
        return User::create([
            'name' => 'Cashier '.$branch->code,
            'email' => 'cashier_'.$branch->code.'@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $branch->id,
        ]);
    }

    public function test_guest_and_cashier_cannot_access_transactions(): void
    {
        // Guests redirect to login
        $this->get(route('admin.transactions.index'))->assertRedirect(route('login'));

        // Cashiers get 403
        $cashier = $this->getCashier($this->branch1);
        $this->actingAs($cashier)->get(route('admin.transactions.index'))->assertStatus(403);
    }

    public function test_super_admin_can_access_transactions_globally_with_aggregates(): void
    {
        $superAdmin = $this->getSuperAdmin();

        // Create orders
        $order1 = Order::create(['branch_id' => $this->branch1->id, 'table_number' => 1, 'status' => 'completed', 'total_amount' => 50000]);
        $order2 = Order::create(['branch_id' => $this->branch2->id, 'table_number' => 2, 'status' => 'completed', 'total_amount' => 100000]);

        // Create success payments (revenue)
        $p1 = Payment::create(['order_id' => $order1->id, 'amount' => 50000, 'method' => 'cash', 'status' => 'success']);
        $p2 = Payment::create(['order_id' => $order2->id, 'amount' => 100000, 'method' => 'qris', 'status' => 'success']);
        // Failed payment (should not count in aggregates)
        $p3 = Payment::create(['order_id' => $order1->id, 'amount' => 30000, 'method' => 'cash', 'status' => 'failed']);

        $response = $this->actingAs($superAdmin)->get(route('admin.transactions.index'));

        $response->assertStatus(200);
        // Assert all transactions are visible
        $response->assertSee('Rp 50.000');
        $response->assertSee('Rp 100.000');
        $response->assertSee('Rp 30.000');

        // Assert aggregates (total = 150000, cash = 50000, qris = 100000)
        $response->assertSee('Rp 150.000'); // total success revenue
        $response->assertSee('Rp 50.000');  // total success cash
        $response->assertSee('Rp 100.000'); // total success qris
    }

    public function test_branch_admin_is_restricted_by_branch_scope(): void
    {
        $adminJKT = $this->getBranchAdmin($this->branch1);

        $orderJKT = Order::create(['branch_id' => $this->branch1->id, 'table_number' => 1, 'status' => 'completed', 'total_amount' => 50000]);
        $orderBDG = Order::create(['branch_id' => $this->branch2->id, 'table_number' => 2, 'status' => 'completed', 'total_amount' => 100000]);

        Payment::create(['order_id' => $orderJKT->id, 'amount' => 50000, 'method' => 'cash', 'status' => 'success']);
        Payment::create(['order_id' => $orderBDG->id, 'amount' => 100000, 'method' => 'qris', 'status' => 'success']);

        // Access as JKT Branch Admin
        $response = $this->actingAs($adminJKT)->get(route('admin.transactions.index'));

        $response->assertStatus(200);
        $response->assertSee('Rp 50.000');
        $response->assertDontSee('Rp 100.000');

        // Aggregates should only count Branch 1
        $response->assertSee('Rp 50.000'); // total revenue for JKT branch
        $response->assertDontSee('Rp 150.000');
    }

    public function test_transactions_can_be_filtered_by_method_status_and_date(): void
    {
        $superAdmin = $this->getSuperAdmin();

        $order1 = Order::create(['branch_id' => $this->branch1->id, 'table_number' => 1, 'status' => 'completed', 'total_amount' => 50000]);
        $order2 = Order::create(['branch_id' => $this->branch1->id, 'table_number' => 2, 'status' => 'completed', 'total_amount' => 100000]);

        $p1 = Payment::create(['order_id' => $order1->id, 'amount' => 50000, 'method' => 'cash', 'status' => 'success']);
        $p2 = Payment::create(['order_id' => $order2->id, 'amount' => 100000, 'method' => 'qris', 'status' => 'success']);
        $p3 = Payment::create(['order_id' => $order1->id, 'amount' => 30000, 'method' => 'cash', 'status' => 'pending']);

        // 1. Filter by method = cash
        $response = $this->actingAs($superAdmin)->get(route('admin.transactions.index', ['method' => 'cash']));
        $response->assertStatus(200);
        $response->assertSee('Rp 50.000');
        $response->assertSee('Rp 30.000');
        $response->assertDontSee('Rp 100.000');

        // Aggregates for cash success should be 50,000 (pending is excluded)
        $response->assertSee('Rp 50.000');

        // 2. Filter by status = pending
        $responseStatus = $this->actingAs($superAdmin)->get(route('admin.transactions.index', ['status' => 'pending']));
        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('Rp 30.000');
        $responseStatus->assertDontSee('Rp 50.000');
        $responseStatus->assertDontSee('Rp 100.000');

        // 3. Filter by date range (yesterday should exclude today's payments)
        $yesterday = now()->subDay()->format('Y-m-d');
        $responseDate = $this->actingAs($superAdmin)->get(route('admin.transactions.index', [
            'start_date' => $yesterday,
            'end_date' => $yesterday,
        ]));
        $responseDate->assertStatus(200);
        $responseDate->assertDontSee('Rp 50.000');
        $responseDate->assertDontSee('Rp 100.000');
        $responseDate->assertDontSee('Rp 30.000');
    }
}
