<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || !in_array($user->role, ['admin', 'super_admin'], true) || !$user->is_active) {
            abort(403, 'Anda tidak memiliki akses ke halaman admin.');
        }

        return $next($request);
    }
}
