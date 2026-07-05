<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Cek apakah role user yang login diizinkan mengakses route ini.
     *
     * Cara pakai di routes/web.php:
     *   ->middleware('role:admin')          → hanya admin
     *   ->middleware('role:guru')           → hanya guru
     *   ->middleware('role:admin,guru')     → admin ATAU guru (keduanya boleh)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}