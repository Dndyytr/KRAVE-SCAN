<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\StockItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
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

        return view('dashboard', [
            'todayOrdersCount' => $todayOrdersCount,
            'todayRevenue' => $todayRevenue,
            'pendingOrdersCount' => $pendingOrdersCount,
            'lowStockCount' => $lowStockCount,
            'recentOrders' => $recentOrders,
            'lowStockItems' => $lowStockItems,
        ]);
    }
}
