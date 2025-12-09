<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class ProductResource extends BaseResource
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
            'SKU' => $this->SKU,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'images' => $this->images,
            'nomenclature' => new NomenclatureResource($this->whenLoaded('nomenclature')),
            'shop' => new ShopResource($this->whenLoaded('shop')),
            'specs' => ProductSpecResource::collection($this->whenLoaded('specs')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
