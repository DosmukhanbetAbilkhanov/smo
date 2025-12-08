<?php

namespace App\Filament\Seller\Resources\Nomenclatures\Pages;

use App\Filament\Seller\Resources\Nomenclatures\NomenclatureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNomenclatures extends ListRecords
{
    protected static string $resource = NomenclatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
