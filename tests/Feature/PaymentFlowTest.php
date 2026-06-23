<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private Role $cashierRole;

    private Role $adminRole;

    private Branch $branch;

    private Branch $otherBranch;

    private User $cashier;

    private User $otherCashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cashierRole = Role::create(['name' => 'cashier']);
        $this->adminRole = Role::create(['name' => 'admin']);

        $this->branch = Branch::create([
            'name' => 'Krave Scan Jakarta',
            'code' => 'JKT-01',
            'address' => 'Jakarta Sudirman',
            'phone' => '021-123456',
        ]);

        $this->otherBranch = Branch::create([
            'name' => 'Krave Scan Bandung',
            'code' => 'BDG-01',
            'address' => 'Bandung Dago',
            'phone' => '022-654321',
        ]);

        $this->cashier = User::create([
            'name' => 'Jakarta Cashier',
            'email' => 'cashier.jkt@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $this->branch->id,
        ]);

        $this->otherCashier = User::create([
            'name' => 'Bandung Cashier',
            'email' => 'cashier.bdg@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $this->otherBranch->id,
        ]);
    }

    /**
     * Test cashier can view order list.
     */
    public function test_cashier_can_view_orders_list(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('cashier.orders'));
        $response->assertStatus(200);
        $response->assertViewIs('cashiers.orders.index');
        $response->assertViewHas('orders');
    }

    /**
     * Test cashier only sees orders from their active branch context.
     */
    public function test_cashier_only_sees_orders_from_their_branch(): void
    {
        // Order for Jakarta
        $jktOrder = Order::withoutEvents(function () {
            return Order::create([
                'branch_id' => $this->branch->id,
                'table_number' => 3,
                'status' => 'pending',
                'total_amount' => 50000.00,
            ]);
        });

        // Order for Bandung
        $bdgOrder = Order::withoutEvents(function () {
            return Order::create([
                'branch_id' => $this->otherBranch->id,
                'table_number' => 5,
                'status' => 'pending',
                'total_amount' => 35000.00,
            ]);
        });

        // Jakarta cashier scans orders
        $response = $this->actingAs($this->cashier)->get(route('cashier.orders'));
        $orders = $response->viewData('orders');

        $this->assertTrue($orders->contains('id', $jktOrder->id));
        $this->assertFalse($orders->contains('id', $bdgOrder->id));
    }

    /**
     * Test cashier cannot access order from a different branch.
     */
    public function test_cashier_cannot_access_other_branch_order(): void
    {
        $bdgOrder = Order::withoutEvents(function () {
            return Order::create([
                'branch_id' => $this->otherBranch->id,
                'table_number' => 5,
                'status' => 'pending',
                'total_amount' => 35000.00,
            ]);
        });

        // Jakarta cashier tries to view Bandung order
        $response = $this->actingAs($this->cashier)->get(route('cashier.orders.show', $bdgOrder->id));
        $response->assertStatus(404);
    }

    /**
     * Test payment fails if cash received is less than total amount.
     */
    public function test_cash_payment_fails_if_insufficient_amount(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::create([
                'branch_id' => $this->branch->id,
                'table_number' => 3,
                'status' => 'pending',
                'total_amount' => 50000.00,
            ]);
        });

        $response = $this->actingAs($this->cashier)
            ->from(route('cashier.orders.show', $order->id))
            ->post(route('cashier.orders.payment', $order->id), [
                'payment_method' => 'cash',
                'amount_paid' => 45000, // Insufficient cash
            ]);

        $response->assertRedirect(route('cashier.orders.show', $order->id));
        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(0, Payment::count());
    }

    /**
     * Test cash payment succeeds with sufficient amount.
     */
    public function test_cash_payment_succeeds_and_creates_payment_and_receipt(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::create([
                'branch_id' => $this->branch->id,
                'table_number' => 3,
                'status' => 'pending',
                'total_amount' => 50000.00,
            ]);
        });

        $response = $this->actingAs($this->cashier)
            ->post(route('cashier.orders.payment', $order->id), [
                'payment_method' => 'cash',
                'amount_paid' => 100000, // Sufficient cash
            ]);

        $order->refresh();
        $this->assertEquals('confirmed', $order->status);

        // Verify Payment
        $payment = Payment::first();
        $this->assertNotNull($payment);
        $this->assertEquals($order->id, $payment->order_id);
        $this->assertEquals(50000.00, $payment->amount);
        $this->assertEquals('cash', $payment->method);
        $this->assertEquals('success', $payment->status);

        // Verify Receipt
        $receipt = Receipt::first();
        $this->assertNotNull($receipt);
        $this->assertEquals($payment->id, $receipt->payment_id);

        // Format check: REC-{KODE_CABANG}-{YYYYMMDD}-{ID_PEMBAYARAN}
        $expectedReceiptNumber = 'REC-JKT-01-'.now()->format('Ymd').'-'.str_pad($payment->id, 4, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedReceiptNumber, $receipt->receipt_number);

        // Verify Redirection & Session
        $response->assertRedirect(route('cashier.orders.show', $order->id));
        $response->assertSessionHas('success');
        $response->assertSessionHas('cash_received', 100000);
        $response->assertSessionHas('change', 50000);
    }

    /**
     * Test QRIS payment succeeds instantly.
     */
    public function test_qris_payment_succeeds_instantly(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::create([
                'branch_id' => $this->branch->id,
                'table_number' => 3,
                'status' => 'pending',
                'total_amount' => 50000.00,
            ]);
        });

        $response = $this->actingAs($this->cashier)
            ->post(route('cashier.orders.payment', $order->id), [
                'payment_method' => 'qris',
            ]);

        $order->refresh();
        $this->assertEquals('confirmed', $order->status);

        // Verify Payment
        $payment = Payment::first();
        $this->assertNotNull($payment);
        $this->assertEquals($order->id, $payment->order_id);
        $this->assertEquals(50000.00, $payment->amount);
        $this->assertEquals('qris', $payment->method);
        $this->assertEquals('success', $payment->status);

        // Verify Receipt
        $receipt = Receipt::first();
        $this->assertNotNull($receipt);
        $this->assertEquals($payment->id, $receipt->payment_id);

        $response->assertRedirect(route('cashier.orders.show', $order->id));
        $response->assertSessionHas('success');
    }
}
