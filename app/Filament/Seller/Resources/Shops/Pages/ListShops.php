<?php

namespace App\Filament\Seller\Resources\Shops\Pages;

use App\Filament\Seller\Resources\Shops\ShopResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShops extends ListRecords
{
    protected static string $resource = ShopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
