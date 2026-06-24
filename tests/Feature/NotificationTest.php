<?php

namespace Tests\Feature;

use App\Events\OrderCreated;
use App\Jobs\CheckStockLevelsJob;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\User;
use App\Notifications\DailyReportNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\OrderCreatedNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $branchAAdmin;

    protected User $branchACashier;

    protected User $branchBAdmin;

    protected Branch $branchA;

    protected Branch $branchB;

    protected Role $adminRole;

    protected Role $cashierRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup base roles
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->cashierRole = Role::create(['name' => 'cashier']);

        // Setup branches
        $this->branchA = Branch::create(['name' => 'Cabang A', 'code' => 'CBG-A', 'address' => 'Alamat A']);
        $this->branchB = Branch::create(['name' => 'Cabang B', 'code' => 'CBG-B', 'address' => 'Alamat B']);

        // Super Admin (no branch)
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@kravescan.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => null,
        ]);

        // Branch A staff
        $this->branchAAdmin = User::create([
            'name' => 'Admin A',
            'email' => 'admin.a@kravescan.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $this->branchA->id,
        ]);

        $this->branchACashier = User::create([
            'name' => 'Cashier A',
            'email' => 'cashier.a@kravescan.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $this->branchA->id,
        ]);

        // Branch B staff
        $this->branchBAdmin = User::create([
            'name' => 'Admin B',
            'email' => 'admin.b@kravescan.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $this->branchB->id,
        ]);
    }

    /**
     * Test OrderCreatedNotification is dispatched to correct branch staff and super admin.
     */
    public function test_order_created_notification_is_sent_to_correct_staff()
    {
        Notification::fake();

        // Create an order for Branch A
        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 5,
            'status' => 'pending',
            'total_amount' => 150000,
        ]);

        event(new OrderCreated($order));

        // Super Admin, Branch A Admin, and Branch A Cashier should receive notification
        Notification::assertSentTo(
            [$this->superAdmin, $this->branchAAdmin, $this->branchACashier],
            OrderCreatedNotification::class
        );

        // Branch B Admin should NOT receive notification
        Notification::assertNotSentTo(
            [$this->branchBAdmin],
            OrderCreatedNotification::class
        );
    }

    /**
     * Test LowStockNotification is sent when stock is below minimum.
     */
    public function test_low_stock_notification_is_triggered_when_stock_falls_below_minimum()
    {
        Notification::fake();

        // Create StockItem in Branch A with low stock
        $stockItem = StockItem::create([
            'branch_id' => $this->branchA->id,
            'name' => 'Bakso Sapi',
            'quantity' => 5,
            'minimum_quantity' => 10,
            'unit' => 'pcs',
        ]);

        $category = Category::create([
            'branch_id' => $this->branchA->id,
            'name' => 'Makanan',
        ]);

        $menu = Menu::create([
            'branch_id' => $this->branchA->id,
            'category_id' => $category->id,
            'name' => 'Bakso Spesial',
            'price' => 20000,
            'stock_item_id' => $stockItem->id,
            'is_active' => true,
        ]);

        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 2,
            'status' => 'pending',
            'total_amount' => 20000,
        ]);

        $order->orderItems()->create([
            'menu_id' => $menu->id,
            'quantity' => 1,
            'price' => 20000,
            'subtotal' => 20000,
        ]);

        // Run the Job
        CheckStockLevelsJob::dispatchSync($order);

        // Only admins (Super Admin & Branch A Admin) should receive LowStockNotification
        Notification::assertSentTo(
            [$this->superAdmin, $this->branchAAdmin],
            LowStockNotification::class
        );

        // Branch A Cashier and Branch B Admin should NOT receive LowStockNotification
        Notification::assertNotSentTo(
            [$this->branchACashier, $this->branchBAdmin],
            LowStockNotification::class
        );
    }

    /**
     * Test DailyReportNotification is dispatched.
     */
    public function test_daily_report_notification_is_sent()
    {
        Notification::fake();

        // Seed some paid orders for yesterday
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 2,
            'status' => 'completed',
            'total_amount' => 50000,
            'created_at' => Carbon::yesterday()->setHour(12),
        ]);

        $order->payments()->create([
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'success',
            'created_at' => Carbon::yesterday()->setHour(12),
        ]);

        // Run daily aggregation command
        Artisan::call('reports:aggregate-daily', ['date' => $yesterday]);

        // Super Admin & Branch A Admin should receive DailyReportNotification
        Notification::assertSentTo(
            [$this->superAdmin, $this->branchAAdmin],
            DailyReportNotification::class
        );

        // Branch B Admin should receive DailyReportNotification for Branch B report (aggregated in the loop)
        Notification::assertSentTo(
            [$this->branchBAdmin],
            DailyReportNotification::class
        );
    }

    /**
     * Test AJAX endpoints for notifications.
     */
    public function test_notification_api_endpoints()
    {
        // Force database channel notifications
        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 5,
            'status' => 'pending',
            'total_amount' => 150000,
        ]);

        // Notify Cashier A
        $this->branchACashier->notify(new OrderCreatedNotification($order));

        $this->assertEquals(1, $this->branchACashier->unreadNotifications()->count());

        // Call list endpoint acting as Cashier A
        $response = $this->actingAs($this->branchACashier)
            ->getJson(route('api.notifications.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'notifications' => [
                    '*' => ['id', 'type', 'message', 'created_at', 'redirect_url'],
                ],
                'unread_count',
            ])
            ->assertJsonFragment([
                'unread_count' => 1,
            ]);

        $notificationId = $response->json('notifications.0.id');

        // Mark as read
        $markResponse = $this->actingAs($this->branchACashier)
            ->postJson(route('api.notifications.mark-as-read', $notificationId));

        $markResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'unread_count' => 0,
            ]);

        $this->assertEquals(0, $this->branchACashier->unreadNotifications()->count());
    }

    /**
     * Test CleanOldNotifications command.
     */
    public function test_cleanup_command_deletes_old_read_notifications()
    {
        // Create notifications manually in database
        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 5,
            'status' => 'pending',
            'total_amount' => 150000,
        ]);

        $this->branchACashier->notify(new OrderCreatedNotification($order));
        $this->branchACashier->notify(new OrderCreatedNotification($order));

        // Get notifications
        $notifications = $this->branchACashier->notifications;
        $this->assertCount(2, $notifications);

        // Mark first one as read and make it old
        $notification1 = $notifications[0];
        $notification1->update([
            'read_at' => Carbon::now(),
            'created_at' => Carbon::now()->subDays(40),
        ]);

        // Leave second one as unread, but also old
        $notification2 = $notifications[1];
        $notification2->update([
            'created_at' => Carbon::now()->subDays(40),
        ]);

        // Run cleanup
        Artisan::call('notifications:clean', ['days' => 30]);

        // Assert that notification1 (read & old) was deleted, but notification2 (unread & old) remains
        $this->assertDatabaseMissing('notifications', ['id' => $notification1->id]);
        $this->assertDatabaseHas('notifications', ['id' => $notification2->id]);
    }
}
