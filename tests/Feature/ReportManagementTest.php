<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportManagementTest extends TestCase
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
            'name' => 'Krave Jakarta',
            'code' => 'JKT-01',
            'address' => 'Jakarta',
            'phone' => '12345',
        ]);

        $this->branch2 = Branch::create([
            'name' => 'Krave Bandung',
            'code' => 'BDG-01',
            'address' => 'Bandung',
            'phone' => '54321',
        ]);

        $this->category = Category::create([
            'name' => 'Makanan Utama',
            'slug' => 'makanan-utama',
        ]);
    }

    private function getSuperAdmin(): User
    {
        return User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => null,
            'is_active' => true,
        ]);
    }

    private function getBranchAdmin(Branch $branch): User
    {
        return User::create([
            'name' => 'Branch Admin '.$branch->code,
            'email' => 'admin_'.$branch->code.'@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);
    }

    private function getCashier(Branch $branch): User
    {
        return User::create([
            'name' => 'Cashier '.$branch->code,
            'email' => 'cashier_'.$branch->code.'@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);
    }

    public function test_guests_and_cashiers_cannot_access_reports(): void
    {
        // Guests redirect to login
        $this->get(route('admin.reports.sales'))->assertRedirect(route('login'));
        $this->get(route('admin.reports.menus'))->assertRedirect(route('login'));
        $this->get(route('admin.reports.payments'))->assertRedirect(route('login'));

        // Cashiers get 403
        $cashier = $this->getCashier($this->branch1);
        $this->actingAs($cashier)->get(route('admin.reports.sales'))->assertStatus(403);
        $this->actingAs($cashier)->get(route('admin.reports.menus'))->assertStatus(403);
        $this->actingAs($cashier)->get(route('admin.reports.payments'))->assertStatus(403);
    }

    public function test_super_admin_can_access_reports_globally_and_filter_by_branch(): void
    {
        $superAdmin = $this->getSuperAdmin();

        // Create orders and payments
        $order1 = Order::create(['branch_id' => $this->branch1->id, 'table_number' => 1, 'status' => 'completed', 'total_amount' => 50000]);
        $order2 = Order::create(['branch_id' => $this->branch2->id, 'table_number' => 2, 'status' => 'completed', 'total_amount' => 100000]);

        $menu1 = Menu::create(['category_id' => $this->category->id, 'name' => 'Bakso Cinta', 'price' => 25000, 'description' => 'Enak', 'is_active' => true]);
        $menu2 = Menu::create(['category_id' => $this->category->id, 'name' => 'Mie Bakso', 'price' => 20000, 'description' => 'Lezat', 'is_active' => true]);

        OrderItem::create(['order_id' => $order1->id, 'menu_id' => $menu1->id, 'quantity' => 2, 'price' => 25000, 'subtotal' => 50000]);
        OrderItem::create(['order_id' => $order2->id, 'menu_id' => $menu2->id, 'quantity' => 5, 'price' => 20000, 'subtotal' => 100000]);

        Payment::create(['order_id' => $order1->id, 'amount' => 50000, 'method' => 'cash', 'status' => 'success']);
        Payment::create(['order_id' => $order2->id, 'amount' => 100000, 'method' => 'qris', 'status' => 'success']);

        // 1. Sales Report: View all
        $responseSales = $this->actingAs($superAdmin)->get(route('admin.reports.sales'));
        $responseSales->assertStatus(200);
        $responseSales->assertSee('Rp 150.000'); // total
        $responseSales->assertSee('Krave Jakarta');
        $responseSales->assertSee('Krave Bandung');

        // Filter JKT Branch
        $responseSalesFiltered = $this->actingAs($superAdmin)->get(route('admin.reports.sales', ['branch_id' => $this->branch1->id]));
        $responseSalesFiltered->assertStatus(200);
        $responseSalesFiltered->assertSee('Rp 50.000');
        $responseSalesFiltered->assertDontSee('Rp 150.000');

        // 2. Menu Report
        $responseMenus = $this->actingAs($superAdmin)->get(route('admin.reports.menus'));
        $responseMenus->assertStatus(200);
        $responseMenus->assertSee('Bakso Cinta');
        $responseMenus->assertSee('Mie Bakso');

        // 3. Payments Report
        $responsePayments = $this->actingAs($superAdmin)->get(route('admin.reports.payments'));
        $responsePayments->assertStatus(200);
        $responsePayments->assertSee('Cash');
        $responsePayments->assertSee('QRIS');
    }

    public function test_branch_admin_is_restricted_to_own_branch(): void
    {
        $adminBDG = $this->getBranchAdmin($this->branch2);

        $order1 = Order::create(['branch_id' => $this->branch1->id, 'table_number' => 1, 'status' => 'completed', 'total_amount' => 50000]);
        $order2 = Order::create(['branch_id' => $this->branch2->id, 'table_number' => 2, 'status' => 'completed', 'total_amount' => 100000]);

        $category2 = Category::create(['branch_id' => $this->branch2->id, 'name' => 'Makanan Utama', 'slug' => 'makanan-utama']);

        $menu1 = Menu::create(['branch_id' => $this->branch1->id, 'category_id' => $this->category->id, 'name' => 'Bakso Cinta', 'price' => 25000, 'description' => 'Enak', 'is_active' => true]);
        $menu2 = Menu::create(['branch_id' => $this->branch2->id, 'category_id' => $category2->id, 'name' => 'Mie Bakso', 'price' => 20000, 'description' => 'Lezat', 'is_active' => true]);

        OrderItem::create(['order_id' => $order1->id, 'menu_id' => $menu1->id, 'quantity' => 2, 'price' => 25000, 'subtotal' => 50000]);
        OrderItem::create(['order_id' => $order2->id, 'menu_id' => $menu2->id, 'quantity' => 5, 'price' => 20000, 'subtotal' => 100000]);

        Payment::create(['order_id' => $order1->id, 'amount' => 50000, 'method' => 'cash', 'status' => 'success']);
        Payment::create(['order_id' => $order2->id, 'amount' => 100000, 'method' => 'qris', 'status' => 'success']);

        // Access Sales JKT - JKT data must NOT be visible
        $responseSales = $this->actingAs($adminBDG)->get(route('admin.reports.sales'));
        $responseSales->assertStatus(200);
        $responseSales->assertSee('Rp 100.000');
        $responseSales->assertDontSee('Rp 50.000');
        $responseSales->assertDontSee('Krave Jakarta');

        // Access Menus JKT - only BDG menus visible
        $responseMenus = $this->actingAs($adminBDG)->get(route('admin.reports.menus'));
        $responseMenus->assertStatus(200);
        $responseMenus->assertSee('Mie Bakso');
        $responseMenus->assertDontSee('Bakso Cinta');

        // Access Payments JKT - only QRIS visible
        $responsePayments = $this->actingAs($adminBDG)->get(route('admin.reports.payments'));
        $responsePayments->assertStatus(200);
        $responsePayments->assertSee('QRIS');
        $responsePayments->assertDontSee('Cash</span>', false);
    }

    public function test_reports_can_export_to_excel(): void
    {
        $superAdmin = $this->getSuperAdmin();

        $order = Order::create(['branch_id' => $this->branch1->id, 'table_number' => 1, 'status' => 'completed', 'total_amount' => 50000]);
        $menu = Menu::create(['category_id' => $this->category->id, 'name' => 'Bakso Cinta', 'price' => 25000, 'description' => 'Enak', 'is_active' => true]);
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $menu->id, 'quantity' => 2, 'price' => 25000, 'subtotal' => 50000]);
        Payment::create(['order_id' => $order->id, 'amount' => 50000, 'method' => 'cash', 'status' => 'success']);

        // 1. Sales Report Excel Export
        $responseSales = $this->actingAs($superAdmin)->get(route('admin.reports.sales', ['export' => 'excel']));
        $responseSales->assertStatus(200);
        $responseSales->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // 2. Menu Report Excel Export
        $responseMenus = $this->actingAs($superAdmin)->get(route('admin.reports.menus', ['export' => 'excel']));
        $responseMenus->assertStatus(200);
        $responseMenus->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // 3. Payments Report Excel Export
        $responsePayments = $this->actingAs($superAdmin)->get(route('admin.reports.payments', ['export' => 'excel']));
        $responsePayments->assertStatus(200);
        $responsePayments->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
