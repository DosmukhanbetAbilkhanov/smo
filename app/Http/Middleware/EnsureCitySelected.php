<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCitySelected
{
    /**
     * Routes that don't require city selection
     */
    protected array $except = [
        'city.select',
        'city.store',
        'city.change',
        'login',
        'register',
        'password.*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if route is in exception list
        if ($this->shouldPassThrough($request)) {
            return $next($request);
        }

        // Check if city is selected (session for guests, database for authenticated)
        $cityId = $this->getSelectedCityId($request);

        if (! $cityId) {
            // Redirect to city selection page
            return redirect()->route('city.select');
        }

        return $next($request);
    }

    protected function shouldPassThrough(Request $request): bool
    {
        foreach ($this->except as $pattern) {
            if ($request->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function getSelectedCityId(Request $request): ?int
    {
        // Priority 1: Authenticated user's city preference
        if ($request->user()?->city_id) {
            return $request->user()->city_id;
        }

        // Priority 2: Session (for guests)
        return $request->session()->get('selected_city_id');
    }
}
