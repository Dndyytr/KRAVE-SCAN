<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Payment;
use App\Models\Scopes\BranchScope;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = is_null($user->branch_id);

        // Apply BranchScope bypass only for Super Admin to query across branches
        if ($isSuperAdmin) {
            $query = Payment::withoutGlobalScope(BranchScope::class)
                ->with(['order.branch', 'receipts']);
        } else {
            $query = Payment::with(['order.branch', 'receipts']);
        }

        // Apply Filters
        if ($isSuperAdmin && $request->filled('branch_id')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Calculate aggregates for success payments from the filtered query
        $totalRevenue = (clone $query)->where('status', 'success')->sum('amount');
        $totalCash = (clone $query)->where('status', 'success')->where('method', 'cash')->sum('amount');
        $totalQris = (clone $query)->where('status', 'success')->where('method', 'qris')->sum('amount');

        // Paginate results
        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $branches = $isSuperAdmin ? Branch::all() : collect();

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'branches' => $branches,
            'totalRevenue' => $totalRevenue,
            'totalCash' => $totalCash,
            'totalQris' => $totalQris,
            'isSuperAdmin' => $isSuperAdmin,
            'currentBranchId' => $request->input('branch_id'),
            'currentMethod' => $request->input('method'),
            'currentStatus' => $request->input('status'),
            'startDate' => $request->input('start_date'),
            'endDate' => $request->input('end_date'),
        ]);
    }
}
