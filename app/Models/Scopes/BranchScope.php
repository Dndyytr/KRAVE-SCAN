<?php

namespace App\Models\Scopes;

use App\Models\Payment;
use App\Models\Receipt;
use App\Services\BranchContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $branchId = app(BranchContext::class)->getBranchId();

        // If no branch is active in the context, we bypass the filter (Super Admin Support)
        if ($branchId === null) {
            return;
        }

        if ($model instanceof Payment) {
            $builder->whereHas('order', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            });
        } elseif ($model instanceof Receipt) {
            $builder->whereHas('payment.order', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            });
        } else {
            $builder->where($model->getTable().'.branch_id', $branchId);
        }
    }
}
