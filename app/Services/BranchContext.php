<?php

namespace App\Services;

use App\Models\Branch;

class BranchContext
{
    protected ?Branch $branch = null;

    /**
     * Set the current active branch.
     */
    public function setBranch(?Branch $branch): void
    {
        $this->branch = $branch;
    }

    /**
     * Get the current active branch.
     */
    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    /**
     * Get the ID of the current active branch.
     */
    public function getBranchId(): ?int
    {
        return $this->branch?->id;
    }

    /**
     * Get branch-isolated cache key.
     */
    public function cacheKey(string $key): string
    {
        $branchId = $this->getBranchId() ?? 'global';

        return "branch_{$branchId}_{$key}";
    }
}
