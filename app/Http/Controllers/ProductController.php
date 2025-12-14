<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display the product detail page with all related data.
     */
    public function show(Request $request, int $id): Response
    {
        // Get selected city ID
        $cityId = $this->getSelectedCityId($request);

        $product = Product::where('id', $id)
            ->where('is_active', true)
            ->with([
                'nomenclature.category',
                'nomenclature.unit',
                'shop.city',
                'specs',
            ])
            ->firstOrFail();

        // Get similar products from the same city (same category, different product)
        $similarProductsQuery = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->where('id', '!=', $product->id)
            ->whereHas('nomenclature', function ($query) use ($product) {
                $query->where('category_id', $product->nomenclature->category_id);
            })
            ->with(['nomenclature.unit', 'shop.city']);

        // Filter similar products by city if selected
        if ($cityId) {
            $similarProductsQuery->whereHas('shop', function ($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        }

        $similarProducts = $similarProductsQuery->limit(6)->get();

        return Inertia::render('Catalog/ProductDetail', [
            'product' => $product,
            'similarProducts' => $similarProducts,
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
