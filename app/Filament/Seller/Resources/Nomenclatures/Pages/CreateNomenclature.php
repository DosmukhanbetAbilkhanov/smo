<?php

namespace App\Filament\Seller\Resources\Nomenclatures\Pages;

use App\Enums\NomenclatureStatus;
use App\Filament\Seller\Resources\Nomenclatures\NomenclatureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNomenclature extends CreateRecord
{
    protected static string $resource = NomenclatureResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = NomenclatureStatus::Pending;

        return $data;
    }
}
