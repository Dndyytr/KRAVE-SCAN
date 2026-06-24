<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::query()
            ->with(['user', 'branch'])
            ->latest();

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Apply branch filter (only relevant for Super Admin since Branch Admin is scoped)
        if (auth()->user()->branch_id === null && $request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        $logs = $query->paginate(20)->withQueryString();

        // Populate filter options
        // If Branch Admin, only get users belonging to their branch.
        $usersQuery = User::query();
        if (auth()->user()->branch_id !== null) {
            $usersQuery->where('branch_id', auth()->user()->branch_id);
        }
        $users = $usersQuery->orderBy('name')->get();

        $branches = auth()->user()->branch_id === null
            ? Branch::orderBy('name')->get()
            : collect();

        // Actions list
        $actions = ['created', 'updated', 'deleted', 'login', 'logout'];

        return view('admin.activity_logs.index', compact('logs', 'users', 'branches', 'actions'));
    }
}
