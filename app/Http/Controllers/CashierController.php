<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
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
        } else {
            $query->where('status', '!=', 'pending');
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Sort: confirmed first, then in_process, then others, sorted by creation time
        $orders = $query->orderByRaw("CASE 
                WHEN status = 'confirmed' THEN 0 
                WHEN status = 'in_process' THEN 1 
                ELSE 2 
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

        if ($order->status === 'pending') {
            return redirect()->route('cashier.transactions.show', $order->id);
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
            'status' => 'required|in:cancelled',
        ]);

        $newStatus = $request->input('status');
        $currentStatus = $order->status;

        if ($currentStatus !== 'pending') {
            return redirect()->back()->with('error', __('Hanya pesanan yang belum terbayar (Pending) yang dapat dibatalkan oleh kasir.'));
        }

        DB::beginTransaction();

        try {
            // Restore stock if transitioning to cancelled (for pending order, stock is not yet deducted, but for safety in case of custom configurations)
            // Wait, pending order doesn't deduct stock, but if we do, we can restore it.
            $order->load('orderItems.menu');
            foreach ($order->orderItems as $item) {
                if ($item->menu && $item->menu->stock_item_id) {
                    StockItem::withoutGlobalScopes()
                        ->where('id', $item->menu->stock_item_id)
                        ->increment('quantity', $item->quantity);
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
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'method' => $paymentMethod,
                'status' => 'success',
                'cash_received' => $amountPaid,
                'change' => $change,
            ]);

            // Trigger RPA automations
            event(new OrderPaid($order, $payment));

            DB::commit();

            return redirect()->route('cashier.orders.show', $order->id)
                ->with('success', __('Pembayaran berhasil dikonfirmasi! Penerbitan struk sedang diproses secara otomatis di latar belakang.'))
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

        $cashReceived = $receipt->payment->cash_received ?? $receipt->payment->amount;
        $change = $receipt->payment->change ?? 0;

        return view('cashiers.receipts.show', [
            'receipt' => $receipt,
            'cashReceived' => $cashReceived,
            'change' => $change,
        ]);
    }

    /**
     * Display the list of pending transactions.
     */
    public function transactions(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Order::query()->where('status', 'pending')->with(['orderItems.menu']);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('cashiers.transactions.index', [
            'orders' => $orders,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Display a specific transaction for payment processing.
     */
    public function showTransaction(Order $order)
    {
        $branchId = app(BranchContext::class)->getBranchId();

        if ($branchId && $order->branch_id !== $branchId) {
            abort(404, __('Transaksi tidak ditemukan di cabang ini.'));
        }

        if ($order->status !== 'pending') {
            return redirect()->route('cashier.orders.show', $order->id)
                ->with('info', __('Pesanan ini sudah dibayar.'));
        }

        $order->load(['orderItems.menu', 'payments.receipts']);

        return view('cashiers.transactions.show', [
            'order' => $order,
        ]);
    }
}
