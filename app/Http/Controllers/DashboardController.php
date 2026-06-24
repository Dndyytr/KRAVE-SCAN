<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Switch current branch context for Super Admin.
     */
    public function switchBranch(Request $request)
    {
        $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if (auth()->user()->branch_id === null) {
            if ($request->filled('branch_id')) {
                session()->put('active_branch_id', $request->branch_id);
            } else {
                session()->forget('active_branch_id');
            }
        }

        return redirect()->back()->with('success', 'Konteks cabang berhasil diubah.');
    }

    /**
     * Display the unified dashboard.
     */
    public function index(Request $request)
    {
        $today = today();

        // 1. Total Orders Today
        $todayOrdersCount = Order::whereDate('created_at', $today)->count();

        // 2. Total Revenue Today
        $todayRevenue = (float) Payment::where('status', 'success')
            ->whereDate('created_at', $today)
            ->sum('amount');

        // 3. Pending Orders Count (All time/active)
        $pendingOrdersCount = Order::where('status', 'pending')->count();

        // 4. Low Stock Items Count
        $lowStockCount = StockItem::whereColumn('quantity', '<=', 'minimum_quantity')->count();

        // 5. Recent Orders (Latest 5)
        $recentOrders = Order::with(['branch'])
            ->latest()
            ->take(5)
            ->get();

        // 6. Low Stock Items List (Latest 5 warnings)
        $lowStockItems = StockItem::whereColumn('quantity', '<=', 'minimum_quantity')
            ->latest()
            ->take(5)
            ->get();

        // 7. 7-day revenue trend data for ECharts
        $revenueTrend = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'date' => $date->translatedFormat('d M'),
                'revenue' => (float) Payment::where('status', 'success')
                    ->whereDate('created_at', $date)
                    ->sum('amount'),
            ];
        });

        // 8. 7-day orders trend data for ECharts
        $ordersTrend = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'date' => $date->translatedFormat('d M'),
                'orders' => Order::whereDate('created_at', $date)->count(),
            ];
        });

        // 9. Yesterday comparisons for trend arrows
        $yesterday = today()->subDay();
        $yesterdayRevenue = (float) Payment::where('status', 'success')
            ->whereDate('created_at', $yesterday)
            ->sum('amount');
        $yesterdayOrdersCount = Order::whereDate('created_at', $yesterday)->count();

        return view('dashboard', [
            'todayOrdersCount' => $todayOrdersCount,
            'todayRevenue' => $todayRevenue,
            'pendingOrdersCount' => $pendingOrdersCount,
            'lowStockCount' => $lowStockCount,
            'recentOrders' => $recentOrders,
            'lowStockItems' => $lowStockItems,
            'revenueTrend' => $revenueTrend,
            'ordersTrend' => $ordersTrend,
            'yesterdayRevenue' => $yesterdayRevenue,
            'yesterdayOrdersCount' => $yesterdayOrdersCount,
        ]);
    }
}
