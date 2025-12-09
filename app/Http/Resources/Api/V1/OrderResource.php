<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class OrderResource extends BaseResource
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
            'order_number' => $this->order_number,
            'user_id' => $this->user_id,
            'shop_id' => $this->shop_id,
            'shop' => new ShopResource($this->whenLoaded('shop')),
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'delivery_address' => $this->delivery_address,
            'delivery_entry' => $this->delivery_entry,
            'delivery_floor' => $this->delivery_floor,
            'delivery_apartment' => $this->delivery_apartment,
            'delivery_intercom' => $this->delivery_intercom,
            'delivery_city_id' => $this->delivery_city_id,
            'delivery_city' => new CityResource($this->whenLoaded('deliveryCity')),
            'contact_phone' => $this->contact_phone,
            'delivery_notes' => $this->delivery_notes,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'items_count' => $this->items_count ?? 0,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
