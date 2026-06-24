<?php

namespace Tests\Feature;

use App\Jobs\CheckStockLevelsJob;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\User;
use App\Services\BranchContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BranchContextJobTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branchA;

    protected Branch $branchB;

    protected User $superAdmin;

    protected User $branchAdminA;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => 'admin']);

        $this->branchA = Branch::create([
            'name' => 'Branch A',
            'code' => 'BRA-01',
            'address' => 'Street A',
            'phone' => '123',
        ]);

        $this->branchB = Branch::create([
            'name' => 'Branch B',
            'code' => 'BRB-02',
            'address' => 'Street B',
            'phone' => '456',
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@krave.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'branch_id' => null,
        ]);

        $this->branchAdminA = User::create([
            'name' => 'Branch Admin A',
            'email' => 'admina@krave.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'branch_id' => $this->branchA->id,
        ]);
    }

    protected function tearDown(): void
    {
        app(BranchContext::class)->setBranch(null);
        parent::tearDown();
    }

    /**
     * Test Category and Menu branch isolation.
     */
    public function test_category_and_menu_branch_isolation(): void
    {
        // Set context to Branch A
        app(BranchContext::class)->setBranch($this->branchA);

        $categoryA = Category::create(['name' => 'Drinks']);
        $menuA = Menu::create([
            'category_id' => $categoryA->id,
            'name' => 'Espresso A',
            'price' => 15000,
        ]);

        $this->assertEquals($this->branchA->id, $categoryA->branch_id);
        $this->assertEquals($this->branchA->id, $menuA->branch_id);

        // Set context to Branch B
        app(BranchContext::class)->setBranch($this->branchB);

        $categoryB = Category::create(['name' => 'Foods']);
        $menuB = Menu::create([
            'category_id' => $categoryB->id,
            'name' => 'Burger B',
            'price' => 30000,
        ]);

        $this->assertEquals($this->branchB->id, $categoryB->branch_id);
        $this->assertEquals($this->branchB->id, $menuB->branch_id);

        // Under Branch B context, Category A and Menu A should not be visible
        $this->assertCount(1, Category::all());
        $this->assertCount(1, Menu::all());
        $this->assertEquals('Foods', Category::first()->name);
        $this->assertEquals('Burger B', Menu::first()->name);

        // Reset Context to null (Super Admin) - all should be visible
        app(BranchContext::class)->setBranch(null);
        $this->assertCount(2, Category::all());
        $this->assertCount(2, Menu::all());
    }

    /**
     * Test that CheckStockLevelsJob preserves and sets branch context in queue worker.
     */
    public function test_job_preserves_and_sets_branch_context(): void
    {
        // 1. Setup inventory & order under Branch A context
        app(BranchContext::class)->setBranch($this->branchA);

        $stockItemA = StockItem::create([
            'name' => 'Coffee Beans',
            'quantity' => 2,
            'minimum_quantity' => 5, // trigger low stock
            'unit' => 'kg',
        ]);

        $category = Category::create(['name' => 'Coffee']);
        $menu = Menu::create([
            'category_id' => $category->id,
            'stock_item_id' => $stockItemA->id,
            'name' => 'Espresso',
            'price' => 15000,
        ]);

        $order = Order::create([
            'table_number' => '5',
            'status' => 'pending',
            'total_amount' => 15000,
        ]);
        $order->orderItems()->create([
            'menu_id' => $menu->id,
            'quantity' => 1,
            'price' => 15000,
            'subtotal' => 15000,
        ]);

        // 2. Setup standard inventory under Branch B context to make sure the job doesn't mix them up
        app(BranchContext::class)->setBranch($this->branchB);

        $stockItemB = StockItem::create([
            'name' => 'Coffee Beans',
            'quantity' => 10,
            'minimum_quantity' => 5, // sufficient stock
            'unit' => 'kg',
        ]);

        // Reset branch context to Bypassed (like standard Queue environment)
        app(BranchContext::class)->setBranch(null);

        // Run job directly (recreating worker environment)
        $job = new CheckStockLevelsJob($order);
        $job->handle();

        // The job should have set BranchContext to Branch A,
        // so it found stockItemA (quantity <= minimum_quantity)
        // and created an AutomationLog for Branch A.
        app(BranchContext::class)->setBranch($this->branchA);
        $this->assertDatabaseHas('automation_logs', [
            'branch_id' => $this->branchA->id,
            'status' => 'warning',
        ]);

        app(BranchContext::class)->setBranch($this->branchB);
        $this->assertDatabaseMissing('automation_logs', [
            'branch_id' => $this->branchB->id,
        ]);
    }

    /**
     * Test Super Admin branch switching session flow.
     */
    public function test_super_admin_branch_switcher(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Initial request to dashboard without active branch - should run in null context
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $this->assertNull(app(BranchContext::class)->getBranchId());

        // 2. Post to switch branch to Branch A
        $response = $this->post(route('admin.switch-branch'), [
            'branch_id' => $this->branchA->id,
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('active_branch_id', $this->branchA->id);

        // Trigger dashboard and check middleware sets context
        $this->get(route('dashboard'))
            ->assertSessionHas('active_branch_id', $this->branchA->id);

        $this->assertEquals($this->branchA->id, app(BranchContext::class)->getBranchId());

        // 3. Switch back to all branches
        $response = $this->post(route('admin.switch-branch'), [
            'branch_id' => '',
        ]);
        $response->assertRedirect();
        $response->assertSessionMissing('active_branch_id');
    }
}
