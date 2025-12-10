<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the homepage with featured categories and products.
     */
    public function index(): Response
    {
        // Cache featured data for 1 hour
        $featuredData = Cache::remember('homepage.featured', 3600, function () {
            // Get top-level active categories with children (max 8)
            $categories = Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('name_ru');
                }])
                ->orderBy('name_ru')
                ->limit(8)
                ->get();

            // Get popular/recent products (max 12)
            $products = Product::where('is_active', true)
                ->where('quantity', '>', 0)
                ->with(['nomenclature.unit', 'shop'])
                ->latest()
                ->limit(12)
                ->get();

            return [
                'categories' => $categories,
                'products' => $products,
            ];
        });

        return Inertia::render('Welcome', [
            'featuredCategories' => $featuredData['categories'],
            'popularProducts' => $featuredData['products'],
        ]);
    }
}
