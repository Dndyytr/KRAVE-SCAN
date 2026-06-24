<?php

namespace App\Traits;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
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

            if (! $branchId && ($model instanceof Category || $model instanceof Menu)) {
                $branchId = Branch::first()?->id;
            }

            if ($branchId && ! $model->branch_id && in_array('branch_id', $model->getFillable())) {
                $model->branch_id = $branchId;
            }
        });
    }
}
