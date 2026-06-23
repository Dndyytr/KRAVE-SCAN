<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\StockItem;
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
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Order::query()->with(['orderItems.menu']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Sort: pending first, then confirmed, then in_process, then others, sorted by creation time
        $orders = $query->orderByRaw("CASE 
                WHEN status = 'pending' THEN 0 
                WHEN status = 'confirmed' THEN 1 
                WHEN status = 'in_process' THEN 2 
                ELSE 3 
            END")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('cashiers.orders.index', [
            'orders' => $orders,
            'currentStatus' => $status,
            'startDate' => $startDate,
            'endDate' => $endDate,
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

        $order->load(['orderItems.menu', 'payments.receipts', 'histories.user']);

        return view('cashiers.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $branchId = app(BranchContext::class)->getBranchId();

        if ($branchId && $order->branch_id !== $branchId) {
            abort(404, __('Pesanan tidak ditemukan di cabang ini.'));
        }

        $request->validate([
            'status' => 'required|in:in_process,completed,cancelled',
        ]);

        $newStatus = $request->input('status');
        $currentStatus = $order->status;

        // Validate state transitions
        if ($newStatus === 'in_process' && $currentStatus !== 'confirmed') {
            return redirect()->back()->with('error', __('Pesanan harus berstatus Confirmed sebelum diproses.'));
        }

        if ($newStatus === 'completed' && $currentStatus !== 'in_process') {
            return redirect()->back()->with('error', __('Pesanan harus berstatus In Process sebelum diselesaikan.'));
        }

        if ($newStatus === 'cancelled' && in_array($currentStatus, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', __('Pesanan yang sudah selesai atau dibatalkan tidak dapat dibatalkan lagi.'));
        }

        DB::beginTransaction();

        try {
            // Restore stock if transitioning to cancelled and stock was already deducted (status was confirmed or in process)
            if ($newStatus === 'cancelled' && in_array($currentStatus, ['confirmed', 'in_process'])) {
                $order->load('orderItems.menu');
                foreach ($order->orderItems as $item) {
                    if ($item->menu && $item->menu->stock_item_id) {
                        StockItem::withoutGlobalScopes()
                            ->where('id', $item->menu->stock_item_id)
                            ->increment('quantity', $item->quantity);
                    }
                }
            }

            $order->update(['status' => $newStatus]);

            DB::commit();

            return redirect()->route('cashier.orders.show', $order->id)
                ->with('success', __("Status pesanan berhasil diperbarui menjadi {$newStatus}."));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', __('Gagal memperbarui status pesanan: ').$e->getMessage());
        }
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

            // Deduct stock for each order item
            $order->load('orderItems.menu');
            foreach ($order->orderItems as $item) {
                if ($item->menu && $item->menu->stock_item_id) {
                    StockItem::withoutGlobalScopes()
                        ->where('id', $item->menu->stock_item_id)
                        ->decrement('quantity', $item->quantity);
                }
            }

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
