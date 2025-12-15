<?php

namespace App\Http\Controllers;

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

        $query = $shop->products()
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->with(['nomenclature.unit', 'nomenclature.category']);

        $products = $query->latest()
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('Shop/Show', [
            'shop' => $shop,
            'products' => $products,
        ]);
    }
}
