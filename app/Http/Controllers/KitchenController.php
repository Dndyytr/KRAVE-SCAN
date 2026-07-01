<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StockItem;
use App\Services\BranchContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KitchenController extends Controller
{
    /**
     * Display a listing of orders for the kitchen.
     */
    public function orders(Request $request)
    {
        $branchId = app(BranchContext::class)->getBranchId();

        $query = Order::query()
            ->with(['orderItems.menu'])
            ->latest();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Apply status filter
        $status = $request->input('status');
        if (in_array($status, ['confirmed', 'in_process', 'completed'])) {
            $query->where('status', $status);
        } else {
            // Default: active cooking orders (confirmed = paid, in_process = cooking)
            $query->whereIn('status', ['confirmed', 'in_process']);
        }

        // Apply date filters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('kitchen.orders.index', [
            'orders' => $orders,
            'currentStatus' => $status,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Display a specific order in the kitchen.
     */
    public function showOrder(Order $order)
    {
        $branchId = app(BranchContext::class)->getBranchId();

        if ($branchId && $order->branch_id !== $branchId) {
            abort(404, __('Pesanan tidak ditemukan di cabang ini.'));
        }

        $order->load(['orderItems.menu', 'histories.user']);

        return view('kitchen.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Update the cooking status of the order.
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

        if ($newStatus === 'cancelled' && ! in_array($currentStatus, ['confirmed', 'in_process'])) {
            return redirect()->back()->with('error', __('Hanya pesanan yang sedang diproses atau dikonfirmasi yang dapat dibatalkan di dapur.'));
        }

        DB::beginTransaction();

        try {
            // Restore stock if cancelled by kitchen
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

            return redirect()->route('kitchen.orders.show', $order->id)
                ->with('success', __("Status pesanan berhasil diperbarui menjadi {$newStatus}."));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', __('Gagal memperbarui status pesanan: ').$e->getMessage());
        }
    }
}
