<?php

namespace App\Filament\Seller\Resources\Shops;

use App\Filament\Seller\Resources\Shops\Pages\CreateShop;
use App\Filament\Seller\Resources\Shops\Pages\EditShop;
use App\Filament\Seller\Resources\Shops\Pages\ListShops;
use App\Filament\Seller\Resources\Shops\RelationManagers\ProductsRelationManager;
use App\Filament\Seller\Resources\Shops\Schemas\ShopForm;
use App\Filament\Seller\Resources\Shops\Tables\ShopsTable;
use App\Models\Shop;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ShopResource extends Resource
{
    protected static ?string $model = Shop::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|UnitEnum|null $navigationGroup = 'Shop Management';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $company = Filament::getTenant();

        return parent::getEloquentQuery()->where('company_id', $company->id);
    }

    public static function form(Schema $schema): Schema
    {
        return ShopForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShopsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShops::route('/'),
            'create' => CreateShop::route('/create'),
            'edit' => EditShop::route('/{record}/edit'),
        ];
    }
}
