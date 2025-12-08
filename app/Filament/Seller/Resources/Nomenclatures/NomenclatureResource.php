<?php

namespace App\Filament\Seller\Resources\Nomenclatures;

use App\Filament\Seller\Resources\Nomenclatures\Pages\CreateNomenclature;
use App\Filament\Seller\Resources\Nomenclatures\Pages\EditNomenclature;
use App\Filament\Seller\Resources\Nomenclatures\Pages\ListNomenclatures;
use App\Filament\Seller\Resources\Nomenclatures\Schemas\NomenclatureForm;
use App\Filament\Seller\Resources\Nomenclatures\Tables\NomenclaturesTable;
use App\Models\Nomenclature;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NomenclatureResource extends Resource
{
    protected static ?string $model = Nomenclature::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 1;

    protected static bool $isScopedToTenant = false;

    public static function form(Schema $schema): Schema
    {
        return NomenclatureForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NomenclaturesTable::configure($table);
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
            'index' => ListNomenclatures::route('/'),
            'create' => CreateNomenclature::route('/create'),
            'edit' => EditNomenclature::route('/{record}/edit'),
        ];
    }
}
