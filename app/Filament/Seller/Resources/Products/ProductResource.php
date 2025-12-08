<?php

namespace App\Filament\Seller\Resources\Products;

use App\Filament\Seller\Resources\Products\Pages\CreateProduct;
use App\Filament\Seller\Resources\Products\Pages\EditProduct;
use App\Filament\Seller\Resources\Products\Pages\ListProducts;
use App\Filament\Seller\Resources\Products\Pages\ViewProduct;
use App\Filament\Seller\Resources\Products\Schemas\ProductForm;
use App\Filament\Seller\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): Builder
    {
        $company = Filament::getTenant();

        return parent::getEloquentQuery()
            ->whereHas('shop', function (Builder $query) use ($company) {
                $query->where('company_id', $company->id);
            });
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
