<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log a user activity.
     */
    public function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null,
        ?int $branchId = null
    ): ActivityLog {
        $resolvedUserId = $userId ?: Auth::id();
        $resolvedBranchId = $branchId ?: app(BranchContext::class)->getBranchId();

        if (! $resolvedBranchId && $model && isset($model->branch_id)) {
            $resolvedBranchId = $model->branch_id;
        }

        $log = new ActivityLog;
        $log->user_id = $resolvedUserId;
        $log->branch_id = $resolvedBranchId;
        $log->action = $action;

        if ($model) {
            $log->loggable_type = get_class($model);
            $log->loggable_id = $model->getKey();
        }

        $log->old_values = $oldValues;
        $log->new_values = $newValues;
        $log->ip_address = Request::ip();
        $log->user_agent = Request::userAgent();
        $log->save();

        return $log;
    }
}
