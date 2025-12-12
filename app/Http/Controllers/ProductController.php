<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display the product detail page with all related data.
     */
    public function show(int $id): Response
    {
        $product = Product::where('id', $id)
            ->where('is_active', true)
            ->with([
                'nomenclature.category',
                'nomenclature.unit',
                'shop',
                'specs',
            ])
            ->firstOrFail();

        // Get similar products (same category, different shop or product)
        $similarProducts = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->where('id', '!=', $product->id)
            ->whereHas('nomenclature', function ($query) use ($product) {
                $query->where('category_id', $product->nomenclature->category_id);
            })
            ->with(['nomenclature.unit', 'shop'])
            ->limit(6)
            ->get();

        return Inertia::render('Catalog/ProductDetail', [
            'product' => $product,
            'similarProducts' => $similarProducts,
        ]);
    }
}
