<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderingTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;

    private Branch $otherBranch;

    private Category $category;

    private Menu $activeMenu;

    private Menu $inactiveMenu;

    protected function setUp(): void
    {
        parent::setUp();

        // Create branches
        $this->branch = Branch::create([
            'name' => 'Krave Scan Jakarta',
            'code' => 'JKT-01',
            'address' => 'Sudirman Jakarta',
            'phone' => '021-123456',
        ]);

        $this->otherBranch = Branch::create([
            'name' => 'Krave Scan Bandung',
            'code' => 'BDG-01',
            'address' => 'Dago Bandung',
            'phone' => '022-654321',
        ]);

        // Create category and menus
        $this->category = Category::create(['name' => 'Food']);

        $this->activeMenu = Menu::create([
            'category_id' => $this->category->id,
            'name' => 'Bakso Cinta Spesial',
            'description' => 'Bakso berbentuk cinta dengan kuah gurih berkaldu asli.',
            'price' => 25000.00,
            'is_active' => true,
        ]);

        $this->inactiveMenu = Menu::create([
            'category_id' => $this->category->id,
            'name' => 'Bakso Petir Ciamis',
            'description' => 'Bakso super pedas cabai rawit merah.',
            'price' => 28000.00,
            'is_active' => false,
        ]);
    }

    /**
     * Test scanning QR sets table number in session and shows menu.
     */
    public function test_customer_scans_qr_sets_table_and_displays_menu(): void
    {
        $response = $this->get(route('customer.menu', [
            'branch_code' => 'jkt-01',
            'table_number' => 12,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('customers.menu');
        $response->assertViewHasAll(['branch', 'table', 'categories', 'menus']);

        $this->assertEquals(12, session('table_number'));
    }

    /**
     * Test adding active menu item to cart.
     */
    public function test_customer_can_add_active_menu_to_cart(): void
    {
        // Go to menu first to setup session/context
        $this->get(route('customer.menu', ['branch_code' => 'jkt-01', 'table_number' => 5]));

        $response = $this->postJson(route('customer.cart.add', ['branch_code' => 'jkt-01']), [
            'menu_id' => $this->activeMenu->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cart_count' => 2,
        ]);

        $cart = session('cart');
        $this->assertNotNull($cart);
        $this->assertArrayHasKey($this->activeMenu->id, $cart);
        $this->assertEquals(2, $cart[$this->activeMenu->id]['quantity']);
    }

    /**
     * Test adding inactive menu item to cart fails.
     */
    public function test_customer_cannot_add_inactive_menu_to_cart(): void
    {
        $this->get(route('customer.menu', ['branch_code' => 'jkt-01', 'table_number' => 5]));

        $response = $this->postJson(route('customer.cart.add', ['branch_code' => 'jkt-01']), [
            'menu_id' => $this->inactiveMenu->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(404);
        $this->assertNull(session('cart'));
    }

    /**
     * Test updating cart item quantities.
     */
    public function test_customer_can_update_cart_quantities(): void
    {
        // Put initial item in cart session
        session(['cart' => [
            $this->activeMenu->id => [
                'id' => $this->activeMenu->id,
                'name' => $this->activeMenu->name,
                'price' => (float) $this->activeMenu->price,
                'quantity' => 2,
                'image_path' => null,
            ],
        ]]);

        // Update quantity to 4
        $response = $this->postJson(route('customer.cart.update', ['branch_code' => 'jkt-01']), [
            'menu_id' => $this->activeMenu->id,
            'quantity' => 4,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cart_count' => 4,
            'item_subtotal' => 100000.00,
            'cart_total' => 100000.00,
            'cart_empty' => false,
        ]);

        $this->assertEquals(4, session('cart')[$this->activeMenu->id]['quantity']);

        // Update quantity to 0 (should remove the item)
        $response = $this->postJson(route('customer.cart.update', ['branch_code' => 'jkt-01']), [
            'menu_id' => $this->activeMenu->id,
            'quantity' => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cart_count' => 0,
            'item_subtotal' => 0,
            'cart_total' => 0,
            'cart_empty' => true,
        ]);

        $this->assertEmpty(session('cart'));
    }

    /**
     * Test checkout creates order with auto-assigned branch and clears cart.
     */
    public function test_checkout_creates_order_and_clears_cart(): void
    {
        // Setup session cart & table number
        session([
            'table_number' => 8,
            'cart' => [
                $this->activeMenu->id => [
                    'id' => $this->activeMenu->id,
                    'name' => $this->activeMenu->name,
                    'price' => (float) $this->activeMenu->price,
                    'quantity' => 3,
                    'image_path' => null,
                ],
            ],
        ]);

        // Resolve branch context via middleware by running a request
        $response = $this->post(route('customer.checkout', ['branch_code' => 'jkt-01']));

        // Should redirect to order status
        $response->assertRedirect();

        // Assert order was created in DB
        $this->assertDatabaseHas('orders', [
            'table_number' => '8',
            'status' => 'pending',
            'total_amount' => 75000.00,
        ]);

        // Fetch created order
        // Bypass global scoping for verifying correct branch assignment
        $order = Order::withoutGlobalScopes()->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals($this->branch->id, $order->branch_id);

        // Assert order items were created
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'menu_id' => $this->activeMenu->id,
            'quantity' => 3,
            'price' => 25000.00,
            'subtotal' => 75000.00,
        ]);

        // Assert session cart is cleared
        $this->assertNull(session('cart'));

        // Assert latest_order_id was saved to session
        $this->assertEquals($order->id, session('latest_order_id'));

        // Check redirection goes to order status page
        $response->assertRedirect(route('customer.order.status', [
            'branch_code' => 'jkt-01',
            'order' => $order->id,
        ]));
    }

    /**
     * Test checkout fails if cart is empty.
     */
    public function test_checkout_fails_if_cart_is_empty(): void
    {
        session(['table_number' => 5]);

        $response = $this->post(route('customer.checkout', ['branch_code' => 'jkt-01']));
        $response->assertRedirect(route('customer.cart', ['branch_code' => 'jkt-01']));
        $response->assertSessionHas('error');

        $this->assertEquals(0, Order::count());
    }

    /**
     * Test checkout fails if table number is missing.
     */
    public function test_checkout_fails_if_table_number_missing(): void
    {
        session(['cart' => [
            $this->activeMenu->id => [
                'id' => $this->activeMenu->id,
                'name' => $this->activeMenu->name,
                'price' => (float) $this->activeMenu->price,
                'quantity' => 1,
                'image_path' => null,
            ],
        ]]);

        $response = $this->post(route('customer.checkout', ['branch_code' => 'jkt-01']));
        $response->assertRedirect(route('customer.cart', ['branch_code' => 'jkt-01']));
        $response->assertSessionHas('error');

        $this->assertEquals(0, Order::count());
    }

    /**
     * Test branch isolation on status page (preventing cross-branch order viewing).
     */
    public function test_branch_isolation_on_order_status_page(): void
    {
        // Create an order for branch JKT-01 (using bypass to create explicitly)
        $order = Order::withoutEvents(function () {
            return Order::create([
                'branch_id' => $this->branch->id,
                'table_number' => '10',
                'status' => 'pending',
                'total_amount' => 50000.00,
            ]);
        });

        // Requesting status under Jakarta (JKT-01) branch context should succeed
        $response = $this->get(route('customer.order.status', [
            'branch_code' => 'jkt-01',
            'order' => $order->id,
        ]));
        $response->assertStatus(200);
        $response->assertViewIs('customers.status');

        // Requesting status under Bandung (BDG-01) branch context should fail (404)
        $response = $this->get(route('customer.order.status', [
            'branch_code' => 'bdg-01',
            'order' => $order->id,
        ]));
        $response->assertStatus(404);
    }
}
