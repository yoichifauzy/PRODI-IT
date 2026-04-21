<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetPublicLocale
{
    /**
     * @var array<int, string>
     */
    private const SUPPORTED = ['id', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $queryLocale = $this->normalizeLocale($request->query('lang'));

        if ($queryLocale !== null) {
            $locale = $queryLocale;
            $request->session()->put('site_locale', $locale);
        } else {
            $locale = $this->normalizeLocale($request->session()->get('site_locale'))
                ?? $this->normalizeLocale($request->cookie('site_locale'))
                ?? $this->normalizeLocale(config('app.locale'))
                ?? 'id';

            $request->session()->put('site_locale', $locale);
        }

        App::setLocale($locale);

        $response = $next($request);

        if ($request->cookie('site_locale') !== $locale) {
            $response->headers->setCookie(cookie()->forever('site_locale', $locale));
        }

        return $response;
    }

    private function normalizeLocale(mixed $locale): ?string
    {
        if (!is_string($locale)) {
            return null;
        }

        $candidate = strtolower(trim($locale));

        return in_array($candidate, self::SUPPORTED, true) ? $candidate : null;
    }
}
