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

        $userRole = $request->user()->role;
        if (! $userRole) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah peran pengguna sesuai dengan peran yang diminta secara persis (backward compatibility)
        // atau memiliki hak akses khusus 'access_{role}' atau merupakan admin
        $permissionName = 'access_'.$role;
        $hasAccess = $userRole->name === $role
            || $userRole->permissions()->where('name', $permissionName)->exists();

        if (! $hasAccess) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
