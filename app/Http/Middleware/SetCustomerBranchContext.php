<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Services\BranchContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetCustomerBranchContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $branchCode = $request->route('branch_code');

        if ($branchCode) {
            $branch = Branch::where('code', strtoupper($branchCode))->first();

            if (! $branch) {
                abort(404, __('Branch not found.'));
            }

            // Set the branch context
            app(BranchContext::class)->setBranch($branch);

            // Set URL default for easier route generation
            URL::defaults(['branch_code' => $branch->code]);
        }

        return $next($request);
    }
}
