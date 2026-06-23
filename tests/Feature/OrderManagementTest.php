<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $cashierRole;

    private Branch $branch1;

    private Branch $branch2;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->cashierRole = Role::create(['name' => 'cashier']);

        $this->branch1 = Branch::create([
            'name' => 'Branch One',
            'code' => 'BR-01',
            'address' => 'Address One',
            'phone' => '11111',
        ]);

        $this->branch2 = Branch::create([
            'name' => 'Branch Two',
            'code' => 'BR-02',
            'address' => 'Address Two',
            'phone' => '22222',
        ]);

        $this->category = Category::create(['name' => 'Food']);
    }

    private function getAdminUser()
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => null, // Super admin context: can see all
        ]);
    }

    private function getCashierUser(Branch $branch)
    {
        return User::create([
            'name' => 'Cashier '.$branch->code,
            'email' => 'cashier_'.$branch->code.'@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $branch->id,
        ]);
    }

    public function test_guest_cannot_access_orders(): void
    {
        $this->get(route('cashier.orders'))->assertRedirect(route('login'));
        $this->get(route('admin.orders.index'))->assertRedirect(route('login'));
    }

    public function test_cashier_can_filter_orders_by_date_and_status(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

        // Order today
        $orderToday = Order::create([
            'branch_id' => $this->branch1->id,
            'table_number' => 1,
            'status' => 'pending',
            'total_amount' => 10000,
        ]);

        // Order yesterday
        $orderYesterday = Order::create([
            'branch_id' => $this->branch1->id,
            'table_number' => 2,
            'status' => 'confirmed',
            'total_amount' => 20000,
        ]);
        $orderYesterday->created_at = now()->subDay();
        $orderYesterday->save();

        $response = $this->actingAs($cashier)->get(route('cashier.orders', ['status' => 'pending']));
        $response->assertStatus(200);
        $response->assertSee('#'.$orderToday->id);
        $response->assertDontSee('#'.$orderYesterday->id);

        $responseDate = $this->actingAs($cashier)->get(route('cashier.orders', [
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->subDay()->format('Y-m-d'),
        ]));
        $responseDate->assertStatus(200);
        $responseDate->assertSee('#'.$orderYesterday->id);
        $responseDate->assertDontSee('#'.$orderToday->id);
    }

    public function test_cashier_can_update_order_status_sequence(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

        $order = Order::create([
            'branch_id' => $this->branch1->id,
            'table_number' => 1,
            'status' => 'confirmed', // starting sequence
            'total_amount' => 10000,
        ]);

        // 1. confirmed -> in_process
        $response = $this->actingAs($cashier)->patch(route('cashier.orders.update-status', $order->id), [
            'status' => 'in_process',
        ]);
        $response->assertRedirect(route('cashier.orders.show', $order->id));
        $this->assertEquals('in_process', $order->refresh()->status);

        // 2. in_process -> completed
        $response = $this->actingAs($cashier)->patch(route('cashier.orders.update-status', $order->id), [
            'status' => 'completed',
        ]);
        $response->assertRedirect(route('cashier.orders.show', $order->id));
        $this->assertEquals('completed', $order->refresh()->status);
    }

    public function test_invalid_status_transitions_are_rejected(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

        $order = Order::create([
            'branch_id' => $this->branch1->id,
            'table_number' => 1,
            'status' => 'confirmed',
            'total_amount' => 10000,
        ]);

        // Try transitioning directly to completed (violating: confirmed -> in_process -> completed)
        $response = $this->actingAs($cashier)->patch(route('cashier.orders.update-status', $order->id), [
            'status' => 'completed',
        ]);
        $response->assertSessionHas('error');
        $this->assertEquals('confirmed', $order->refresh()->status);
    }

    public function test_cancelling_order_restores_stock(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

        $stock = StockItem::create([
            'branch_id' => $this->branch1->id,
            'name' => 'Soft Drink',
            'quantity' => 10,
            'minimum_quantity' => 2,
            'unit' => 'can',
        ]);

        $menu = Menu::create([
            'name' => 'Drink Item',
            'category_id' => $this->category->id,
            'stock_item_id' => $stock->id,
            'price' => 5000,
            'is_active' => true,
        ]);

        $order = Order::create([
            'branch_id' => $this->branch1->id,
            'table_number' => 1,
            'status' => 'confirmed', // already paid, stock deducted
            'total_amount' => 5000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
            'price' => 5000,
            'subtotal' => 10000,
        ]);

        // Stock was 10. Let's say it's updated manually or deducted.
        // During pay it decrements. Let's make it 8 to simulate the state after pay.
        $stock->update(['quantity' => 8]);

        // Cancel order
        $response = $this->actingAs($cashier)->patch(route('cashier.orders.update-status', $order->id), [
            'status' => 'cancelled',
        ]);
        $response->assertRedirect(route('cashier.orders.show', $order->id));
        $this->assertEquals('cancelled', $order->refresh()->status);

        // Stock must be restored (8 + 2 = 10)
        $stock->refresh();
        $this->assertEquals(10, $stock->quantity);
    }

    public function test_observer_records_timeline_automatically(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

        // Creation of order should trigger Observer to write history record
        $order = Order::create([
            'branch_id' => $this->branch1->id,
            'table_number' => 1,
            'status' => 'pending',
            'total_amount' => 10000,
        ]);

        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        // Status update should trigger Observer to write another history record
        $this->actingAs($cashier)->patch(route('cashier.orders.update-status', $order->id), [
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'status' => 'cancelled',
            'user_id' => $cashier->id,
        ]);
    }

    public function test_admin_can_monitor_orders_globally_and_filter_by_branch(): void
    {
        $admin = $this->getAdminUser();

        // Create orders in branch 1 and branch 2
        $orderB1 = Order::create([
            'branch_id' => $this->branch1->id,
            'table_number' => 1,
            'status' => 'pending',
            'total_amount' => 10000,
        ]);

        $orderB2 = Order::create([
            'branch_id' => $this->branch2->id,
            'table_number' => 2,
            'status' => 'confirmed',
            'total_amount' => 20000,
        ]);

        // Admin checks global list
        $response = $this->actingAs($admin)->get(route('admin.orders.index'));
        $response->assertStatus(200);
        $response->assertSee('#'.$orderB1->id);
        $response->assertSee('#'.$orderB2->id);

        // Admin filters by Branch 2
        $responseFiltered = $this->actingAs($admin)->get(route('admin.orders.index', [
            'branch_id' => $this->branch2->id,
        ]));
        $responseFiltered->assertStatus(200);
        $responseFiltered->assertSee('#'.$orderB2->id);
        $responseFiltered->assertDontSee('#'.$orderB1->id);
    }
}
