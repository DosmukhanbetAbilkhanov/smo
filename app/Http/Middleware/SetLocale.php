<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getPreferredLocale($request);

        if ($this->isValidLocale($locale)) {
            App::setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }

    /**
     * Get the preferred locale from various sources
     */
    protected function getPreferredLocale(Request $request): string
    {
        // Priority 1: Query parameter (for locale switching)
        if ($request->has('locale') && $this->isValidLocale($request->get('locale'))) {
            return $request->get('locale');
        }

        // Priority 2: Session
        if ($request->session()->has('locale')) {
            return $request->session()->get('locale');
        }

        // Priority 3: Cookie
        if ($request->hasCookie('locale')) {
            return $request->cookie('locale');
        }

        // Priority 4: Accept-Language header
        $browserLocale = $request->getPreferredLanguage(config('app.available_locales'));
        if ($browserLocale) {
            return $browserLocale;
        }

        // Fallback to default locale
        return config('app.locale');
    }

    /**
     * Check if the given locale is valid
     */
    protected function isValidLocale(?string $locale): bool
    {
        return $locale && in_array($locale, config('app.available_locales'));
    }
}
