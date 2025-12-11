<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductResource;
use App\Http\Responses\ApiResponse;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Display a listing of products with search and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);

        // Build cache key from all query parameters
        $cacheKey = 'api.products.index.'.md5(\json_encode($request->all()));

        $result = Cache::remember($cacheKey, 1800, function () use ($request, $perPage) {
            $query = Product::query()
                ->with(['nomenclature.category', 'nomenclature.unit', 'shop.city', 'specs']);

            // Search by name
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name_ru', 'like', "%{$search}%")
                        ->orWhere('name_kz', 'like', "%{$search}%");
                });
            }

            // Filter by price range
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->input('min_price'));
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->input('max_price'));
            }

            // Filter by nomenclature
            if ($request->filled('nomenclature_id')) {
                $query->where('nomenclature_id', $request->input('nomenclature_id'));
            }

            // Filter by shop
            if ($request->filled('shop_id')) {
                $query->where('shop_id', $request->input('shop_id'));
            }

            // Filter by category (via nomenclature)
            if ($request->filled('category_id')) {
                $query->whereHas('nomenclature', function ($q) use ($request) {
                    $q->where('category_id', $request->input('category_id'));
                });
            }

            // Filter by stock availability
            if ($request->boolean('in_stock')) {
                $query->where('quantity', '>', 0);
            }

            // Filter by specs
            if ($request->filled('specs')) {
                $specs = $request->input('specs');
                if (is_array($specs)) {
                    foreach ($specs as $specName => $specValue) {
                        $query->whereHas('specs', function ($q) use ($specName, $specValue) {
                            $q->where('spec_name', $specName)
                                ->where('spec_value', $specValue);
                        });
                    }
                }
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            $allowedSortFields = ['price', 'quantity', 'created_at', 'name_ru', 'name_kz'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            return $query->paginate($perPage);
        });

        return ApiResponse::success(
            [
                'data' => ProductResource::collection($result->items()),
                'pagination' => [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                ],
            ],
            'Products retrieved successfully'
        );
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): JsonResponse
    {
        $product = Cache::remember("api.products.{$id}", 1800, function () use ($id) {
            return Product::with(['nomenclature.category', 'nomenclature.unit', 'shop.city', 'specs'])
                ->find($id);
        });

        if (! $product) {
            return ApiResponse::notFound('Product not found');
        }

        return ApiResponse::success(
            new ProductResource($product),
            'Product retrieved successfully'
        );
    }

    /**
     * Search products for autocomplete suggestions.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $limit = min($request->input('limit', 8), 20); // Max 20 results

        if (strlen($query) < 2) {
            return ApiResponse::success(
                ['data' => []],
                'Query too short'
            );
        }

        $cacheKey = 'api.products.search.'.md5($query.'.'.$limit);

        $products = Cache::remember($cacheKey, 300, function () use ($query, $limit) {
            return Product::where('is_active', true)
                ->where('quantity', '>', 0)
                ->where(function ($q) use ($query) {
                    $q->where('name_ru', 'like', "%{$query}%")
                        ->orWhere('name_kz', 'like', "%{$query}%")
                        ->orWhereHas('nomenclature', function ($nq) use ($query) {
                            $nq->where('name_ru', 'like', "%{$query}%")
                                ->orWhere('name_kz', 'like', "%{$query}%");
                        });
                })
                ->with(['shop'])
                ->limit($limit)
                ->get();
        });

        return ApiResponse::success(
            ['data' => ProductResource::collection($products)],
            'Search results retrieved successfully'
        );
    }
}
