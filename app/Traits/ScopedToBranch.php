<?php

namespace App\Traits;

use App\Models\Scopes\BranchScope;
use App\Services\BranchContext;

trait ScopedToBranch
{
    /**
     * Boot the ScopedToBranch trait for a model.
     */
    public static function bootScopedToBranch(): void
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function ($model) {
            $branchId = app(BranchContext::class)->getBranchId();

            if ($branchId && ! $model->branch_id && in_array('branch_id', $model->getFillable())) {
                $model->branch_id = $branchId;
            }
        });
    }
}
