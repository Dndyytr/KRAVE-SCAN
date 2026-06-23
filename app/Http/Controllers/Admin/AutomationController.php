<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutomationLog;
use App\Models\AutomationRule;
use App\Models\Branch;
use App\Services\BranchContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutomationController extends Controller
{
    public function index(Request $request)
    {
        $branchId = app(BranchContext::class)->getBranchId();
        $isSuperAdmin = Auth::user()->role?->name === 'admin' && $branchId === null;

        $query = AutomationLog::query()->with('branch');

        if (! $isSuperAdmin) {
            $query->where('branch_id', $branchId);
        } else {
            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->input('branch_id'));
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('task_name')) {
            $query->where('task_name', $request->input('task_name'));
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(15);
        $branches = $isSuperAdmin ? Branch::all() : collect();

        return view('admin.automations.index', compact('logs', 'branches', 'isSuperAdmin'));
    }

    public function indexRules(Request $request)
    {
        $branchId = app(BranchContext::class)->getBranchId();
        $isSuperAdmin = Auth::user()->role?->name === 'admin' && $branchId === null;

        $query = AutomationRule::query()->with('branch');

        if (! $isSuperAdmin) {
            $query->where('branch_id', $branchId);
        } else {
            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->input('branch_id'));
            }
        }

        $rules = $query->orderBy('created_at', 'desc')->paginate(15);
        $branches = $isSuperAdmin ? Branch::all() : collect();

        return view('admin.automations.rules.index', compact('rules', 'branches', 'isSuperAdmin'));
    }

    public function createRule()
    {
        $branchId = app(BranchContext::class)->getBranchId();
        $isSuperAdmin = Auth::user()->role?->name === 'admin' && $branchId === null;
        $branches = $isSuperAdmin ? Branch::all() : collect();

        $triggers = [
            'App\Events\OrderPaid' => 'Order Paid (Pembayaran Selesai)',
        ];

        $actions = [
            'App\Jobs\GenerateReceiptJob' => 'Generate Receipt (Pembuatan Struk)',
            'App\Jobs\CheckStockLevelsJob' => 'Check Stock Levels (Cek Stok Rendah)',
        ];

        $conditionTypes = [
            'always' => 'Always Trigger (Tanpa Kondisi)',
            'payment_method_equals' => 'Payment Method Equals (Metode Pembayaran Sama)',
            'min_order_amount' => 'Minimum Order Amount (Minimal Total Belanja)',
        ];

        return view('admin.automations.rules.create_edit', compact('branches', 'isSuperAdmin', 'triggers', 'actions', 'conditionTypes'));
    }

    public function storeRule(Request $request)
    {
        $branchId = app(BranchContext::class)->getBranchId();
        $isSuperAdmin = Auth::user()->role?->name === 'admin' && $branchId === null;

        $rules = [
            'name' => 'required|string|max:255',
            'trigger_event' => 'required|string|max:255',
            'action_job' => 'required|string|max:255',
            'condition_type' => 'required|string|in:always,payment_method_equals,min_order_amount',
        ];

        if ($isSuperAdmin) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        $validated = $request->validate($rules);

        if (! $isSuperAdmin) {
            $validated['branch_id'] = $branchId;
        }

        $validated['is_active'] = $request->has('is_active');

        // Extract condition value based on type
        $conditionValue = null;
        if ($validated['condition_type'] === 'payment_method_equals') {
            $conditionValue = ['payment_method' => $request->input('payment_method')];
        } elseif ($validated['condition_type'] === 'min_order_amount') {
            $conditionValue = ['min_amount' => (int) $request->input('min_amount')];
        }
        $validated['condition_value'] = $conditionValue;

        AutomationRule::create($validated);

        return redirect()->route('admin.automations.rules.index')->with('success', 'Aturan otomatisasi berhasil dibuat.');
    }

    public function editRule(AutomationRule $rule)
    {
        $branchId = app(BranchContext::class)->getBranchId();
        $isSuperAdmin = Auth::user()->role?->name === 'admin' && $branchId === null;
        $branches = $isSuperAdmin ? Branch::all() : collect();

        // Check ownership if not super admin
        if (! $isSuperAdmin && $rule->branch_id !== $branchId) {
            abort(403, 'Unauthorized action.');
        }

        $triggers = [
            'App\Events\OrderPaid' => 'Order Paid (Pembayaran Selesai)',
        ];

        $actions = [
            'App\Jobs\GenerateReceiptJob' => 'Generate Receipt (Pembuatan Struk)',
            'App\Jobs\CheckStockLevelsJob' => 'Check Stock Levels (Cek Stok Rendah)',
        ];

        $conditionTypes = [
            'always' => 'Always Trigger (Tanpa Kondisi)',
            'payment_method_equals' => 'Payment Method Equals (Metode Pembayaran Sama)',
            'min_order_amount' => 'Minimum Order Amount (Minimal Total Belanja)',
        ];

        return view('admin.automations.rules.create_edit', compact('rule', 'branches', 'isSuperAdmin', 'triggers', 'actions', 'conditionTypes'));
    }

    public function updateRule(Request $request, AutomationRule $rule)
    {
        $branchId = app(BranchContext::class)->getBranchId();
        $isSuperAdmin = Auth::user()->role?->name === 'admin' && $branchId === null;

        if (! $isSuperAdmin && $rule->branch_id !== $branchId) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'trigger_event' => 'required|string|max:255',
            'action_job' => 'required|string|max:255',
            'condition_type' => 'required|string|in:always,payment_method_equals,min_order_amount',
        ];

        if ($isSuperAdmin) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        $validated = $request->validate($rules);

        if (! $isSuperAdmin) {
            $validated['branch_id'] = $branchId;
        }

        $validated['is_active'] = $request->has('is_active');

        // Extract condition value based on type
        $conditionValue = null;
        if ($validated['condition_type'] === 'payment_method_equals') {
            $conditionValue = ['payment_method' => $request->input('payment_method')];
        } elseif ($validated['condition_type'] === 'min_order_amount') {
            $conditionValue = ['min_amount' => (int) $request->input('min_amount')];
        }
        $validated['condition_value'] = $conditionValue;

        $rule->update($validated);

        return redirect()->route('admin.automations.rules.index')->with('success', 'Aturan otomatisasi berhasil diperbarui.');
    }

    public function destroyRule(AutomationRule $rule)
    {
        $branchId = app(BranchContext::class)->getBranchId();
        $isSuperAdmin = Auth::user()->role?->name === 'admin' && $branchId === null;

        if (! $isSuperAdmin && $rule->branch_id !== $branchId) {
            abort(403, 'Unauthorized action.');
        }

        $rule->delete();

        return redirect()->route('admin.automations.rules.index')->with('success', 'Aturan otomatisasi berhasil dihapus.');
    }

    public function toggleRule(AutomationRule $rule)
    {
        $branchId = app(BranchContext::class)->getBranchId();
        $isSuperAdmin = Auth::user()->role?->name === 'admin' && $branchId === null;

        if (! $isSuperAdmin && $rule->branch_id !== $branchId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $rule->update(['is_active' => ! $rule->is_active]);

        return response()->json(['success' => true, 'is_active' => $rule->is_active]);
    }
}
