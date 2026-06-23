<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Receipt;
use App\Services\BranchContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    /**
     * Display the list of orders.
     */
    public function orders(Request $request)
    {
        $status = $request->input('status');

        $query = Order::query()->with(['orderItems.menu']);

        if ($status) {
            $query->where('status', $status);
        }

        // Sort: pending first, then confirmed, then others, sorted by creation time
        $orders = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('cashiers.orders.index', [
            'orders' => $orders,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Display a specific order.
     */
    public function showOrder(Order $order)
    {
        $branchId = app(BranchContext::class)->getBranchId();

        if ($branchId && $order->branch_id !== $branchId) {
            abort(404, __('Pesanan tidak ditemukan di cabang ini.'));
        }

        $order->load(['orderItems.menu', 'payments.receipts']);

        return view('cashiers.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Process order payment.
     */
    public function processPayment(Request $request, Order $order)
    {
        $branchId = app(BranchContext::class)->getBranchId();

        if ($branchId && $order->branch_id !== $branchId) {
            abort(404, __('Pesanan tidak ditemukan di cabang ini.'));
        }

        if ($order->status !== 'pending') {
            return redirect()->route('cashier.orders.show', $order->id)
                ->with('error', __('Pesanan ini sudah diproses atau dibatalkan.'));
        }

        $request->validate([
            'payment_method' => 'required|in:cash,qris',
            'amount_paid' => 'required_if:payment_method,cash|nullable|numeric',
        ]);

        $paymentMethod = $request->input('payment_method');
        $amountPaid = $request->input('amount_paid');
        $totalAmount = (float) $order->total_amount;

        if ($paymentMethod === 'cash') {
            if ($amountPaid < $totalAmount) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', __('Jumlah uang tunai yang dibayarkan kurang dari total pesanan.'));
            }
            $change = $amountPaid - $totalAmount;
        } else {
            // For QRIS, simulate visual check -> instant success
            $amountPaid = $totalAmount;
            $change = 0;
        }

        DB::beginTransaction();

        try {
            // Update order status
            $order->update(['status' => 'confirmed']);

            // Create Payment
            // Note: ScopedToBranch is applied on Payment, but it doesn't have a branch_id column.
            // We set the fields defined in the migration
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'method' => $paymentMethod,
                'status' => 'success',
            ]);

            // Generate receipt number: REC-{KODE_CABANG}-{YYYYMMDD}-{ID_PEMBAYARAN}
            $branchCode = $order->branch ? $order->branch->code : 'HQ';
            $date = now()->format('Ymd');
            $paddedPaymentId = str_pad($payment->id, 4, '0', STR_PAD_LEFT);
            $receiptNumber = "REC-{$branchCode}-{$date}-{$paddedPaymentId}";

            // Create Receipt
            $receipt = Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => $receiptNumber,
                'printed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('cashier.receipts.show', $receipt->id)
                ->with('success', __('Pembayaran berhasil dikonfirmasi!'))
                ->with('cash_received', $amountPaid)
                ->with('change', $change);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', __('Gagal memproses pembayaran: ').$e->getMessage());
        }
    }

    /**
     * Display printable receipt.
     */
    public function showReceipt(Receipt $receipt)
    {
        $branchId = app(BranchContext::class)->getBranchId();

        // Check scope access manually just in case
        if ($branchId && $receipt->payment->order->branch_id !== $branchId) {
            abort(404, __('Struk tidak ditemukan di cabang ini.'));
        }

        $receipt->load(['payment.order.orderItems.menu', 'payment.order.branch']);

        // Check if cash_received is passed in URL query or session flash
        $cashReceived = request()->query('cash_received') ?? session('cash_received');
        $change = request()->query('change') ?? session('change');

        if ($cashReceived === null && $receipt->payment->method === 'cash') {
            // Fallback: if not set, assume exact payment
            $cashReceived = $receipt->payment->amount;
            $change = 0;
        }

        return view('cashiers.receipts.show', [
            'receipt' => $receipt,
            'cashReceived' => $cashReceived,
            'change' => $change,
        ]);
    }
}
