<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MenuPerformanceExport;
use App\Exports\PaymentMethodExport;
use App\Exports\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\BranchContext;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display a listing of the sales reports.
     */
    public function sales(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // Set branch context if Super Admin filters by branch
        $isSuperAdmin = auth()->user()->branch_id === null;
        if ($isSuperAdmin && $request->filled('branch_id')) {
            $branch = Branch::find($request->input('branch_id'));
            if ($branch) {
                app(BranchContext::class)->setBranch($branch);
            }
        }

        $ordersQuery = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        $totalRevenue = $ordersQuery->sum('total_amount');
        $totalOrders = $ordersQuery->count();

        if ($isSuperAdmin && ! $request->filled('branch_id')) {
            $salesData = $ordersQuery->clone()
                ->selectRaw('branch_id, DATE(created_at) as date, COUNT(id) as total_orders, SUM(total_amount) as total_revenue')
                ->groupBy('branch_id')
                ->groupByRaw('DATE(created_at)')
                ->orderBy('date', 'desc')
                ->with('branch')
                ->get();
        } else {
            $salesData = $ordersQuery->clone()
                ->selectRaw('DATE(created_at) as date, COUNT(id) as total_orders, SUM(total_amount) as total_revenue')
                ->groupByRaw('DATE(created_at)')
                ->orderBy('date', 'desc')
                ->get();
        }

        if ($request->input('export') === 'excel') {
            return Excel::download(
                new SalesReportExport($salesData),
                'laporan-penjualan-'.$startDate.'-ke-'.$endDate.'.xlsx'
            );
        }

        $branches = $isSuperAdmin ? Branch::orderBy('name')->get() : collect();

        return view('admin.reports.sales', compact('salesData', 'totalRevenue', 'totalOrders', 'startDate', 'endDate', 'branches'));
    }

    /**
     * Display menu performance reports.
     */
    public function menus(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $isSuperAdmin = auth()->user()->branch_id === null;
        if ($isSuperAdmin && $request->filled('branch_id')) {
            $branch = Branch::find($request->input('branch_id'));
            if ($branch) {
                app(BranchContext::class)->setBranch($branch);
            }
        }

        $menuPerformanceQuery = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->where('status', 'completed')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        });

        if ($isSuperAdmin && ! $request->filled('branch_id')) {
            $menuPerformance = $menuPerformanceQuery
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->selectRaw('orders.branch_id, order_items.menu_id, SUM(order_items.quantity) as total_quantity, SUM(order_items.subtotal) as total_revenue')
                ->groupBy('orders.branch_id', 'order_items.menu_id')
                ->orderBy('total_quantity', 'desc')
                ->with(['menu.category', 'order.branch'])
                ->get();
        } else {
            $menuPerformance = $menuPerformanceQuery
                ->selectRaw('menu_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
                ->groupBy('menu_id')
                ->orderBy('total_quantity', 'desc')
                ->with('menu.category')
                ->get();
        }

        $menuData = $menuPerformance->map(function ($row) use ($isSuperAdmin, $request) {
            $item = [];
            if ($isSuperAdmin && ! $request->filled('branch_id')) {
                $item['branch_name'] = $row->order->branch->name ?? '-';
            }
            $item['menu_name'] = $row->menu->name ?? '-';
            $item['category_name'] = $row->menu->category->name ?? '-';
            $item['total_quantity'] = $row->total_quantity;
            $item['total_revenue'] = $row->total_revenue;

            return $item;
        })->toArray();

        if ($request->input('export') === 'excel') {
            return Excel::download(
                new MenuPerformanceExport($menuData),
                'laporan-performa-menu-'.$startDate.'-ke-'.$endDate.'.xlsx'
            );
        }

        $branches = $isSuperAdmin ? Branch::orderBy('name')->get() : collect();

        return view('admin.reports.menus', compact('menuPerformance', 'startDate', 'endDate', 'branches', 'isSuperAdmin'));
    }

    /**
     * Display payment methods reports.
     */
    public function payments(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $isSuperAdmin = auth()->user()->branch_id === null;
        if ($isSuperAdmin && $request->filled('branch_id')) {
            $branch = Branch::find($request->input('branch_id'));
            if ($branch) {
                app(BranchContext::class)->setBranch($branch);
            }
        }

        $paymentQuery = Payment::where('payments.status', 'success')
            ->whereDate('payments.created_at', '>=', $startDate)
            ->whereDate('payments.created_at', '<=', $endDate);

        if ($isSuperAdmin && ! $request->filled('branch_id')) {
            $paymentReport = $paymentQuery
                ->join('orders', 'payments.order_id', '=', 'orders.id')
                ->selectRaw('orders.branch_id, payments.method, COUNT(payments.id) as total_transactions, SUM(payments.amount) as total_revenue')
                ->groupBy('orders.branch_id', 'payments.method')
                ->orderBy('total_revenue', 'desc')
                ->with('order.branch')
                ->get();
        } else {
            $paymentReport = $paymentQuery
                ->selectRaw('method, COUNT(id) as total_transactions, SUM(amount) as total_revenue')
                ->groupBy('method')
                ->orderBy('total_revenue', 'desc')
                ->get();
        }

        $paymentData = $paymentReport->map(function ($row) use ($isSuperAdmin, $request) {
            $item = [];
            if ($isSuperAdmin && ! $request->filled('branch_id')) {
                $item['branch_name'] = $row->order->branch->name ?? '-';
            }
            $item['method'] = $row->method;
            $item['total_transactions'] = $row->total_transactions;
            $item['total_revenue'] = $row->total_revenue;

            return $item;
        })->toArray();

        if ($request->input('export') === 'excel') {
            return Excel::download(
                new PaymentMethodExport($paymentData),
                'laporan-metode-pembayaran-'.$startDate.'-ke-'.$endDate.'.xlsx'
            );
        }

        $branches = $isSuperAdmin ? Branch::orderBy('name')->get() : collect();

        return view('admin.reports.payments', compact('paymentReport', 'startDate', 'endDate', 'branches', 'isSuperAdmin'));
    }
}
