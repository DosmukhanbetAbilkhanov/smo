<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    /**
     * Display the shop detail page with its products.
     */
    public function show(Request $request, int $id): Response
    {
        $shop = Shop::with('city', 'company')->findOrFail($id);

        $searchQuery = $request->input('search');
        $categoryId = $request->input('category_id');

        // Build products query
        $query = $shop->products()
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->with(['nomenclature.unit', 'nomenclature.category']);

        // Apply search filter
        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name_ru', 'like', "%{$searchQuery}%")
                    ->orWhere('name_kz', 'like', "%{$searchQuery}%")
                    ->orWhereHas('nomenclature', function ($nq) use ($searchQuery) {
                        $nq->where('name_ru', 'like', "%{$searchQuery}%")
                            ->orWhere('name_kz', 'like', "%{$searchQuery}%");
                    });
            });
        }

        // Apply category filter
        if ($categoryId) {
            $query->whereHas('nomenclature', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $products = $query->latest()
            ->paginate(24)
            ->withQueryString();

        // Get all categories from products in this shop
        $categoryIds = $shop->products()
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->with('nomenclature')
            ->get()
            ->pluck('nomenclature.category_id')
            ->filter()
            ->unique()
            ->values();

        // Get parent categories and their children
        $allCategoryIds = Category::whereIn('id', $categoryIds)
            ->orWhereIn('parent_id', $categoryIds)
            ->pluck('id')
            ->unique();

        // Get unique parent IDs
        $parentIds = Category::whereIn('id', $allCategoryIds)
            ->whereNotNull('parent_id')
            ->pluck('parent_id')
            ->unique()
            ->merge(
                Category::whereIn('id', $allCategoryIds)
                    ->whereNull('parent_id')
                    ->pluck('id')
            )
            ->unique();

        $categories = Category::whereIn('id', $parentIds)
            ->where('is_active', true)
            ->with(['children' => function ($query) use ($allCategoryIds) {
                $query->whereIn('id', $allCategoryIds)
                    ->where('is_active', true)
                    ->orderBy('name_ru');
            }])
            ->orderBy('name_ru')
            ->get();

        return Inertia::render('Shop/Show', [
            'shop' => $shop,
            'products' => $products,
            'categories' => $categories,
            'searchQuery' => $searchQuery,
            'selectedCategoryId' => $categoryId ? (int) $categoryId : null,
        ]);
    }
}
