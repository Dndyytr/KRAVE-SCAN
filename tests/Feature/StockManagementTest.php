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

class StockManagementTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $cashierRole;

    private Branch $branch;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->cashierRole = Role::create(['name' => 'cashier']);
        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'code' => 'TEST-01',
            'address' => 'Test Address',
            'phone' => '1234567890',
        ]);
        $this->category = Category::create(['name' => 'Beverage']);
    }

    private function getAdminUser()
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $this->branch->id,
        ]);
    }

    private function getCashierUser()
    {
        return User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $this->branch->id,
        ]);
    }

    public function test_guest_cannot_access_stocks_crud(): void
    {
        $this->get(route('admin.stocks.index'))->assertRedirect(route('login'));
        $this->get(route('admin.stocks.create'))->assertRedirect(route('login'));
        $this->post(route('admin.stocks.store'), [])->assertRedirect(route('login'));
    }

    public function test_cashier_cannot_access_stocks_crud(): void
    {
        $cashier = $this->getCashierUser();

        $this->actingAs($cashier)->get(route('admin.stocks.index'))->assertStatus(403);
        $this->actingAs($cashier)->get(route('admin.stocks.create'))->assertStatus(403);
        $this->actingAs($cashier)->post(route('admin.stocks.store'), [])->assertStatus(403);
    }

    public function test_admin_can_access_stocks_index(): void
    {
        $admin = $this->getAdminUser();

        // Branch context must be set when creating because of trait ScopedToBranch
        $stock = StockItem::create([
            'branch_id' => $this->branch->id,
            'name' => 'Test Coffee beans',
            'quantity' => 10,
            'minimum_quantity' => 2,
            'unit' => 'kg',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.stocks.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Coffee beans');
    }

    public function test_admin_can_create_stock_item(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.stocks.store'), [
            'name' => 'Cup 12oz',
            'quantity' => 100,
            'minimum_quantity' => 10,
            'unit' => 'pcs',
        ]);

        $response->assertRedirect(route('admin.stocks.index'));

        $this->assertDatabaseHas('stock_items', [
            'branch_id' => $this->branch->id,
            'name' => 'Cup 12oz',
            'quantity' => 100,
            'minimum_quantity' => 10,
            'unit' => 'pcs',
        ]);
    }

    public function test_admin_can_update_stock_item(): void
    {
        $admin = $this->getAdminUser();
        $stock = StockItem::create([
            'branch_id' => $this->branch->id,
            'name' => 'Cup 12oz',
            'quantity' => 100,
            'minimum_quantity' => 10,
            'unit' => 'pcs',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.stocks.update', $stock->id), [
            'name' => 'Cup 14oz Updated',
            'quantity' => 80,
            'minimum_quantity' => 5,
            'unit' => 'pcs',
        ]);

        $response->assertRedirect(route('admin.stocks.index'));

        $stock->refresh();
        $this->assertEquals('Cup 14oz Updated', $stock->name);
        $this->assertEquals(80, $stock->quantity);
        $this->assertEquals(5, $stock->minimum_quantity);
    }

    public function test_admin_can_delete_stock_item(): void
    {
        $admin = $this->getAdminUser();
        $stock = StockItem::create([
            'branch_id' => $this->branch->id,
            'name' => 'Cup 12oz',
            'quantity' => 100,
            'minimum_quantity' => 10,
            'unit' => 'pcs',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.stocks.destroy', $stock->id));

        $response->assertRedirect(route('admin.stocks.index'));
        $this->assertDatabaseMissing('stock_items', ['id' => $stock->id]);
    }

    public function test_admin_can_create_menu_linked_to_stock(): void
    {
        $admin = $this->getAdminUser();
        $stock = StockItem::create([
            'branch_id' => $this->branch->id,
            'name' => 'Biji Kopi Arabika',
            'quantity' => 50,
            'minimum_quantity' => 5,
            'unit' => 'kg',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.menus.store'), [
            'name' => 'Espresso Classic Linked',
            'category_id' => $this->category->id,
            'stock_item_id' => $stock->id,
            'price' => 15000,
            'description' => 'A classic double espresso shot linked to stock',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.menus.index'));

        $this->assertDatabaseHas('menus', [
            'name' => 'Espresso Classic Linked',
            'stock_item_id' => $stock->id,
        ]);
    }

    public function test_stock_decrements_automatically_when_payment_is_confirmed(): void
    {
        $cashier = $this->getCashierUser();

        // 1. Create Stock Item
        $stock = StockItem::create([
            'branch_id' => $this->branch->id,
            'name' => 'Botol Coca Cola',
            'quantity' => 20,
            'minimum_quantity' => 5,
            'unit' => 'botol',
        ]);

        // 2. Create Menu linked to stock
        $menu = Menu::create([
            'name' => 'Coca Cola Dingin',
            'category_id' => $this->category->id,
            'stock_item_id' => $stock->id,
            'price' => 8000,
            'is_active' => true,
        ]);

        // 3. Create Order
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'table_number' => 3,
            'status' => 'pending',
            'total_amount' => 16000,
        ]);

        // 4. Create Order Item (Quantity = 2)
        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
            'price' => 8000,
            'subtotal' => 16000,
        ]);

        // 5. Process Payment as Cashier (Transition to 'confirmed')
        $response = $this->actingAs($cashier)->post(route('cashier.orders.payment', $order->id), [
            'payment_method' => 'cash',
            'amount_paid' => 20000,
        ]);

        $response->assertRedirect();

        // 6. Assert Stock item decremented (20 - 2 = 18)
        $stock->refresh();
        $this->assertEquals(18, $stock->quantity);
    }
}
