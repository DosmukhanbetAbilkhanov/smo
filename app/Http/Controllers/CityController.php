<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Store selected city
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
        ]);

        $cityId = (int) $request->input('city_id');

        // Update authenticated user's city preference
        if ($user = $request->user()) {
            $user->update(['city_id' => $cityId]);
        }

        // Store in session for guests
        $request->session()->put('selected_city_id', $cityId);

        return back();
    }

    /**
     * Change city (from header)
     */
    public function change(Request $request): RedirectResponse
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
        ]);

        $cityId = (int) $request->input('city_id');

        // Update authenticated user's city preference
        if ($user = $request->user()) {
            $user->update(['city_id' => $cityId]);
        }

        // Update session
        $request->session()->put('selected_city_id', $cityId);

        // Return with success message
        return back()->with('success', __('City changed successfully'));
    }
}
