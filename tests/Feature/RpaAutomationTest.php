<?php

namespace Tests\Feature;

use App\Jobs\CheckStockLevelsJob;
use App\Jobs\GenerateReceiptJob;
use App\Models\AutomationLog;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RpaAutomationTest extends TestCase
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
            'code' => 'TB01',
            'address' => 'Test Address',
            'phone' => '1234567890',
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

    public function test_payment_processing_dispatches_jobs_automatically(): void
    {
        Queue::fake();

        $cashier = $this->getCashierUser();

        $order = Order::create([
            'branch_id' => $this->branch->id,
            'table_number' => 5,
            'status' => 'pending',
            'total_amount' => 15000,
        ]);

        $response = $this->actingAs($cashier)->post(route('cashier.orders.payment', $order->id), [
            'payment_method' => 'cash',
            'amount_paid' => 15000,
        ]);

        $response->assertRedirect(route('cashier.orders.show', $order->id));

        Queue::assertPushed(GenerateReceiptJob::class);
        Queue::assertPushed(CheckStockLevelsJob::class);
    }

    public function test_generate_receipt_job_executes_successfully(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'table_number' => 5,
            'status' => 'pending',
            'total_amount' => 15000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 15000,
            'method' => 'cash',
            'status' => 'success',
        ]);

        // Run the Job directly
        $job = new GenerateReceiptJob($payment);
        $job->handle();

        $this->assertDatabaseHas('receipts', [
            'payment_id' => $payment->id,
        ]);

        $this->assertDatabaseHas('automation_logs', [
            'branch_id' => $this->branch->id,
            'task_name' => 'Generate Receipt',
            'status' => 'success',
        ]);
    }

    public function test_check_stock_levels_job_triggers_warning_on_low_stock(): void
    {
        // Create stock item at or below minimum threshold
        $stock = StockItem::create([
            'branch_id' => $this->branch->id,
            'name' => 'Es Teh Manis',
            'quantity' => 2,
            'minimum_quantity' => 5, // Quantity 2 is <= minimum 5
            'unit' => 'gelas',
        ]);

        $menu = Menu::create([
            'name' => 'Es Teh Manis',
            'category_id' => $this->category->id,
            'stock_item_id' => $stock->id,
            'price' => 5000,
            'is_active' => true,
        ]);

        $order = Order::create([
            'branch_id' => $this->branch->id,
            'table_number' => 5,
            'status' => 'confirmed',
            'total_amount' => 5000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $menu->id,
            'quantity' => 1,
            'price' => 5000,
            'subtotal' => 5000,
        ]);

        // Run Job
        $job = new CheckStockLevelsJob($order);
        $job->handle();

        $this->assertDatabaseHas('automation_logs', [
            'branch_id' => $this->branch->id,
            'task_name' => 'Low Stock Warning',
            'status' => 'warning',
        ]);

        $log = AutomationLog::where('task_name', 'Low Stock Warning')->first();
        $this->assertStringContainsString('Es Teh Manis', $log->details);
    }

    public function test_cancel_stale_orders_command_cancels_older_orders(): void
    {
        // 1. Create a fresh pending order (should NOT be cancelled)
        $freshOrder = Order::create([
            'branch_id' => $this->branch->id,
            'table_number' => 1,
            'status' => 'pending',
            'total_amount' => 10000,
        ]);

        // 2. Create a stale pending order (older than 30 mins)
        $staleOrder = Order::create([
            'branch_id' => $this->branch->id,
            'table_number' => 2,
            'status' => 'pending',
            'total_amount' => 20000,
        ]);
        // Modify created_at manually via DB query to bypass Eloquent scopes/events if needed
        Order::where('id', $staleOrder->id)->update([
            'created_at' => Carbon::now()->subMinutes(35),
        ]);

        // Run Command
        Artisan::call('orders:cancel-stale');

        $freshOrder->refresh();
        $staleOrder->refresh();

        $this->assertEquals('pending', $freshOrder->status);
        $this->assertEquals('cancelled', $staleOrder->status);

        $this->assertDatabaseHas('automation_logs', [
            'branch_id' => $this->branch->id,
            'task_name' => 'Auto Cancel Order',
            'status' => 'success',
        ]);
    }

    public function test_aggregate_daily_reports_command_aggregates_sales_and_financials(): void
    {
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        // Create completed order and payment for yesterday
        $order = new Order([
            'branch_id' => $this->branch->id,
            'table_number' => 1,
            'status' => 'completed',
            'total_amount' => 30000,
        ]);
        $order->created_at = Carbon::yesterday()->hour(12);
        $order->save();

        $payment = new Payment([
            'order_id' => $order->id,
            'amount' => 30000,
            'method' => 'cash',
            'status' => 'success',
        ]);
        $payment->created_at = Carbon::yesterday()->hour(12);
        $payment->save();

        // Run Command
        Artisan::call('reports:aggregate-daily');

        $this->assertDatabaseHas('sales_reports', [
            'branch_id' => $this->branch->id,
            'date' => $yesterday.' 00:00:00',
            'total_orders' => 1,
            'total_revenue' => 30000,
        ]);

        $this->assertDatabaseHas('financial_reports', [
            'branch_id' => $this->branch->id,
            'date' => $yesterday.' 00:00:00',
            'type' => 'income',
            'amount' => 30000,
        ]);

        $this->assertDatabaseHas('automation_logs', [
            'branch_id' => $this->branch->id,
            'task_name' => 'Aggregate Daily Reports',
            'status' => 'success',
        ]);
    }

    public function test_admin_can_access_automations_log_page(): void
    {
        $admin = $this->getAdminUser();

        AutomationLog::create([
            'branch_id' => $this->branch->id,
            'task_name' => 'Test Task',
            'status' => 'success',
            'details' => json_encode(['test' => 'data']),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.automations.index'));

        $response->assertStatus(200);
        $response->assertSee('Log Otomatisasi (RPA)');
        $response->assertSee('Test Task');
    }
}
