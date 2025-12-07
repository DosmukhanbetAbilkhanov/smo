<?php

namespace App\Filament\Resources\Specs;

use App\Filament\Resources\Specs\Pages\CreateSpec;
use App\Filament\Resources\Specs\Pages\EditSpec;
use App\Filament\Resources\Specs\Pages\ListSpecs;
use App\Filament\Resources\Specs\Schemas\SpecForm;
use App\Filament\Resources\Specs\Tables\SpecsTable;
use App\Models\Spec;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SpecResource extends Resource
{
    protected static ?string $model = Spec::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SpecForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpecsTable::configure($table);
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
            'index' => ListSpecs::route('/'),
            'create' => CreateSpec::route('/create'),
            'edit' => EditSpec::route('/{record}/edit'),
        ];
    }
}
