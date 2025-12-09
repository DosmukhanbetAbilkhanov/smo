<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class NomenclatureResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_ru' => $this->name_ru,
            'name_kz' => $this->name_kz,
            'description_ru' => $this->description_ru,
            'description_kz' => $this->description_kz,
            'SKU' => $this->SKU,
            'GTIN' => $this->GTIN,
            'NTIN' => $this->NTIN,
            'brandname' => $this->brandname,
            'status' => $this->status->value,
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'products_count' => $this->when(
                $this->relationLoaded('products'),
                fn () => $this->products->count()
            ),
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
