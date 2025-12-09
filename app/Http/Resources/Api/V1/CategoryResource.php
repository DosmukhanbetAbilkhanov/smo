<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class CategoryResource extends BaseResource
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
            'slug' => $this->slug,
            'icon' => $this->icon,
            'parent_id' => $this->parent_id,
            'specs' => SpecResource::collection($this->whenLoaded('specs')),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'nomenclatures_count' => $this->when(
                $this->relationLoaded('nomenclatures'),
                fn () => $this->nomenclatures->count()
            ),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
