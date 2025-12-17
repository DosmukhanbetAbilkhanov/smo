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
            'price' => $this->price,
            'quantity' => $this->quantity,
            'images' => $this->images,
            'nomenclature' => $this->whenLoaded('nomenclature', fn () => (new NomenclatureResource($this->nomenclature))->resolve()),
            'shop' => $this->whenLoaded('shop', fn () => (new ShopResource($this->shop))->resolve()),
            'specs' => $this->whenLoaded('specs', fn () => ProductSpecResource::collection($this->specs)->resolve()),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
