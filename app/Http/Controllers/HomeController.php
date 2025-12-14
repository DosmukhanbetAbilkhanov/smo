<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the homepage with featured categories and products.
     */
    public function index(Request $request): Response
    {
        // Get selected city ID
        $cityId = $this->getSelectedCityId($request);

        // Cache categories for 1 hour (city-independent)
        $categories = Cache::remember('homepage.categories', 3600, function () {
            return Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('name_ru');
                }])
                ->orderBy('name_ru')
                ->limit(8)
                ->get();
        });

        // Get popular/recent products for selected city (max 12)
        $productsQuery = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->with(['nomenclature.unit', 'shop.city']);

        // Filter by city if selected
        if ($cityId) {
            $productsQuery->whereHas('shop', function ($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        }

        $products = $productsQuery->latest()
            ->limit(12)
            ->get();

        return Inertia::render('Welcome', [
            'featuredCategories' => $categories,
            'popularProducts' => $products,
        ]);
    }

    /**
     * Get the selected city ID from the request
     */
    protected function getSelectedCityId(Request $request): ?int
    {
        return $request->user()?->city_id
            ?? $request->session()->get('selected_city_id');
    }
}
