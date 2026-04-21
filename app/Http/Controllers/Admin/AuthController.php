<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    /**
     * @throws ValidationException
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Security hardening: do not persist login via remember-me for admin panel.
        if (!Auth::attempt($credentials, false)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak valid.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();
        if ($user === null || !in_array($user->role, ['admin', 'super_admin'], true) || !$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akun Anda tidak memiliki akses admin.',
            ]);
        }

        $request->session()->put('admin_fingerprint', hash('sha256', (string) ($request->userAgent() ?? 'unknown-agent')));
        $request->session()->put('admin_last_activity', now()->getTimestamp());

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
