<?php

namespace App\Services;

use App\Jobs\CheckStockLevelsJob;
use App\Jobs\GenerateReceiptJob;
use App\Models\AutomationLog;
use App\Models\AutomationRule;
use App\Models\Scopes\BranchScope;
use Illuminate\Support\Facades\Log;

class AutomationEngine
{
    /**
     * Trigger automations for a given event.
     */
    public static function trigger($event): void
    {
        $eventClass = get_class($event);

        // Resolve branch_id
        $branchId = null;
        if (property_exists($event, 'order') && $event->order) {
            $branchId = $event->order->branch_id;
        } elseif (property_exists($event, 'payment') && $event->payment) {
            $branchId = $event->payment->order->branch_id ?? null;
        }

        if (! $branchId) {
            $branchId = app(BranchContext::class)->getBranchId();
        }

        if (! $branchId) {
            Log::warning("AutomationEngine: Could not resolve branch_id for event {$eventClass}. Skipping.");

            return;
        }

        // Fetch active rules for this branch and event
        $rules = AutomationRule::withoutGlobalScope(BranchScope::class)
            ->where('branch_id', $branchId)
            ->where('trigger_event', $eventClass)
            ->where('is_active', true)
            ->get();

        foreach ($rules as $rule) {
            static::evaluateAndExecute($rule, $event);
        }
    }

    /**
     * Evaluate rule conditions and execute the job.
     */
    protected static function evaluateAndExecute(AutomationRule $rule, $event): void
    {
        $eventClass = get_class($event);
        $actionJob = $rule->action_job;

        // 1. Resolve targets from event
        $order = property_exists($event, 'order') ? $event->order : null;
        $payment = property_exists($event, 'payment') ? $event->payment : null;

        // 2. Evaluate conditions
        $isConditionMet = false;
        switch ($rule->condition_type) {
            case 'always':
                $isConditionMet = true;
                break;
            case 'payment_method_equals':
                if ($payment) {
                    $targetMethod = $rule->condition_value['payment_method'] ?? null;
                    $isConditionMet = (strtolower($payment->method) === strtolower($targetMethod));
                }
                break;
            case 'min_order_amount':
                if ($order) {
                    $minAmount = $rule->condition_value['min_amount'] ?? 0;
                    $isConditionMet = ($order->total_amount >= $minAmount);
                }
                break;
            default:
                Log::warning("AutomationEngine: Unknown condition type '{$rule->condition_type}' for rule '{$rule->name}'");
                break;
        }

        if (! $isConditionMet) {
            return;
        }

        // 3. Resolve target entity ID for idempotency key
        $targetId = null;
        $jobArg = null;

        if ($actionJob === GenerateReceiptJob::class) {
            if ($payment) {
                $targetId = 'payment_'.$payment->id;
                $jobArg = $payment;
            }
        } elseif ($actionJob === CheckStockLevelsJob::class) {
            if ($order) {
                $targetId = 'order_'.$order->id;
                $jobArg = $order;
            }
        } else {
            // Fallback resolver
            if ($order) {
                $targetId = 'order_'.$order->id;
                $jobArg = $order;
            } elseif ($payment) {
                $targetId = 'payment_'.$payment->id;
                $jobArg = $payment;
            }
        }

        if (! $jobArg) {
            Log::warning("AutomationEngine: Job argument not resolved for action {$actionJob} on rule '{$rule->name}'");

            return;
        }

        $idempotencyKey = "{$eventClass}:{$targetId}:{$actionJob}";

        // 4. Check Idempotency
        $existingLog = AutomationLog::withoutGlobalScope(BranchScope::class)
            ->where('idempotency_key', $idempotencyKey)
            ->whereIn('status', ['success', 'pending', 'warning'])
            ->first();

        if ($existingLog) {
            Log::info("AutomationEngine: Skipping duplicate execution for key: {$idempotencyKey}");

            return;
        }

        // 5. Create initial pending log
        $log = AutomationLog::create([
            'branch_id' => $rule->branch_id,
            'task_name' => $rule->name,
            'status' => 'pending',
            'idempotency_key' => $idempotencyKey,
            'details' => [
                'rule_id' => $rule->id,
                'action_job' => $actionJob,
                'triggered_by' => $eventClass,
            ],
        ]);

        try {
            // Dispatch the job with log ID parameter
            $actionJob::dispatch($jobArg, $log->id);
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'details' => array_merge($log->details ?? [], ['error' => $e->getMessage()]),
            ]);
            throw $e;
        }
    }
}
