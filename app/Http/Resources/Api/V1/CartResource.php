<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class CartResource extends BaseResource
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
            'user_id' => $this->user_id,
            'shop_id' => $this->shop_id,
            'shop' => $this->whenLoaded('shop', fn () => (new ShopResource($this->shop))->resolve()),
            'items' => $this->whenLoaded('items', fn () => CartItemResource::collection($this->items)->resolve()),
            'items_count' => $this->items_count ?? 0,
            'total' => $this->total ?? 0,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
