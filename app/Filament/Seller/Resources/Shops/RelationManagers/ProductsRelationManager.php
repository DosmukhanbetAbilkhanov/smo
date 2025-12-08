<?php

namespace App\Filament\Seller\Resources\Shops\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('nomenclature.name_ru')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('KZT')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Stock')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Products will be managed via the Product resource
            ])
            ->recordActions([
                // Products will be managed via the Product resource
            ])
            ->toolbarActions([
                // Products will be managed via the Product resource
            ]);
    }
}
