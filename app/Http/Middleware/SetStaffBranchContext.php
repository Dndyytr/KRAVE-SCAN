<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Services\BranchContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetStaffBranchContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $branch = $user->branch_id ? Branch::find($user->branch_id) : null;
            app(BranchContext::class)->setBranch($branch);
        }

        return $next($request);
    }
}
