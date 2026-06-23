<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $request->user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            abort(403, 'Akun Anda telah ditangguhkan.');
        }

        // Cek apakah peran pengguna sesuai dengan peran yang diminta oleh rute secara persis (strict match)
        if ($request->user()->role?->name !== $role) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
