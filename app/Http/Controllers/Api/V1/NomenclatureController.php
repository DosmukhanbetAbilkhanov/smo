<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\NomenclatureResource;
use App\Http\Responses\ApiResponse;
use App\Models\Nomenclature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NomenclatureController extends Controller
{
    /**
     * Display a listing of nomenclatures.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $cacheKey = "api.nomenclatures.index.page.{$request->input('page', 1)}.per_page.{$perPage}";

        $nomenclatures = Cache::remember($cacheKey, 1800, function () use ($perPage) {
            return Nomenclature::with(['unit', 'category'])
                ->paginate($perPage);
        });

        return ApiResponse::success(
            [
                'data' => NomenclatureResource::collection($nomenclatures->items()),
                'pagination' => [
                    'current_page' => $nomenclatures->currentPage(),
                    'last_page' => $nomenclatures->lastPage(),
                    'per_page' => $nomenclatures->perPage(),
                    'total' => $nomenclatures->total(),
                ],
            ],
            'Nomenclatures retrieved successfully'
        );
    }

    /**
     * Display the specified nomenclature.
     */
    public function show(int $id): JsonResponse
    {
        $nomenclature = Cache::remember("api.nomenclatures.{$id}", 1800, function () use ($id) {
            return Nomenclature::with(['unit', 'category'])->find($id);
        });

        if (! $nomenclature) {
            return ApiResponse::notFound('Nomenclature not found');
        }

        return ApiResponse::success(
            new NomenclatureResource($nomenclature),
            'Nomenclature retrieved successfully'
        );
    }
}
