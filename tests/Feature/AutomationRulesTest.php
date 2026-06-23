<?php

namespace Tests\Feature;

use App\Events\OrderPaid;
use App\Jobs\CheckStockLevelsJob;
use App\Jobs\GenerateReceiptJob;
use App\Models\AutomationLog;
use App\Models\AutomationRule;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AutomationRulesTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $cashierRole;

    private Branch $branchA;

    private Branch $branchB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->cashierRole = Role::create(['name' => 'cashier']);

        // Disable booted listener temporarily to create branches manually if we want clean state,
        // but wait: booted listener is good because it auto-creates the rules.
        // Let's let the booted hook create the default rules.
        $this->branchA = Branch::create([
            'name' => 'Branch Jakarta',
            'code' => 'JKT01',
            'address' => 'Jakarta Address',
            'phone' => '1234567890',
        ]);

        $this->branchB = Branch::create([
            'name' => 'Branch Bandung',
            'code' => 'BDG01',
            'address' => 'Bandung Address',
            'phone' => '0987654321',
        ]);
    }

    private function getAdminUser(Branch $branch)
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@'.strtolower($branch->code).'.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $branch->id,
        ]);
    }

    public function test_automation_engine_triggers_active_rules_and_ignores_inactive_ones(): void
    {
        Queue::fake();

        // Branch A has default rules created automatically by Branch::created booted hook.
        // Let's set one rule to inactive.
        $rule = AutomationRule::withoutGlobalScopes()
            ->where('branch_id', $this->branchA->id)
            ->where('action_job', 'App\Jobs\CheckStockLevelsJob')
            ->first();

        $this->assertNotNull($rule);
        $rule->update(['is_active' => false]);

        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 1,
            'status' => 'pending',
            'total_amount' => 50000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'success',
        ]);

        // Trigger Event
        event(new OrderPaid($order, $payment));

        // GenerateReceiptJob (active) should be dispatched.
        Queue::assertPushed(GenerateReceiptJob::class);

        // CheckStockLevelsJob (inactive) should NOT be dispatched.
        Queue::assertNotPushed(CheckStockLevelsJob::class);
    }

    public function test_automation_rules_evaluation_of_payment_method_condition(): void
    {
        Queue::fake();

        // Delete default rules for Branch A to avoid interference
        AutomationRule::withoutGlobalScopes()->where('branch_id', $this->branchA->id)->delete();

        // Create a conditional rule: Trigger receipt generation ONLY for QRIS payments
        AutomationRule::create([
            'branch_id' => $this->branchA->id,
            'name' => 'Struk QRIS',
            'trigger_event' => 'App\Events\OrderPaid',
            'condition_type' => 'payment_method_equals',
            'condition_value' => ['payment_method' => 'qris'],
            'action_job' => 'App\Jobs\GenerateReceiptJob',
            'is_active' => true,
        ]);

        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 2,
            'status' => 'pending',
            'total_amount' => 30000,
        ]);

        // 1. Payment with method 'cash' should NOT trigger
        $paymentCash = Payment::create([
            'order_id' => $order->id,
            'amount' => 30000,
            'method' => 'cash',
            'status' => 'success',
        ]);

        event(new OrderPaid($order, $paymentCash));
        Queue::assertNotPushed(GenerateReceiptJob::class);

        // 2. Payment with method 'qris' should trigger
        $paymentQris = Payment::create([
            'order_id' => $order->id,
            'amount' => 30000,
            'method' => 'qris',
            'status' => 'success',
        ]);

        event(new OrderPaid($order, $paymentQris));
        Queue::assertPushed(GenerateReceiptJob::class);
    }

    public function test_automation_rules_evaluation_of_min_order_amount_condition(): void
    {
        Queue::fake();

        AutomationRule::withoutGlobalScopes()->where('branch_id', $this->branchA->id)->delete();

        // Create a conditional rule: Check stock levels ONLY if order is >= 100.000
        AutomationRule::create([
            'branch_id' => $this->branchA->id,
            'name' => 'High Value Stock Check',
            'trigger_event' => 'App\Events\OrderPaid',
            'condition_type' => 'min_order_amount',
            'condition_value' => ['min_amount' => 100000],
            'action_job' => 'App\Jobs\CheckStockLevelsJob',
            'is_active' => true,
        ]);

        // 1. Order total 50.000 (Below min amount)
        $orderLow = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 1,
            'status' => 'pending',
            'total_amount' => 50000,
        ]);
        $paymentLow = Payment::create([
            'order_id' => $orderLow->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'success',
        ]);
        event(new OrderPaid($orderLow, $paymentLow));
        Queue::assertNotPushed(CheckStockLevelsJob::class);

        // 2. Order total 120.000 (Above min amount)
        $orderHigh = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 1,
            'status' => 'pending',
            'total_amount' => 120000,
        ]);
        $paymentHigh = Payment::create([
            'order_id' => $orderHigh->id,
            'amount' => 120000,
            'method' => 'cash',
            'status' => 'success',
        ]);
        event(new OrderPaid($orderHigh, $paymentHigh));
        Queue::assertPushed(CheckStockLevelsJob::class);
    }

    public function test_idempotency_prevents_duplicate_automation_job_dispatch(): void
    {
        Queue::fake();

        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 3,
            'status' => 'pending',
            'total_amount' => 45000,
        ]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 45000,
            'method' => 'cash',
            'status' => 'success',
        ]);

        // Fire event 1st time
        event(new OrderPaid($order, $payment));
        Queue::assertPushed(GenerateReceiptJob::class, 1);

        // Simulate success log for idempotency check
        $log = AutomationLog::withoutGlobalScopes()->where('idempotency_key', 'like', '%GenerateReceiptJob%')->first();
        $this->assertNotNull($log);
        $log->update(['status' => 'success']);

        // Fire event 2nd time - should be skipped by idempotency check
        event(new OrderPaid($order, $payment));

        // Assert that the job was still only pushed once total
        Queue::assertPushed(GenerateReceiptJob::class, 1);
    }

    public function test_branch_isolation_in_automation_rules(): void
    {
        Queue::fake();

        // Disable rule in Branch B
        $ruleB = AutomationRule::withoutGlobalScopes()
            ->where('branch_id', $this->branchB->id)
            ->where('action_job', 'App\Jobs\GenerateReceiptJob')
            ->first();
        $ruleB->update(['is_active' => false]);

        // Branch A's rule remains active
        $orderA = Order::create([
            'branch_id' => $this->branchA->id,
            'table_number' => 1,
            'status' => 'pending',
            'total_amount' => 20000,
        ]);
        $paymentA = Payment::create([
            'order_id' => $orderA->id,
            'amount' => 20000,
            'method' => 'cash',
            'status' => 'success',
        ]);

        event(new OrderPaid($orderA, $paymentA));

        // GenerateReceiptJob should be pushed because Branch A's rule is active
        Queue::assertPushed(GenerateReceiptJob::class);

        // Reset Queue fake
        Queue::fake();

        // Branch B's event
        $orderB = Order::create([
            'branch_id' => $this->branchB->id,
            'table_number' => 2,
            'status' => 'pending',
            'total_amount' => 25000,
        ]);
        $paymentB = Payment::create([
            'order_id' => $orderB->id,
            'amount' => 25000,
            'method' => 'cash',
            'status' => 'success',
        ]);

        event(new OrderPaid($orderB, $paymentB));

        // GenerateReceiptJob should NOT be pushed because Branch B's rule is inactive
        Queue::assertNotPushed(GenerateReceiptJob::class);
    }

    public function test_admin_can_access_automation_rules_pages_and_manage_them(): void
    {
        $admin = $this->getAdminUser($this->branchA);

        // 1. Index
        $response = $this->actingAs($admin)->get(route('admin.automations.rules.index'));
        $response->assertStatus(200);
        $response->assertSee('Mesin Aturan RPA');

        // 2. Create Page
        $response = $this->actingAs($admin)->get(route('admin.automations.rules.create'));
        $response->assertStatus(200);

        // 3. Store Rule
        $response = $this->actingAs($admin)->post(route('admin.automations.rules.store'), [
            'name' => 'Custom Rule Test',
            'trigger_event' => 'App\Events\OrderPaid',
            'action_job' => 'App\Jobs\GenerateReceiptJob',
            'condition_type' => 'always',
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('admin.automations.rules.index'));

        $this->assertDatabaseHas('automation_rules', [
            'name' => 'Custom Rule Test',
            'branch_id' => $this->branchA->id,
        ]);

        // 4. Toggle Rule via AJAX
        $rule = AutomationRule::withoutGlobalScopes()->where('name', 'Custom Rule Test')->first();
        $this->assertTrue($rule->is_active);

        $response = $this->actingAs($admin)->patch(route('admin.automations.rules.toggle', $rule->id));
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_active' => false]);
    }
}
