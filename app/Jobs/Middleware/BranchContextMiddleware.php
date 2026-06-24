<?php

namespace App\Jobs\Middleware;

use App\Models\Branch;
use App\Services\BranchContext;

class BranchContextMiddleware
{
    protected ?int $branchId;

    /**
     * Create a new middleware instance.
     */
    public function __construct(?int $branchId)
    {
        $this->branchId = $branchId;
    }

    /**
     * Process the queued job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        if ($this->branchId !== null) {
            $branch = Branch::find($this->branchId);
            if ($branch) {
                app(BranchContext::class)->setBranch($branch);
            }
        }

        return $next($job);
    }
}
