<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Services\BranchContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                abort(403, 'Akun Anda telah ditangguhkan.');
            }

            $branchId = $user->branch_id;
            if (is_null($branchId) && session()->has('active_branch_id')) {
                $branchId = session('active_branch_id');
            }

            $branch = $branchId ? Branch::find($branchId) : null;
            app(BranchContext::class)->setBranch($branch);
        }

        return $next($request);
    }
}
