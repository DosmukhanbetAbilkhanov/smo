<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\QueryBuilders\ProductQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    /**
     * Display all categories with hierarchy.
     */
    public function categories(): Response
    {
        // Cache categories for 30 minutes
        $categories = Cache::remember('catalog.categories', 1800, function () {
            return Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('name_ru');
                }])
                ->orderBy('name_ru')
                ->get();
        });

        return Inertia::render('Catalog/Categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Display a specific category and its products.
     */
    public function show(Request $request, string $slug): Response
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->with(['children' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('name_ru');
            }])
            ->firstOrFail();

        // Get products in this category and its children
        $categoryIds = [$category->id];
        if ($category->children->isNotEmpty()) {
            $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());
        }

        // Get selected city ID
        $cityId = $this->getSelectedCityId($request);

        $query = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->whereHas('nomenclature', function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            })
            ->with(['nomenclature.unit', 'nomenclature.category', 'shop.city']);

        // Filter by city if selected
        if ($cityId) {
            $query->whereHas('shop', function ($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        }

        $products = $query->latest()
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('Catalog/CategoryShow', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    /**
     * Display all products with filters and sorting.
     */
    public function products(Request $request): Response
    {
        // Inject city_id if not present
        if (! $request->has('city_id')) {
            $request->merge(['city_id' => $this->getSelectedCityId($request)]);
        }

        $queryBuilder = ProductQueryBuilder::make()
            ->applyFilters($request)
            ->applySort($request);

        $products = $queryBuilder->paginate(24);

        $filters = ProductQueryBuilder::getFiltersFromRequest($request);

        return Inertia::render('Catalog/Products', [
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    /**
     * Search products by query.
     */
    public function search(Request $request): Response
    {
        $searchQuery = $request->input('q', '');

        // Inject city_id if not present
        if (! $request->has('city_id')) {
            $request->merge(['city_id' => $this->getSelectedCityId($request)]);
        }

        $queryBuilder = ProductQueryBuilder::make()
            ->applySearch($searchQuery)
            ->applyFilters($request)
            ->applySort($request);

        $products = $queryBuilder->paginate(24);

        $filters = ProductQueryBuilder::getFiltersFromRequest($request);

        return Inertia::render('Catalog/Search', [
            'products' => $products,
            'filters' => $filters,
            'query' => $searchQuery,
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
