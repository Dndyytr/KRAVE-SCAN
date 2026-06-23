<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Scopes\BranchScope;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $branchId = $request->input('branch_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Bypass BranchScope to allow Admin to see all branches
        $query = Order::withoutGlobalScope(BranchScope::class)
            ->with(['branch', 'orderItems.menu']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $branches = Branch::all();

        return view('admin.orders.index', [
            'orders' => $orders,
            'branches' => $branches,
            'currentStatus' => $status,
            'currentBranchId' => $branchId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::withoutGlobalScope(BranchScope::class)
            ->with(['branch', 'orderItems.menu', 'payments.receipts', 'histories.user'])
            ->findOrFail($id);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }
}
