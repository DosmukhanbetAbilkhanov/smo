<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class UnitResource extends BaseResource
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
            'abbreviation_ru' => $this->abbreviation_ru,
            'abbreviation_kz' => $this->abbreviation_kz,
            'shortname_ru' => $this->shortname_ru,
            'shortname_kz' => $this->shortname_kz,
        ];
    }
}
