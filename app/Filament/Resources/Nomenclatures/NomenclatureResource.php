<?php

namespace App\Filament\Resources\Nomenclatures;

use App\Filament\Resources\Nomenclatures\Pages\EditNomenclature;
use App\Filament\Resources\Nomenclatures\Pages\ListNomenclatures;
use App\Filament\Resources\Nomenclatures\Schemas\NomenclatureForm;
use App\Filament\Resources\Nomenclatures\Tables\NomenclaturesTable;
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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 1;

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
            'edit' => EditNomenclature::route('/{record}/edit'),
        ];
    }
}
