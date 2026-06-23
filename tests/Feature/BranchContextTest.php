<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\User;
use App\Services\BranchContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchContextTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branchA;

    private Branch $branchB;

    private Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branchA = Branch::create([
            'name' => 'Branch A',
            'code' => 'BRA-01',
            'address' => 'Address A',
            'phone' => '1111',
        ]);

        $this->branchB = Branch::create([
            'name' => 'Branch B',
            'code' => 'BRB-01',
            'address' => 'Address B',
            'phone' => '2222',
        ]);

        $this->adminRole = Role::create(['name' => 'admin']);
    }

    /**
     * Test resolving branch context via customer URL.
     */
    public function test_customer_resolves_branch_context(): void
    {
        // Clear current context first to ensure clean state
        $reflection = new \ReflectionClass(BranchContext::class);
        $property = $reflection->getProperty('branch');
        $property->setAccessible(true);
        $property->setValue(app(BranchContext::class), null);

        $this->assertNull(app(BranchContext::class)->getBranchId());

        // Get customer menu for branch A
        $response = $this->get(route('customer.menu', ['branch_code' => 'bra-01', 'table_number' => 5]));
        $response->assertStatus(200);

        // BranchContext should now contain Branch A
        $this->assertEquals($this->branchA->id, app(BranchContext::class)->getBranchId());
    }

    /**
     * Test invalid branch code returns 404.
     */
    public function test_invalid_branch_code_returns_404(): void
    {
        $response = $this->get('/c/xyz-99/table/5');
        $response->assertStatus(404);
    }

    /**
     * Test resolving branch context via logged-in staff.
     */
    public function test_logged_in_staff_resolves_branch_context(): void
    {
        $admin = User::create([
            'name' => 'Admin A',
            'email' => 'admina@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $this->branchA->id,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        // BranchContext should now contain Branch A
        $this->assertEquals($this->branchA->id, app(BranchContext::class)->getBranchId());
    }

    /**
     * Test Super Admin (branch_id = null) has null branch context and bypasses scope.
     */
    public function test_super_admin_bypasses_branch_scoping(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => null, // Null indicates Super Admin
        ]);

        // Login as Super Admin
        $this->actingAs($superAdmin)->get('/dashboard');

        // BranchContext should be null
        $this->assertNull(app(BranchContext::class)->getBranchId());
    }

    /**
     * Test data isolation between branches.
     */
    public function test_branch_data_isolation(): void
    {
        // 1. Create orders for both branches
        // Temporarily bypass or specify branch_id directly during creation
        $orderA = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 1,
            'status' => 'pending',
            'total_amount' => 10000,
        ]);

        $orderB = Order::create([
            'branch_id' => $this->branchB->id,
            'table_number' => 2,
            'status' => 'pending',
            'total_amount' => 20000,
        ]);

        // 2. Query orders under Branch A context
        app(BranchContext::class)->setBranch($this->branchA);
        $ordersForA = Order::all();
        $this->assertCount(1, $ordersForA);
        $this->assertEquals($orderA->id, $ordersForA->first()->id);

        // 3. Query orders under Branch B context
        app(BranchContext::class)->setBranch($this->branchB);
        $ordersForB = Order::all();
        $this->assertCount(1, $ordersForB);
        $this->assertEquals($orderB->id, $ordersForB->first()->id);

        // 4. Query orders under Super Admin (Null) context
        // Set context to null (equivalent to Super Admin)
        $reflection = new \ReflectionClass(BranchContext::class);
        $property = $reflection->getProperty('branch');
        $property->setAccessible(true);
        $property->setValue(app(BranchContext::class), null);

        $allOrders = Order::all();
        $this->assertCount(2, $allOrders);
    }

    /**
     * Test automatic assignment of branch_id when creating models.
     */
    public function test_automatic_branch_id_assignment(): void
    {
        // Set Branch A context
        app(BranchContext::class)->setBranch($this->branchA);

        // Create a model without specifying branch_id
        $order = Order::create([
            'table_number' => 10,
            'status' => 'pending',
            'total_amount' => 15000,
        ]);

        // It should automatically set branch_id
        $this->assertEquals($this->branchA->id, $order->branch_id);

        // Create a StockItem
        $stock = StockItem::create([
            'name' => 'Bakso',
            'quantity' => 100,
            'minimum_quantity' => 10,
            'unit' => 'pcs',
        ]);

        $this->assertEquals($this->branchA->id, $stock->branch_id);
    }

    /**
     * Test indirect data isolation (Payments & Receipts).
     */
    public function test_indirect_data_isolation(): void
    {
        // Create orders
        $orderA = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 1,
            'status' => 'pending',
            'total_amount' => 10000,
        ]);

        $orderB = Order::create([
            'branch_id' => $this->branchB->id,
            'table_number' => 2,
            'status' => 'pending',
            'total_amount' => 20000,
        ]);

        // Create payments
        $paymentA = Payment::create([
            'order_id' => $orderA->id,
            'amount' => 10000,
            'method' => 'cash',
            'status' => 'success',
        ]);

        $paymentB = Payment::create([
            'order_id' => $orderB->id,
            'amount' => 20000,
            'method' => 'qris',
            'status' => 'success',
        ]);

        // Create receipts
        $receiptA = Receipt::create([
            'payment_id' => $paymentA->id,
            'receipt_number' => 'REC-A',
            'printed_at' => now(),
        ]);

        $receiptB = Receipt::create([
            'payment_id' => $paymentB->id,
            'receipt_number' => 'REC-B',
            'printed_at' => now(),
        ]);

        // 1. Scoped to Branch A
        app(BranchContext::class)->setBranch($this->branchA);
        $payments = Payment::all();
        $receipts = Receipt::all();

        $this->assertCount(1, $payments);
        $this->assertEquals($paymentA->id, $payments->first()->id);

        $this->assertCount(1, $receipts);
        $this->assertEquals($receiptA->id, $receipts->first()->id);

        // 2. Scoped to Branch B
        app(BranchContext::class)->setBranch($this->branchB);
        $payments = Payment::all();
        $receipts = Receipt::all();

        $this->assertCount(1, $payments);
        $this->assertEquals($paymentB->id, $payments->first()->id);

        $this->assertCount(1, $receipts);
        $this->assertEquals($receiptB->id, $receipts->first()->id);

        // 3. Super Admin (Null) Context
        $reflection = new \ReflectionClass(BranchContext::class);
        $property = $reflection->getProperty('branch');
        $property->setAccessible(true);
        $property->setValue(app(BranchContext::class), null);

        $this->assertCount(2, Payment::all());
        $this->assertCount(2, Receipt::all());
    }
}
