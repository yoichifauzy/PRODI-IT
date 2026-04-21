<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminSessionSecurity
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        if (!in_array($user->role, ['admin', 'super_admin'], true)) {
            return $next($request);
        }

        $fingerprint = hash('sha256', (string) ($request->userAgent() ?? 'unknown-agent'));
        $sessionFingerprint = (string) $request->session()->get('admin_fingerprint', '');

        if ($sessionFingerprint === '') {
            $request->session()->put('admin_fingerprint', $fingerprint);
        } elseif (!hash_equals($sessionFingerprint, $fingerprint)) {
            return $this->invalidateSession($request, 'Sesi tidak valid. Silakan login kembali.');
        }

        $now = now()->getTimestamp();
        $lastActivity = (int) $request->session()->get('admin_last_activity', $now);
        $idleTimeout = (int) config('session.admin_idle_timeout', 1800);

        if ($idleTimeout > 0 && ($now - $lastActivity) > $idleTimeout) {
            return $this->invalidateSession($request, 'Sesi admin berakhir karena tidak ada aktivitas.');
        }

        $request->session()->put('admin_last_activity', $now);

        return $next($request);
    }

    private function invalidateSession(Request $request, string $message): Response
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('error', $message);
    }
}
