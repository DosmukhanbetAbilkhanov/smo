<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\NomenclatureResource;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): JsonResponse
    {
        $search = request('search');
        $limit = request('limit', null);

        if ($search) {
            // Search in both Russian and Kazakh names
            $query = Category::with(['specs', 'children'])
                ->where(function ($q) use ($search) {
                    $q->where('name_ru', 'like', "%{$search}%")
                        ->orWhere('name_kz', 'like', "%{$search}%");
                });

            if ($limit) {
                $query->limit((int) $limit);
            }

            $categories = $query->get();
        } else {
            // Return cached top-level categories
            $categories = Cache::remember('api.categories.index', 3600, function () {
                return Category::with(['specs', 'children'])
                    ->whereNull('parent_id')
                    ->get();
            });
        }

        return ApiResponse::success(
            CategoryResource::collection($categories),
            'Categories retrieved successfully'
        );
    }

    /**
     * Display the specified category by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $category = Cache::remember("api.categories.{$slug}", 3600, function () use ($slug) {
            return Category::with(['specs', 'children', 'nomenclatures'])
                ->where('slug', $slug)
                ->first();
        });

        if (! $category) {
            return ApiResponse::notFound('Category not found');
        }

        return ApiResponse::success(
            new CategoryResource($category),
            'Category retrieved successfully'
        );
    }

    /**
     * Display nomenclatures for the specified category.
     */
    public function nomenclatures(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->first();

        if (! $category) {
            return ApiResponse::notFound('Category not found');
        }

        $nomenclatures = Cache::remember(
            "api.categories.{$slug}.nomenclatures",
            3600,
            fn () => $category->nomenclatures()->with(['unit', 'category'])->get()
        );

        return ApiResponse::success(
            NomenclatureResource::collection($nomenclatures),
            'Nomenclatures retrieved successfully'
        );
    }
}
